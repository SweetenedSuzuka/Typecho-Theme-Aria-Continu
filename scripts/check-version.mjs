import { readFileSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const repoRoot = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const packageJsonPath = path.join(repoRoot, 'package.json');
const versionJsonPath = path.join(repoRoot, 'version.json');
const constantsPhpPath = path.join(repoRoot, 'inc', 'constants.php');

function readJsonVersion(filePath, label) {
  try {
    const parsed = JSON.parse(readFileSync(filePath, 'utf8'));
    const version = typeof parsed.version === 'string' ? parsed.version.trim() : '';

    if (!version) {
      throw new Error('缺少 version 字段');
    }

    return version;
  } catch (error) {
    console.error(`${label} 版本读取失败：${path.relative(repoRoot, filePath)}`);
    console.error(error instanceof Error ? error.message : String(error));
    process.exit(1);
  }
}

function readPhpConstantVersion(filePath) {
  const content = readFileSync(filePath, 'utf8');
  const match = content.match(/define\('ARIA_VERSION',\s*'([^']+)'\);/);

  if (!match || !match[1].trim()) {
    console.error(`PHP 常量版本读取失败：${path.relative(repoRoot, filePath)}`);
    process.exit(1);
  }

  return match[1].trim();
}

const versions = {
  'package.json': readJsonVersion(packageJsonPath, 'package.json'),
  'version.json': readJsonVersion(versionJsonPath, 'version.json'),
  'inc/constants.php': readPhpConstantVersion(constantsPhpPath)
};

const uniqueVersions = Array.from(new Set(Object.values(versions)));

if (uniqueVersions.length !== 1) {
  console.error('版本号不一致：');
  for (const [label, version] of Object.entries(versions)) {
    console.error(`- ${label}: ${version}`);
  }
  process.exit(1);
}

console.log(`版本一致性检查通过：${uniqueVersions[0]}`);
