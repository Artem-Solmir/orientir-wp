import { describe, expect, it } from "vitest";
import { isPhotoUrl, submissionSchema } from "./validation";

const valid = {
  name: " Анна ",
  contact: "anna@example.com",
  title: "Поход по Припяти",
  message: "Прошли 40 км за два дня.",
  photosUrl: "https://disk.yandex.ru/d/abc123",
};

describe("submissionSchema", () => {
  it("trims fields and keeps a valid photo link", () => {
    const parsed = submissionSchema.parse(valid);
    expect(parsed.name).toBe("Анна");
    expect(parsed.photosUrl).toBe(valid.photosUrl);
  });

  it("treats an empty photo link as absent", () => {
    expect(submissionSchema.parse({ ...valid, photosUrl: "  " }).photosUrl).toBeNull();
  });

  it("rejects links to other hosts", () => {
    expect(submissionSchema.safeParse({ ...valid, photosUrl: "https://evil.example/x" }).success).toBe(false);
  });

  it("reports readable messages", () => {
    const r = submissionSchema.safeParse({ ...valid, name: "A" });
    expect(r.success ? "" : r.error.issues[0]?.message).toBe("Укажите имя");
  });

  it("enforces length bounds", () => {
    expect(submissionSchema.safeParse({ ...valid, name: "Aн" }).success).toBe(true);
    expect(submissionSchema.safeParse({ ...valid, message: "a".repeat(10_000) }).success).toBe(true);
    expect(submissionSchema.safeParse({ ...valid, message: "a".repeat(10_001) }).success).toBe(false);
    expect(submissionSchema.safeParse({ ...valid, message: "short" }).success).toBe(false);
    expect(submissionSchema.safeParse({ ...valid, title: "ab" }).success).toBe(false);
    expect(submissionSchema.safeParse({ ...valid, contact: "ab" }).success).toBe(false);
  });
});

describe("isPhotoUrl", () => {
  it.each([
    ["https://disk.yandex.ru/d/x", true],
    ["https://disk.yandex.by/d/x", true],
    ["https://yadi.sk/d/x", true],
    ["http://disk.yandex.ru/d/x", false],
    ["https://user:pw@disk.yandex.ru/d/x", false],
    ["https://disk.yandex.ru.evil.com/d/x", false],
    ["javascript:alert(1)", false],
    ["not a url", false],
  ])("%s -> %s", (url, ok) => {
    expect(isPhotoUrl(url)).toBe(ok);
  });
});
