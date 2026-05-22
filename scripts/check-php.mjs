import { readdirSync, statSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { spawnSync } from 'node:child_process';

const repoRoot = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const phpCandidates = [
  process.env.ARIA_PHP_BIN,
  path.join(repoRoot, '.tools', 'php-8.3.30-nts', 'php.exe'),
  'php'
].filter(Boolean);

const scanTargets = [
  'admin',
  'components',
  'inc',
  'lib'
];

const rootPhpFiles = readdirSync(repoRoot)
  .filter((entry) => entry.toLowerCase().endsWith('.php'))
  .map((entry) => path.join(repoRoot, entry));

function walkPhpFiles(directory) {
  const results = [];
  for (const entry of readdirSync(directory)) {
    const fullPath = path.join(directory, entry);
    const stats = statSync(fullPath);
    if (stats.isDirectory()) {
      results.push(...walkPhpFiles(fullPath));
      continue;
    }

    if (entry.toLowerCase().endsWith('.php')) {
      results.push(fullPath);
    }
  }

  return results;
}

function resolvePhpCommand() {
  for (const candidate of phpCandidates) {
    const result = spawnSync(candidate, ['-v'], {
      cwd: repoRoot,
      encoding: 'utf8',
      shell: false
    });

    if (result.status === 0) {
      return candidate;
    }
  }

  console.error('未找到可用的 PHP 可执行文件。可通过环境变量 ARIA_PHP_BIN 指定项目内 PHP 路径。');
  process.exit(1);
}

const phpCommand = resolvePhpCommand();
const phpFiles = [
  ...rootPhpFiles,
  ...scanTargets.flatMap((target) => walkPhpFiles(path.join(repoRoot, target)))
].sort((left, right) => left.localeCompare(right));

for (const filePath of phpFiles) {
  const relativePath = path.relative(repoRoot, filePath);
  const result = spawnSync(phpCommand, ['-l', filePath], {
    cwd: repoRoot,
    encoding: 'utf8',
    shell: false
  });

  if (result.status !== 0) {
    process.stdout.write(result.stdout || '');
    process.stderr.write(result.stderr || '');
    console.error(`PHP 语法检查失败：${relativePath}`);
    process.exit(result.status || 1);
  }
}

console.log(`PHP 语法检查通过：${phpFiles.length} 个文件`);
