const fs = require("fs");
const path = require("path");

const root = path.join(__dirname, "..");
const arPath = path.join(root, "resources/lang/ar.json");
const ar = JSON.parse(fs.readFileSync(arPath, "utf8"));
const arKeys = new Set(Object.keys(ar));

function walk(dir, acc) {
  if (!fs.existsSync(dir)) return;
  for (const f of fs.readdirSync(dir, { withFileTypes: true })) {
    if (f.name === "vendor" || f.name === "node_modules") continue;
    const p = path.join(dir, f.name);
    if (f.isDirectory()) walk(p, acc);
    else if (/\.(php|blade\.php)$/i.test(f.name)) acc.push(p);
  }
}

const files = [];
walk(path.join(root, "resources/views"), files);
walk(path.join(root, "app"), files);

const found = new Set();

function addStringLiteral(raw) {
  const k = raw.replace(/\\(.)/g, "$1");
  if (!k || k.length > 500) return;
  if (k.startsWith("$")) return;
  if (k.includes("{{")) return;
  if (/^[a-z][a-z0-9_.]*$/i.test(k) && k.includes(".")) return;
  found.add(k);
}

const quoteRes = [
  /(?:@lang|__|@choice|trans)\(\s*'((?:\\.|[^'\\])*)'/gs,
  /(?:@lang|__|@choice|trans)\(\s*"((?:\\.|[^"\\])*)"/gs,
];

for (const file of files) {
  const c = fs.readFileSync(file, "utf8");
  for (const re of quoteRes) {
    re.lastIndex = 0;
    let m;
    while ((m = re.exec(c))) addStringLiteral(m[1]);
  }
}

const missing = [...found].filter((k) => !arKeys.has(k)).sort();

const outPath = path.join(__dirname, "missing-lang-keys.json");
fs.writeFileSync(
  outPath,
  JSON.stringify({ arCount: arKeys.size, foundCount: found.size, missingCount: missing.length, missing }, null, 2),
  "utf8"
);
console.log("Wrote", outPath);
console.log("missingCount:", missing.length);
