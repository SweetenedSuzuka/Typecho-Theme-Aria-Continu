import { readdirSync, statSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { spawnSync } from 'node:child_process';

const repoRoot = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const jsTargets = [
  path.join(repoRoot, 'assets', 'js', 'main.js'),
  path.join(repoRoot, 'assets', 'js', 'modules')
];

function walkJsFiles(directory) {
  const results = [];
  for (const entry of readdirSync(directory)) {
    const fullPath = path.join(directory, entry);
    const stats = statSync(fullPath);
    if (stats.isDirectory()) {
      results.push(...walkJsFiles(fullPath));
      continue;
    }

    if (entry.toLowerCase().endsWith('.js')) {
      results.push(fullPath);
    }
  }

  return results;
}

const jsFiles = jsTargets.flatMap((target) => {
  const stats = statSync(target);
  return stats.isDirectory() ? walkJsFiles(target) : [target];
}).sort((left, right) => left.localeCompare(right));

for (const filePath of jsFiles) {
  const relativePath = path.relative(repoRoot, filePath);
  const result = spawnSync(process.execPath, ['--check', filePath], {
    cwd: repoRoot,
    encoding: 'utf8',
    shell: false
  });

  if (result.status !== 0) {
    process.stdout.write(result.stdout || '');
    process.stderr.write(result.stderr || '');
    console.error(`JavaScript 语法检查失败：${relativePath}`);
    process.exit(result.status || 1);
  }
}

console.log(`JavaScript 语法检查通过：${jsFiles.length} 个文件`);
