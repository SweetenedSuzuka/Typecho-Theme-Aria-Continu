<?php
// Admin UI helpers for the theme configuration page.

function ariaRenderThemeConfigAssets()
{
    echo '<script>var ARIA_VERSION = "' . ARIA_VERSION . '";</script>';
    ?>
    <style>form{position:relative;max-width:100%}form input:not([type]),form input[type="date"],form input[type="datetime-local"],form input[type="email"],form input[type="number"],form input[type="password"],form input[type="search"],form input[type="tel"],form input[type="time"],form input[type="text"],form input[type="file"],form input[type="url"]{font-family:'Lato','Helvetica Neue',Arial,Helvetica,sans-serif;margin:0em;outline:none;-webkit-appearance:none;tap-highlight-color:rgba(255,255,255,0);line-height:1.21428571em;padding:0.67857143em 1em;font-size:1em;background:#FFFFFF;border:1px solid rgba(34,36,38,0.15);color:rgba(0,0,0,0.87);border-radius:0.28571429rem;-webkit-box-shadow:0em 0em 0em 0em transparent inset;box-shadow:0em 0em 0em 0em transparent inset;-webkit-transition:color 0.5s ease,border-color 0.5s ease;transition:color 0.5s ease,border-color 0.5s ease}form textarea{margin:0em;-webkit-appearance:none;tap-highlight-color:rgba(255,255,255,0);padding:0.78571429em 1em;background:#FFFFFF;border:1px solid rgba(34,36,38,0.15);outline:none;color:rgba(0,0,0,0.87);border-radius:0.28571429rem;-webkit-box-shadow:0em 0em 0em 0em transparent inset;box-shadow:0em 0em 0em 0em transparent inset;-webkit-transition:color 0.1s ease,border-color 0.5s ease;transition:color 0.1s ease,border-color 0.5s ease;font-size:1em;line-height:1.2857;resize:vertical}form textarea:not([rows]){height:12em;min-height:8em;max-height:24em}form textarea,form input[type="checkbox"]{vertical-align:top}form textarea:focus,form input:focus{color:rgba(0,0,0,0.95);border-color:#85B7D9;border-radius:0.28571429rem;background:#FFFFFF;-webkit-box-shadow:0px 0em 0em 0em rgba(34,36,38,0.35) inset;box-shadow:0px 0em 0em 0em rgba(34,36,38,0.35) inset;-webkit-appearance:none}.tip{max-width:100%;position:relative;min-height:1em;margin:0 10px;background:#F8F8F9;padding:1em 1.5em;line-height:1.4285em;color:rgba(0,0,0,0.87);-webkit-transition:opacity 0.1s ease,color 0.1s ease,background 0.1s ease,-webkit-box-shadow 0.1s ease;transition:opacity 0.1s ease,color 0.1s ease,background 0.1s ease,-webkit-box-shadow 0.1s ease;transition:opacity 0.1s ease,color 0.1s ease,background 0.1s ease,box-shadow 0.1s ease;transition:opacity 0.1s ease,color 0.1s ease,background 0.1s ease,box-shadow 0.1s ease,-webkit-box-shadow 0.1s ease;border-radius:0.28571429rem;-webkit-box-shadow:0 0 0 1px rgba(34,36,38,.22) inset,0 2px 4px 0 rgba(34,36,38,.12),0 2px 10px 0 rgba(34,36,38,.15);box-shadow:0 0 0 1px rgba(34,36,38,.22) inset,0 2px 4px 0 rgba(34,36,38,.12),0 2px 10px 0 rgba(34,36,38,.15)}.tip-header{text-align:center;margin:10px auto 20px auto;color:#444;text-shadow:0 0 2px #c2c2c2}.current-ver{position:relative;border-color:#b21e1e!important;background-color:#DB2828!important;color:#FFF!important;left:-37px;padding-left:1rem;border-bottom-right-radius:5px;padding-right:1.2em}.current-ver:after{position:absolute;content:'';top:100%;left:0;background-color:transparent!important;border-style:solid;border-width:0 1.2em 1.2em 0;border-color:transparent;border-right-color:inherit;width:0;height:0}.btn.primary{cursor:pointer;display:inline-block;background:#E0E1E2 none;color:rgba(0,0,0,0.6);padding:0 1.5em;border-radius:0.28571429rem;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;outline:none;-webkit-transition:opacity 0.5s ease,background-color 0.5s ease,color 0.5s ease,background 0.5s ease,-webkit-box-shadow 0.5s ease;transition:opacity 0.5s ease,background-color 0.5s ease,color 0.5s ease,background 0.5s ease,-webkit-box-shadow 0.5s ease;transition:opacity 0.5s ease,background-color 0.5s ease,color 0.5s ease,box-shadow 0.5s ease,background 0.5s ease;transition:opacity 0.5s ease,background-color 0.5s ease,color 0.5s ease,box-shadow 0.5s ease,background 0.5s ease,-webkit-box-shadow 0.5s ease;-webkit-tap-highlight-color:transparent}.btn.primary:hover{background-color:#CACBCD;color:rgba(0,0,0,0.8)}.btn.primary[type="submit"]{position:fixed;right:100px;bottom:100px}#check-update{position:relative;display:inline-flex;align-items:center;justify-content:center;min-height:2.4em;line-height:1.2;vertical-align:middle}#check-update i.loading,#check-update i.confirm,#check-update i.alert{position:absolute;left:.6em;top:50%;width:18px;height:18px;display:block;pointer-events:none;transform:translateY(-50%)}.btn.confirm{background-color:#95f798!important}.btn.alert{background-color:#fa9492 !important}i.confirm{position:absolute;left:.5em}i.confirm:after,i.confirm:before{content:"";background:green;display:block;position:absolute;width:3px;border-radius:3px}i.confirm:after{height:6px;transform:rotate(-45deg);top:9px;left:6px}i.confirm:before{height:11px;transform:rotate(45deg);top:5px;left:10px}i.alert{position:absolute;left:.5em}i.alert:after,i.alert:before{content:"";background:red;display:block;position:absolute;width:3px;border-radius:3px;left:9px}i.alert:after{height:3px;top:14px}i.alert:before{height:8px;top:4px}.multiline{position:relative;display:inline-block;-webkit-backface-visibility:hidden;backface-visibility:hidden;outline:none;vertical-align:baseline;font-style:normal;min-height:17px;font-size:1rem;line-height:17px;min-width:17px}.multiline input[type="checkbox"],.multiline input[type="radio"]{cursor:pointer;position:absolute;top:0px;left:0px;opacity:0 !important;outline:none;z-index:3;width:17px;height:17px}.multiline{min-height:1.5rem}.multiline input{width:3.5rem;height:1.5rem}.multiline .box,.multiline label{min-height:1.5rem;padding-left:4.5rem;color:rgba(0,0,0,0.87)}.multiline label{padding-top:0.15em}.multiline .box:before,.multiline label:before{cursor:pointer;display:block;position:absolute;content:'';z-index:1;-webkit-transform:none;transform:none;border:none;top:0rem;background:rgba(0,0,0,0.05);-webkit-box-shadow:none;box-shadow:none;width:3.5rem;height:1rem;border-radius:500rem}.multiline .box:after,.multiline label:after{cursor:pointer;background:#FFFFFF -webkit-gradient(linear,left top,left bottom,from(transparent),to(rgba(0,0,0,0.05)));background:#FFFFFF -webkit-linear-gradient(transparent,rgba(0,0,0,0.05));background:#FFFFFF linear-gradient(transparent,rgba(0,0,0,0.05));position:absolute;content:'' !important;opacity:1;z-index:2;border:none;-webkit-box-shadow:0px 1px 2px 0 rgba(34,36,38,0.15),0px 0px 0px 1px rgba(34,36,38,0.15) inset;box-shadow:0px 1px 2px 0 rgba(34,36,38,0.15),0px 0px 0px 1px rgba(34,36,38,0.15) inset;width:1.2rem;height:1.2rem;top:-.1rem;left:0em;border-radius:500rem;-webkit-transition:background 0.3s ease,left 0.3s ease;transition:background 0.3s ease,left 0.3s ease}.multiline input ~ .box:after,.multiline input ~ label:after{left:-0.05rem;-webkit-box-shadow:0px 1px 2px 0 rgba(34,36,38,0.15),0px 0px 0px 1px rgba(34,36,38,0.15) inset;box-shadow:0px 1px 2px 0 rgba(34,36,38,0.15),0px 0px 0px 1px rgba(34,36,38,0.15) inset}.multiline input:focus ~ .box:before,.multiline input:focus ~ label:before{background-color:rgba(0,0,0,0.15);border:none}.multiline .box:hover::before,.multiline label:hover::before{background-color:rgba(0,0,0,0.15);border:none}.multiline input:checked ~ .box,.multiline input:checked ~ label{color:rgba(0,0,0,0.95) !important}.multiline input:checked ~ .box:before,.multiline input:checked ~ label:before{background-color:#2185D0 !important}.multiline input:checked ~ .box:after,.multiline input:checked ~ label:after{left:2.3rem;-webkit-box-shadow:0px 1px 2px 0 rgba(34,36,38,0.15),0px 0px 0px 1px rgba(34,36,38,0.15) inset;box-shadow:0px 1px 2px 0 rgba(34,36,38,0.15),0px 0px 0px 1px rgba(34,36,38,0.15) inset}.multiline input:focus:checked ~ .box,.multiline input:focus:checked ~ label{color:rgba(0,0,0,0.95) !important}.multiline input:focus:checked ~ .box:before,.multiline input:focus:checked ~ label:before{background-color:#0d71bb !important}
    [id^="typecho-option-item-MathJaxConfig-"]{display:none}
    [id^="typecho-option-item-footerCreditsText-"],[id^="typecho-option-item-footerCreditsLink-"],[id^="typecho-option-item-homeExcludeCategories-"],[id^="typecho-option-item-footerRecords-"],[id^="typecho-option-item-enableMathJaxInComments-"],[id^="typecho-option-item-MathJaxConfig-"],[id^="typecho-option-item-hitokotoOrigin-"],[id^="typecho-option-item-customPageBackgroundUrl-"],[id^="typecho-option-item-customCommentBoxBackgroundUrl-"],[id^="typecho-option-item-lazyloadPlaceholderEnabled-"],[id^="typecho-option-item-themeConfigSchemaVersion-"]{display:none}.aria-config-issues{margin:0 10px 1em;border-left:4px solid #d92d20;background:#fff2f0;color:#7a271a}.aria-config-issues strong{color:#b42318}.aria-config-issues ul{margin:0.5em 0 0 1.2em}.aria-config-issues li{margin:0.25em 0}.typecho-option.aria-field-invalid textarea,.typecho-option.aria-field-invalid input[type="text"]{border-color:#d92d20;box-shadow:0 0 0 1px rgba(217,45,32,0.12)}.typecho-option .aria-field-error,.typecho-option .message.error{color:#b42318;font-weight:600;margin-top:0.5em}</style>
    <script>
        (function () {
            function prependStatusIcon(dom, className) {
                if (!dom) {
                    return;
                }

                var existingIcons = dom.querySelectorAll('i.loading, i.confirm, i.alert');
                existingIcons.forEach(function (icon) {
                    icon.remove();
                });

                var icon = document.createElement('i');
                icon.className = className;
                dom.prepend(icon);
            }

            function setButtonState(dom, className, text) {
                if (!dom) {
                    return;
                }

                dom.classList.remove('loading', 'confirm', 'alert');
                if (className) {
                    dom.classList.add(className);
                }
                dom.style.paddingLeft = className ? '2em' : '';
                dom.textContent = text;
                prependStatusIcon(dom, className);
            }

            window.checkUpdate = function (dom) {
                if (!dom) {
                    return;
                }

                setButtonState(dom, 'loading', '检查中');

                var request = new XMLHttpRequest();
                request.onreadystatechange = function () {
                    if (request.readyState !== 4) {
                        return;
                    }

                    if (request.status !== 200) {
                        dom.classList.remove('loading');
                        dom.style.paddingLeft = '';
                        dom.textContent = '请求失败！错误码：' + request.status;
                        return;
                    }

                    try {
                        var data = JSON.parse(request.responseText);
                        var updateInfo = document.getElementById('update-info');
                        if (data.version == ARIA_VERSION.trim()) {
                            setButtonState(dom, 'confirm', '已经为最新版');
                            if (updateInfo) {
                                updateInfo.remove();
                            }
                            return;
                        }

                        setButtonState(dom, 'alert', '检查到新版本');
                        var html = '<ul><li>新版本：' + data.version + '</li><li>更新日志：<a href="' + data.changeLog + '" target="_blank">changeLog</a></li><li>使用帮助（Aria）：<a href="' + data.wiki + '" target="_blank">Wiki</a></li>';
                        if (data.warning) {
                            html += '<li><strong>更新须知:' + data.warning + '</strong></li>';
                        }
                        html += '</ul>';

                        if (!updateInfo) {
                            updateInfo = document.createElement('div');
                            updateInfo.id = 'update-info';
                            updateInfo.classList.add('tip');
                            Array.prototype.slice.call(document.getElementsByClassName('tip')).pop().after(updateInfo);
                        }

                        updateInfo.innerHTML = html;
                    } catch (error) {
                        dom.classList.remove('loading');
                        dom.style.paddingLeft = '';
                        dom.textContent = '请求失败，请稍后重试！' + error;
                    }
                };

                try {
                    var requestUrl = 'https://raw.githubusercontent.com/SweetenedSuzuka/Typecho-Theme-Aria-Continuo/master/version.json?raw=true&_=' + Date.now();
                    request.open('GET', requestUrl, true);
                    request.send();
                } catch (error) {
                    dom.classList.remove('loading');
                    dom.style.paddingLeft = '';
                    dom.textContent = '请求失败，请稍后重试！' + error;
                }
            };

            window.addEventListener('load', function () {
                var updateButton = document.getElementById('check-update');
                if (!updateButton) {
                    return;
                }

                updateButton.addEventListener('click', function () {
                    window.checkUpdate(updateButton);
                });

                window.checkUpdate(updateButton);
            });
        })();
    </script>
    <script>
        window.addEventListener('load', function () {
            function findConfigForm() {
                return document.querySelector('form[action][method]') || document.querySelector('form');
            }

            function ensureIssuesPanel() {
                var form = findConfigForm();
                if (!form) {
                    return null;
                }

                var panel = document.getElementById('aria-theme-config-issues');
                if (panel) {
                    return panel;
                }

                panel = document.createElement('div');
                panel.id = 'aria-theme-config-issues';
                panel.className = 'tip aria-config-issues';
                panel.style.display = 'none';

                var firstTip = form.querySelector('.tip');
                if (firstTip) {
                    form.insertBefore(panel, firstTip);
                } else {
                    form.insertBefore(panel, form.firstChild);
                }

                return panel;
            }

            function refreshIssuesPanel() {
                var panel = ensureIssuesPanel();
                if (!panel) {
                    return;
                }

                var fieldItems = Array.prototype.slice.call(document.querySelectorAll('ul.typecho-option[id^="typecho-option-item-"]'));
                var issueItems = [];

                fieldItems.forEach(function (item) {
                    item.classList.remove('aria-field-invalid');

                    var storedMessage = item.getAttribute('data-aria-stored-issue') || '';
                    var customErrorNode = item.querySelector('.aria-field-error');
                    var builtInErrorNode = item.querySelector('.message.error');
                    var messageText = '';

                    if (storedMessage) {
                        if (!customErrorNode) {
                            customErrorNode = document.createElement('p');
                            customErrorNode.className = 'aria-field-error';
                            item.appendChild(customErrorNode);
                        }
                        customErrorNode.textContent = storedMessage;
                        messageText = storedMessage;
                    } else if (customErrorNode) {
                        customErrorNode.remove();
                    }

                    if (!messageText && builtInErrorNode) {
                        messageText = builtInErrorNode.textContent.trim();
                    }

                    if (!messageText) {
                        return;
                    }

                    item.classList.add('aria-field-invalid');

                    var labelNode = item.querySelector('.typecho-label');
                    var labelText = labelNode ? labelNode.textContent.trim() : '未命名设置项';
                    issueItems.push('<li><strong>' + labelText + '</strong>：' + messageText + '</li>');
                });

                if (!issueItems.length) {
                    panel.style.display = 'none';
                    panel.innerHTML = '';
                    return;
                }

                panel.innerHTML = '<strong>以下设置存在格式问题，当前不会按这些内容生效：</strong><ul>' + issueItems.join('') + '</ul>';
                panel.style.display = 'block';
            }

            function findFieldItem(name) {
                return document.querySelector('[id^="typecho-option-item-' + name + '-"]');
            }

            function findCheckbox(name) {
                return document.querySelector('input[name="' + name + '[]"]')
                    || document.querySelector('input[name="' + name + '"]')
                    || document.querySelector('input[id^="' + name + '-"]')
                    || document.getElementById(name);
            }

            function findSelect(name) {
                return document.getElementById(name) || document.querySelector('select[name="' + name + '"]');
            }

            function toggle(dom, show) {
                if (dom) {
                    dom.style.display = show ? 'block' : 'none';
                }
            }

            function hasFieldError(dom) {
                return !!(dom && (dom.querySelector('.aria-field-error') || dom.querySelector('.message.error')));
            }

            var mathJaxToggle = findCheckbox('enableMathJax');
            var mathJaxInCommentsItem = findFieldItem('enableMathJaxInComments');
            var mathJaxConfigItem = findFieldItem('MathJaxConfig');
            var hitokotoToggle = findCheckbox('showHitokoto');
            var hitokotoOriginItem = findFieldItem('hitokotoOrigin');
            var creditsMode = findSelect('footerCreditsMode');
            var creditsText = findFieldItem('footerCreditsText');
            var creditsLink = findFieldItem('footerCreditsLink');
            var customPageBackgroundToggle = findCheckbox('customPageBackgroundEnabled');
            var customPageBackgroundUrl = findFieldItem('customPageBackgroundUrl');
            var customCommentBoxBackgroundToggle = findCheckbox('customCommentBoxBackgroundEnabled');
            var customCommentBoxBackgroundUrl = findFieldItem('customCommentBoxBackgroundUrl');
            var homeExcludeToggle = findCheckbox('homeExcludeCategoriesEnabled');
            var homeExcludeCategories = findFieldItem('homeExcludeCategories');
            var footerRecordsToggle = findCheckbox('footerRecordsEnabled');
            var footerRecords = findFieldItem('footerRecords');
            var lazyloadToggle = findCheckbox('enableLazyload');
            var lazyloadPlaceholderItem = findFieldItem('lazyloadPlaceholderEnabled');
            var advancedCustomCodeEnabledToggle = findCheckbox('enableAdvancedCustomCode');
            var advancedCustomCodeItems = Array.prototype.slice.call(
                document.querySelectorAll('ul.typecho-option[data-aria-advanced-custom-code="1"]')
            );

            function syncAdvancedCustomCode() {
                var visible = !!(advancedCustomCodeEnabledToggle && advancedCustomCodeEnabledToggle.checked);
                advancedCustomCodeItems.forEach(function (item) {
                    toggle(item, visible || hasFieldError(item));
                });
            }

            function syncMathJax() {
                var enabled = !!(mathJaxToggle && mathJaxToggle.checked);
                toggle(mathJaxInCommentsItem, enabled);
                toggle(mathJaxConfigItem, enabled);
            }

            function syncHitokoto() {
                var enabled = !!(hitokotoToggle && hitokotoToggle.checked);
                toggle(hitokotoOriginItem, enabled);
            }

            function syncFooterCredits() {
                var showCustom = !!(creditsMode && creditsMode.value === 'custom');
                toggle(creditsText, showCustom);
                toggle(creditsLink, showCustom);
            }

            function syncCustomPageBackground() {
                var enabled = !!(customPageBackgroundToggle && customPageBackgroundToggle.checked);
                toggle(customPageBackgroundUrl, enabled);
            }

            function syncCustomCommentBoxBackground() {
                var enabled = !!(customCommentBoxBackgroundToggle && customCommentBoxBackgroundToggle.checked);
                toggle(customCommentBoxBackgroundUrl, enabled);
            }

            function syncHomeExclude() {
                var enabled = !!(homeExcludeToggle && homeExcludeToggle.checked);
                toggle(homeExcludeCategories, enabled);
            }

            function syncFooterRecords() {
                var enabled = !!(footerRecordsToggle && footerRecordsToggle.checked);
                toggle(footerRecords, enabled || hasFieldError(footerRecords));
            }

            function syncLazyload() {
                var enabled = !!(lazyloadToggle && lazyloadToggle.checked);
                toggle(lazyloadPlaceholderItem, enabled);
            }

            if (mathJaxToggle) {
                mathJaxToggle.addEventListener('change', syncMathJax);
                syncMathJax();
            }

            if (hitokotoToggle) {
                hitokotoToggle.addEventListener('change', syncHitokoto);
                syncHitokoto();
            }

            if (creditsMode) {
                creditsMode.addEventListener('change', syncFooterCredits);
                syncFooterCredits();
            }

            if (customPageBackgroundToggle) {
                customPageBackgroundToggle.addEventListener('change', syncCustomPageBackground);
                syncCustomPageBackground();
            }

            if (customCommentBoxBackgroundToggle) {
                customCommentBoxBackgroundToggle.addEventListener('change', syncCustomCommentBoxBackground);
                syncCustomCommentBoxBackground();
            }

            if (homeExcludeToggle) {
                homeExcludeToggle.addEventListener('change', syncHomeExclude);
                syncHomeExclude();
            }

            if (footerRecordsToggle) {
                footerRecordsToggle.addEventListener('change', syncFooterRecords);
                syncFooterRecords();
            }

            if (lazyloadToggle) {
                lazyloadToggle.addEventListener('change', syncLazyload);
                syncLazyload();
            }

            if (advancedCustomCodeEnabledToggle) {
                advancedCustomCodeEnabledToggle.addEventListener('change', syncAdvancedCustomCode);
                syncAdvancedCustomCode();
            }

            refreshIssuesPanel();
        });
    </script>
    <?php
}

function ariaRenderThemeConfigIssuesPanel(array $issues)
{
    if (empty($issues)) {
        echo '<div id="aria-theme-config-issues" class="tip aria-config-issues" style="display:none"></div>';
        return;
    }

    echo '<div id="aria-theme-config-issues" class="tip aria-config-issues">';
    echo '<strong>以下设置存在格式问题，当前不会按这些内容生效：</strong><ul>';
    foreach ($issues as $issue) {
        $label = htmlspecialchars((string) $issue['label'], ENT_QUOTES, 'UTF-8');
        $message = htmlspecialchars((string) $issue['message'], ENT_QUOTES, 'UTF-8');
        echo '<li><strong>' . $label . '</strong>：' . $message . '</li>';
    }
    echo '</ul></div>';
}

function ariaRenderThemeConfigIntro()
{
    echo '<div class="tip"><span class="current-ver"><strong><code>Ver ' . ARIA_VERSION . '</code></strong></span>
    <div class="tip-header"><h1>Aria-Continuo</h1></div>
    <p>感谢选择使用 <code>Aria</code> / <code>Aria Continuo</code></p>
    <p>帮助手册（Aria）：<a href="https://github.com/Siphils/Typecho-Theme-Aria/wiki" target="_blank">Wiki</a>（Continuo 暂无）</p>
    <p>Aria：<a href="https://github.com/Siphils/Typecho-Theme-Aria/issues" target="_blank">Issues</a> <a href="https://github.com/Siphils/Typecho-Theme-Aria/pulls" target="_blank">PR</a> ｜ Continuo：<a href="https://github.com/SweetenedSuzuka/Typecho-Theme-Aria-Continuo/issues" target="_blank">Issues</a> <a href="https://github.com/SweetenedSuzuka/Typecho-Theme-Aria-Continuo/pulls" target="_blank">PR</a></p>
    <p style="position:absolute;right:5px;bottom:5px;display:flex;gap:0.5em;align-items:center;">';
    ariaRenderThemeConfigTransferButtons();
    echo '<button id="check-update" class="btn primary">检查更新</button></p>
</div>';
}
