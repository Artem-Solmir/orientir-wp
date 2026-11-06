import { defineConfig, globalIgnores } from "eslint/config";
import nextVitals from "eslint-config-next/core-web-vitals";
import nextTs from "eslint-config-next/typescript";

const eslintConfig = defineConfig([
  ...nextVitals,
  ...nextTs,
  {
    rules: {
      // ub-mode "crapper" substitute: keep functions small and simple.
      complexity: ["error", 10],
      "max-depth": ["error", 3],
      "no-console": ["error", { allow: ["warn", "error"] }],
    },
  },
  {
    // CLI scripts legitimately print results.
    files: ["scripts/**"],
    rules: { "no-console": "off" },
  },
  globalIgnores([".next/**", "out/**", "build/**", "next-env.d.ts"]),
]);

export default eslintConfig;
