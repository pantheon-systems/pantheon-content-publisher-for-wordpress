export function toKebabCase(s) {
  return s
    .trim()
    .replace(/[^\w\s]/gi, "-")
    .replace(/[\s_]+|[^\w\s]+/g, "-")
    .replace(/^-|-$/g, "")
    .replace(/-{2,}/g, "-") // Replace repeating hyphens with a single hyphen
    .toLowerCase();
}
