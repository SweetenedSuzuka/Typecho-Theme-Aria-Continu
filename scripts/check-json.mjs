import { readFileSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const repoRoot = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const jsonTargets = [
  path.join(repoRoot, 'package.json'),
  path.join(repoRoot, 'version.json')
];

for (const filePath of jsonTargets) {
  const fileName = path.basename(filePath);

  try {
    JSON.parse(readFileSync(filePath, 'utf8'));
  } catch (error) {
    console.error(`JSON 解析失败：${fileName}`);
    console.error(error instanceof Error ? error.message : String(error));
    process.exit(1);
  }
}

console.log(`JSON 语法检查通过：${jsonTargets.length} 个文件`);
