import sharp from "sharp";
import { describe, expect, it } from "vitest";
import { isDownloadUrl, parseShareUrl, PhotoError, processImage, safeBaseName } from "./photos";

describe("parseShareUrl", () => {
  it("accepts public Yandex Disk folder and file links", () => {
    expect(parseShareUrl("https://disk.yandex.kz/d/vHTxVAK3OmzUpg")).toBe("https://disk.yandex.kz/d/vHTxVAK3OmzUpg");
    expect(parseShareUrl("https://yadi.sk/i/3OSCPKicxyCKEA")).toBe("https://yadi.sk/i/3OSCPKicxyCKEA");
  });

  it.each([
    "http://disk.yandex.ru/d/abc",
    "https://evil.example/d/abc",
    "https://disk.yandex.ru.evil.example/d/abc",
    "https://disk.yandex.kz/client/disk?idApp=client",
    "file:///etc/passwd",
    "not a url",
  ])("rejects %s", (url) => {
    expect(() => parseShareUrl(url)).toThrow(PhotoError);
  });
});

describe("isDownloadUrl", () => {
  it("allows only Yandex download hosts over https", () => {
    expect(isDownloadUrl("https://downloader.disk.yandex.ru/disk/abc")).toBe(true);
    expect(isDownloadUrl("https://evil.example/yandex.net")).toBe(false);
    expect(isDownloadUrl("http://downloader.disk.yandex.ru/x")).toBe(false);
  });
});

describe("safeBaseName", () => {
  it("transliterates and never keeps separators", () => {
    expect(safeBaseName("Поход ../../Птичь.JPG", "abcdef0123456789")).toBe("poxod-ptich-abcdef01");
    expect(safeBaseName("???.png", "abcdef0123456789")).toBe("photo-abcdef01");
  });
});

describe("processImage", () => {
  it("re-encodes to JPEG, strips EXIF and makes thumbnails", async () => {
    const source = await sharp({ create: { width: 1600, height: 1200, channels: 3, background: "#468" } })
      .withExif({ IFD0: { Copyright: "secret" } })
      .jpeg()
      .toBuffer();
    const out = await processImage(source, "x.jpg");
    expect(out.variants.map((v) => v.meta.width)).toEqual([400, 800]);
    expect((await sharp(out.original).metadata()).exif).toBeUndefined();
  });

  it("rejects bytes that are not a picture, whatever the name", async () => {
    await expect(processImage(Buffer.from("<?php echo 1;"), "evil.jpg")).rejects.toThrow(PhotoError);
  });
});
