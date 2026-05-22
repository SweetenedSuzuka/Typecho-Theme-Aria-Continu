module.exports = {
  root: true,
  env: {
    browser: true,
    es2021: true
  },
  parserOptions: {
    ecmaVersion: 2021,
    sourceType: 'script'
  },
  globals: {
    THEME_CONFIG: 'readonly',
    $: 'readonly',
    jQuery: 'readonly',
    hljs: 'readonly'
  },
  ignorePatterns: [
    'assets/js/**/*.min.js',
    'assets/js/main.restored.js'
  ],
  rules: {
    'no-undef': 'error',
    'no-unused-vars': [
      'error',
      {
        args: 'none',
        caughtErrors: 'none',
        ignoreRestSiblings: true
      }
    ],
    'no-redeclare': 'error',
    'no-unreachable': 'error',
    'valid-typeof': 'error',
    'no-console': 'off'
  }
};
