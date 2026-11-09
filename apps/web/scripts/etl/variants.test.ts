import { describe, expect, it } from "vitest";
import { imageVariants } from "./php";

const meta =
  'a:5:{s:5:"width";i:480;s:6:"height";i:166;s:4:"file";s:6:"x.png";s:5:"sizes";a:3:{' +
  's:9:"thumbnail";a:4:{s:4:"file";s:11:"x-150x150.p";s:5:"width";i:150;s:6:"height";i:150;s:9:"mime-type";s:9:"image/png";}' +
  's:6:"medium";a:4:{s:4:"file";s:11:"x-300x104.p";s:5:"width";i:300;s:6:"height";i:104;s:9:"mime-type";s:9:"image/png";}' +
  's:5:"same1";a:4:{s:4:"file";s:11:"x-300x104.p";s:5:"width";i:300;s:6:"height";i:104;s:9:"mime-type";s:9:"image/png";}}' +
  's:10:"image_meta";a:1:{s:4:"file";s:5:"zz.jp";s:5:"width";i:9;s:6:"height";i:9;}}';

describe("imageVariants", () => {
  it("reads the sizes section, de-duplicates files and ignores image_meta", () => {
    expect(imageVariants(meta)).toEqual([
      { file: "x-150x150.p", width: 150, height: 150 },
      { file: "x-300x104.p", width: 300, height: 104 },
    ]);
  });

  it("returns nothing for empty or size-less metadata", () => {
    expect(imageVariants(null)).toEqual([]);
    expect(imageVariants("a:1:{s:5:\"width\";i:1;}")).toEqual([]);
  });

  it("rejects file names that look like paths", () => {
    const evil = 's:5:"sizes";a:1:{s:1:"a";a:3:{s:4:"file";s:9:"../../etc";s:5:"width";i:1;s:6:"height";i:1;}}';
    expect(imageVariants(evil)).toEqual([]);
    const hidden = 's:5:"sizes";a:1:{s:1:"a";a:3:{s:4:"file";s:4:".env";s:5:"width";i:1;s:6:"height";i:1;}}';
    expect(imageVariants(hidden)).toEqual([]);
  });
});