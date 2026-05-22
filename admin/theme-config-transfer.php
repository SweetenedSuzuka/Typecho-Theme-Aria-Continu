<?php
// Theme config import/export helpers.

/**
 * 获取主题配置导入导出字段元数据
 *
 * @return array<string, array<string, mixed>>
 */
function ariaGetThemeConfigTransferSchema()
{
    return array(
        'avatarUrl' => array('label' => '站点头像', 'type' => 'text', 'default' => ''),
        'backgroundUrl' => array('label' => '首页背景图片', 'type' => 'textarea', 'default' => ''),
        'customPageBackgroundEnabled' => array('label' => '启用网页背景自定义', 'type' => 'checkbox', 'default' => false),
        'customPageBackgroundUrl' => array('label' => '网页背景图地址', 'type' => 'text', 'default' => '/assets/img/background.webp'),
        'customCommentBoxBackgroundEnabled' => array('label' => '启用评论框背景自定义', 'type' => 'checkbox', 'default' => false),
        'customCommentBoxBackgroundUrl' => array('label' => '评论框背景图地址', 'type' => 'text', 'default' => ''),
        'heroSubtitle' => array('label' => '首页副标题', 'type' => 'text', 'default' => '越过喧嚣找到你'),
        'searchPlaceholder' => array('label' => '搜索框 placeholder', 'type' => 'text', 'default' => '要看书架吗？请吧'),
        'defaultThumbnail' => array('label' => '默认文章缩略图', 'type' => 'textarea', 'default' => ''),
        'notFoundBackgroundUrl' => array('label' => '404 背景图 URL', 'type' => 'text', 'default' => ''),
        'navConfig' => array('label' => '导航栏配置', 'type' => 'json_array_strict', 'default' => ThemeOptions::getDefaultNavConfigExample()),
        'iconPacks' => array('label' => '附加图标包', 'type' => 'checkbox_multi', 'default' => array()),
        'enableNavHeadroom' => array('label' => '启用导航栏吸顶隐藏', 'type' => 'checkbox', 'default' => true),
        'homeExcludeCategoriesEnabled' => array('label' => '启用首页分类排除', 'type' => 'checkbox', 'default' => true),
        'homeExcludeCategories' => array('label' => '首页排除分类（slug）', 'type' => 'textarea', 'default' => ''),
        'rewardConfig' => array('label' => '打赏功能配置', 'type' => 'json_object_legacy', 'default' => ''),
        'showQRCode' => array('label' => '文章底部显示本文链接二维码', 'type' => 'checkbox', 'default' => false),
        'enableFancybox' => array('label' => '文章/评论图片启用 Fancybox', 'type' => 'checkbox', 'default' => false),
        'enableLazyload' => array('label' => '开启图片懒加载', 'type' => 'checkbox', 'default' => false),
        'lazyloadPlaceholderEnabled' => array('label' => '懒加载完成前显示占位图', 'type' => 'checkbox', 'default' => false),
        'enableMathJax' => array('label' => '启用 MathJax', 'type' => 'checkbox', 'default' => false),
        'enableMathJaxInComments' => array('label' => '评论区启用 MathJax 解析', 'type' => 'checkbox', 'default' => false),
        'MathJaxConfig' => array(
            'label' => 'MathJax配置信息',
            'type' => 'textarea',
            'default' => "MathJax = MathJax || {};\n"
                . "MathJax.tex = MathJax.tex || {};\n"
                . "MathJax.tex.inlineMath = [['$', '$'], ['\\\\(', '\\\\)']];\n"
                . "MathJax.tex.displayMath = [['$$', '$$'], ['\\\\[', '\\\\]']];\n"
                . "MathJax.tex.processEscapes = true;",
        ),
        'showHitokoto' => array('label' => '显示一言', 'type' => 'checkbox', 'default' => false),
        'hitokotoOrigin' => array('label' => '自定义「一言」接口地址', 'type' => 'text', 'default' => ''),
        'placeholder' => array('label' => '评论框placeholder', 'type' => 'text', 'default' => ''),
        'commentWaitingText' => array('label' => '评论审核提示文案', 'type' => 'text', 'default' => '正在思考这条评论和不和谐.jpg（评论正在等待审核）'),
        'commentClosedText' => array('label' => '评论关闭提示文案', 'type' => 'text', 'default' => '评论关闭了哟'),
        'OwOJson' => array('label' => 'OwO', 'type' => 'text', 'default' => ''),
        'gravatarPrefix' => array('label' => 'Gravatar头像源', 'type' => 'text', 'default' => ''),
        'enableAjaxComment' => array('label' => '开启 AJAX 评论', 'type' => 'checkbox', 'default' => false),
        'enableCommentToMail' => array('label' => '显示评论邮件通知选项', 'type' => 'checkbox', 'default' => false),
        'showCommentUA' => array('label' => '评论显示 UserAgent', 'type' => 'checkbox', 'default' => false),
        'footerSiteName' => array('label' => '页脚站点名称', 'type' => 'text', 'default' => '网站名称'),
        'footerSiteUrl' => array('label' => '页脚站点链接', 'type' => 'text', 'default' => 'https://example.com/'),
        'footerCreditsMode' => array('label' => '页脚署名模式', 'type' => 'select', 'default' => 'Continuo'),
        'footerCreditsText' => array('label' => '页脚署名文本', 'type' => 'text', 'default' => '用户自定义内容'),
        'footerCreditsLink' => array('label' => '页脚署名链接', 'type' => 'text', 'default' => ''),
        'footerRecordsEnabled' => array('label' => '显示页脚备案信息', 'type' => 'checkbox', 'default' => true),
        'footerRecords' => array(
            'label' => '页脚备案信息',
            'type' => 'json_array_strict',
            'default' => ThemeOptions::getDefaultFooterRecordsExample(),
        ),
        'footerWidget' => array('label' => '底部额外链接组件', 'type' => 'json_array_strict', 'default' => ''),
        'cpr' => array('label' => 'Copyright年份', 'type' => 'text', 'default' => '2022-{Y}'),
        'notFoundTitle' => array('label' => '404 标题文案', 'type' => 'text', 'default' => '404:没有找到界面呢，是书架摆错了吗？'),
        'notFoundDescription' => array('label' => '404 描述文案', 'type' => 'textarea', 'default' => '这个页面不存在或者被删除，你可以尝试搜索你想要的内容。'),
        'statistics' => array('label' => '统计代码', 'type' => 'textarea', 'default' => ''),
        'customHeader' => array('label' => '顶部自定义内容', 'type' => 'textarea', 'default' => ''),
        'customFooter' => array('label' => '底部自定义内容', 'type' => 'textarea', 'default' => ''),
        'customScript' => array('label' => '自定义JS', 'type' => 'textarea', 'default' => ''),
    );
}

/**
 * 输出导入导出按钮
 *
 * @return void
 */
function ariaRenderThemeConfigTransferButtons()
{
    echo '<button type="button" id="aria-theme-config-export" class="btn">导出配置</button>'
        . '<button type="button" id="aria-theme-config-import-trigger" class="btn">导入配置</button>'
        . '<input type="file" id="aria-theme-config-import-file" accept=".json,application/json" style="display:none">'
        . '<span id="aria-theme-config-transfer-status" style="display:none"></span>';
}

/**
 * 输出导入导出脚本
 *
 * @param array<string, array<string, mixed>> $schema
 *
 * @return void
 */
function ariaRenderThemeConfigTransferScript(array $schema)
{
    $payload = json_encode(
        array(
            'theme' => 'Aria Continuo',
            'version' => ARIA_VERSION,
            'exportSchemaVersion' => 1,
            'fields' => $schema,
        ),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

    if (!is_string($payload)) {
        return;
    }
    ?>
    <script>
        window.ARIA_THEME_CONFIG_TRANSFER = <?php echo $payload; ?>;
    </script>
    <script>
        window.addEventListener('load', function () {
            var transferMeta = window.ARIA_THEME_CONFIG_TRANSFER;
            if (!transferMeta || !transferMeta.fields) {
                return;
            }

            function findThemeConfigForm() {
                var sampleField = document.querySelector('[name="heroSubtitle"]')
                    || document.querySelector('[name="searchPlaceholder"]');
                return sampleField ? sampleField.closest('form') : document.querySelector('form');
            }

            var form = findThemeConfigForm();
            var exportButton = document.getElementById('aria-theme-config-export');
            var importTriggerButton = document.getElementById('aria-theme-config-import-trigger');
            var importFileInput = document.getElementById('aria-theme-config-import-file');
            var statusNode = document.getElementById('aria-theme-config-transfer-status');

            if (!form || !exportButton || !importTriggerButton || !importFileInput || !statusNode) {
                return;
            }

            function setStatus(message, isError) {
                statusNode.textContent = message || '';
                statusNode.style.color = isError ? '#b42318' : '#666';
            }

            function normalizeLineEndings(value) {
                return String(value == null ? '' : value).replace(/\r\n/g, '\n');
            }

            function sanitizeJsonLike(raw) {
                return normalizeLineEndings(raw).replace(/,\s*([\]}])/g, '$1').trim();
            }

            function isPlainObject(value) {
                return Object.prototype.toString.call(value) === '[object Object]';
            }

            function deepSort(value) {
                if (Array.isArray(value)) {
                    return value.map(deepSort);
                }

                if (!isPlainObject(value)) {
                    return value;
                }

                var sorted = {};
                Object.keys(value).sort().forEach(function (key) {
                    sorted[key] = deepSort(value[key]);
                });
                return sorted;
            }

            function valuesEqual(left, right) {
                return JSON.stringify(deepSort(left)) === JSON.stringify(deepSort(right));
            }

            function parseStrictJsonArray(raw, label) {
                var sanitized = sanitizeJsonLike(raw);
                if (sanitized === '') {
                    return '';
                }

                var parsed;
                try {
                    parsed = JSON.parse(sanitized);
                } catch (error) {
                    throw new Error(label + ' 不是合法的 JSON 数组');
                }

                if (!Array.isArray(parsed)) {
                    throw new Error(label + ' 必须是 JSON 数组');
                }

                return parsed;
            }

            function parseLegacyJsonArray(raw, label) {
                var sanitized = sanitizeJsonLike(raw);
                if (sanitized === '') {
                    return '';
                }

                try {
                    var direct = JSON.parse(sanitized);
                    if (Array.isArray(direct)) {
                        return direct;
                    }
                } catch (error) {
                }

                try {
                    var wrapped = JSON.parse('[' + sanitized + ']');
                    if (Array.isArray(wrapped)) {
                        return wrapped;
                    }
                } catch (error) {
                }

                throw new Error(label + ' 不是合法的数组配置');
            }

            function parseLegacyJsonObject(raw, label) {
                var sanitized = sanitizeJsonLike(raw);
                if (sanitized === '') {
                    return '';
                }

                try {
                    var direct = JSON.parse(sanitized);
                    if (isPlainObject(direct)) {
                        return direct;
                    }
                } catch (error) {
                }

                try {
                    var wrapped = JSON.parse('{' + sanitized + '}');
                    if (isPlainObject(wrapped)) {
                        return wrapped;
                    }
                } catch (error) {
                }

                throw new Error(label + ' 不是合法的对象配置');
            }

            function getFieldNodes(name) {
                return Array.prototype.slice.call(
                    form.querySelectorAll('[name="' + name + '"], [name="' + name + '[]"]')
                );
            }

            function normalizeSchemaValue(meta, rawValue) {
                switch (meta.type) {
                    case 'checkbox':
                        return !!rawValue;
                    case 'checkbox_multi':
                        return Array.isArray(rawValue)
                            ? rawValue.slice().map(String).sort()
                            : [];
                    case 'json_array_strict':
                        if (Array.isArray(rawValue)) {
                            return rawValue;
                        }
                        return parseStrictJsonArray(rawValue, meta.label);
                    case 'json_array_legacy':
                        if (Array.isArray(rawValue)) {
                            return rawValue;
                        }
                        return parseLegacyJsonArray(rawValue, meta.label);
                    case 'json_object_legacy':
                        if (isPlainObject(rawValue)) {
                            return rawValue;
                        }
                        return parseLegacyJsonObject(rawValue, meta.label);
                    case 'select':
                    case 'text':
                    case 'textarea':
                    default:
                        return normalizeLineEndings(rawValue);
                }
            }

            function getFormValue(name, meta) {
                var nodes = getFieldNodes(name);
                if (!nodes.length) {
                    return normalizeSchemaValue(meta, meta.default);
                }

                if (meta.type === 'checkbox') {
                    return !!nodes[0].checked;
                }

                if (meta.type === 'checkbox_multi') {
                    return normalizeSchemaValue(meta, nodes.filter(function (node) {
                        return node.checked;
                    }).map(function (node) {
                        return String(node.value);
                    }));
                }

                return normalizeSchemaValue(meta, nodes[0].value);
            }

            function formatValueForField(meta, value) {
                if (value === '') {
                    return '';
                }

                switch (meta.type) {
                    case 'json_array_strict':
                    case 'json_array_legacy':
                    case 'json_object_legacy':
                        return JSON.stringify(value, null, 4);
                    case 'checkbox':
                        return !!value;
                    case 'checkbox_multi':
                        return Array.isArray(value) ? value.map(String) : [];
                    case 'select':
                    case 'text':
                    case 'textarea':
                    default:
                        return normalizeLineEndings(value);
                }
            }

            function setFieldValue(name, meta, value) {
                var nodes = getFieldNodes(name);
                if (!nodes.length) {
                    return;
                }

                if (meta.type === 'checkbox') {
                    nodes[0].checked = !!value;
                    nodes[0].dispatchEvent(new Event('change', { bubbles: true }));
                    return;
                }

                if (meta.type === 'checkbox_multi') {
                    var selected = Array.isArray(value) ? value.map(String) : [];
                    nodes.forEach(function (node) {
                        node.checked = selected.indexOf(String(node.value)) !== -1;
                        node.dispatchEvent(new Event('change', { bubbles: true }));
                    });
                    return;
                }

                var formattedValue = formatValueForField(meta, value);
                nodes[0].value = formattedValue;
                nodes[0].dispatchEvent(new Event('change', { bubbles: true }));
            }

            function getNormalizedDefaults() {
                var defaults = {};
                Object.keys(transferMeta.fields).forEach(function (name) {
                    defaults[name] = normalizeSchemaValue(transferMeta.fields[name], transferMeta.fields[name].default);
                });
                return defaults;
            }

            function getCurrentNormalizedValues() {
                var values = {};
                Object.keys(transferMeta.fields).forEach(function (name) {
                    values[name] = getFormValue(name, transferMeta.fields[name]);
                });
                return values;
            }

            function buildExportPayload() {
                var defaults = getNormalizedDefaults();
                var current = getCurrentNormalizedValues();
                var config = {};

                Object.keys(transferMeta.fields).forEach(function (name) {
                    if (!valuesEqual(current[name], defaults[name])) {
                        config[name] = current[name];
                    }
                });

                return {
                    theme: transferMeta.theme,
                    version: transferMeta.version,
                    exportSchemaVersion: transferMeta.exportSchemaVersion,
                    exportedAt: new Date().toISOString(),
                    config: config
                };
            }

            function downloadJsonFile(fileName, payload) {
                var blob = new Blob(
                    [JSON.stringify(payload, null, 4)],
                    { type: 'application/json;charset=utf-8' }
                );
                var url = URL.createObjectURL(blob);
                var link = document.createElement('a');
                link.href = url;
                link.download = fileName;
                document.body.appendChild(link);
                link.click();
                link.remove();
                window.setTimeout(function () {
                    URL.revokeObjectURL(url);
                }, 0);
            }

            function extractImportConfig(payload) {
                if (isPlainObject(payload) && payload.theme && payload.theme !== transferMeta.theme) {
                    throw new Error('导入文件不属于当前主题');
                }

                if (isPlainObject(payload) && isPlainObject(payload.config)) {
                    return payload.config;
                }

                if (isPlainObject(payload)) {
                    var hasKnownField = Object.keys(transferMeta.fields).some(function (name) {
                        return Object.prototype.hasOwnProperty.call(payload, name);
                    });

                    if (hasKnownField) {
                        return payload;
                    }
                }

                if (isPlainObject(payload) && Object.prototype.hasOwnProperty.call(payload, 'config')) {
                    throw new Error('导入文件中的 config 字段格式不正确');
                }

                throw new Error('导入文件格式不正确');
            }

            function normalizeImportedConfig(payloadConfig) {
                var normalized = {};

                Object.keys(transferMeta.fields).forEach(function (name) {
                    if (!Object.prototype.hasOwnProperty.call(payloadConfig, name)) {
                        return;
                    }

                    normalized[name] = normalizeSchemaValue(transferMeta.fields[name], payloadConfig[name]);
                });

                return normalized;
            }

            function buildImportTargetValues(importedConfig) {
                var targetValues = getNormalizedDefaults();
                Object.keys(importedConfig).forEach(function (name) {
                    targetValues[name] = importedConfig[name];
                });
                return targetValues;
            }

            function collectOverwriteLabels(currentValues, defaultValues, targetValues) {
                var labels = [];

                Object.keys(transferMeta.fields).forEach(function (name) {
                    if (valuesEqual(currentValues[name], defaultValues[name])) {
                        return;
                    }

                    if (!valuesEqual(currentValues[name], targetValues[name])) {
                        labels.push(transferMeta.fields[name].label);
                    }
                });

                return labels;
            }

            function submitThemeConfigForm() {
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                    return;
                }

                form.submit();
            }

            exportButton.addEventListener('click', function () {
                try {
                    var payload = buildExportPayload();
                    var date = new Date().toISOString().slice(0, 19).replace(/[:T]/g, '-');
                    downloadJsonFile('aria-continuo-theme-config-' + date + '.json', payload);
                    setStatus('主题配置已导出到本地下载文件。');
                } catch (error) {
                    setStatus(error.message || '导出失败，请检查当前配置是否存在无效 JSON。', true);
                }
            });

            importTriggerButton.addEventListener('click', function () {
                importFileInput.click();
            });

            importFileInput.addEventListener('change', function () {
                var file = importFileInput.files && importFileInput.files[0];
                if (!file) {
                    return;
                }

                var reader = new FileReader();
                reader.onload = function () {
                    try {
                        var rawText = normalizeLineEndings(reader.result);
                        var payload = JSON.parse(rawText);
                        var payloadConfig = extractImportConfig(payload);
                        var normalizedImport = normalizeImportedConfig(payloadConfig);
                        var currentValues = getCurrentNormalizedValues();
                        var defaultValues = getNormalizedDefaults();
                        var targetValues = buildImportTargetValues(normalizedImport);
                        var overwriteLabels = collectOverwriteLabels(currentValues, defaultValues, targetValues);

                        if (overwriteLabels.length > 0) {
                            var shouldImport = window.confirm(
                                '导入配置将覆盖以下当前已改动且与导入目标不同的设置：\n- '
                                + overwriteLabels.join('\n- ')
                                + '\n\n未出现在导入文件中的字段会回退到默认值。\n\n是否继续导入并立即保存？'
                            );

                            if (!shouldImport) {
                                setStatus('已取消导入。');
                                importFileInput.value = '';
                                return;
                            }
                        }

                        Object.keys(transferMeta.fields).forEach(function (name) {
                            setFieldValue(name, transferMeta.fields[name], targetValues[name]);
                        });

                        setStatus('已将导入配置写入表单，正在保存主题设置……');
                        importFileInput.value = '';
                        submitThemeConfigForm();
                    } catch (error) {
                        setStatus(error.message || '导入失败，请确认文件内容有效。', true);
                        importFileInput.value = '';
                    }
                };

                reader.onerror = function () {
                    setStatus('导入失败，无法读取所选文件。', true);
                    importFileInput.value = '';
                };

                reader.readAsText(file, 'utf-8');
            });
        });
    </script>
    <?php
}
