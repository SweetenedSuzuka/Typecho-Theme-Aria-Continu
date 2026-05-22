<div align="center">

# Typecho Theme · 咏叹调 通奏低音 · Aria Continuo

> 书写自己的篇章  
> 让期许不再落幕

[![Version](https://img.shields.io/badge/version-1.18.0-blue.svg)](#更新日志)
[![Typecho](https://img.shields.io/badge/Typecho-Compatible-green.svg)](#)
[![License](https://img.shields.io/badge/license-GPL%202.0-blue.svg)](#)

</div>

---

> [!NOTE]
> ~~当前项目仍以旧版 Aria 的功能优化与 BUG 修复为主，尚未进入完整风格重构阶段。~~  
> ~~由于原版中存在较多编译后的前端资源，恢复可维护源码仍需要一定时间。~~  
> 已恢复可维护源码，进入重构。  
> 希望2.0可以带来新的视觉效果。  

---

## 关于项目

**Aria Continuo** 是 [Siphils](https://github.com/Siphils) 开发的 Typecho 主题 [Aria](https://github.com/Siphils/Typecho-Theme-Aria) 的第三方延续版本。

Aria，即咏叹调。  
一种简约而富有美感的抒情音乐风格——这正是它所追求的：简洁与精致并存。

曾经有过这样一种设计语言：它不迷恋超大圆角，也不偏执于锐利的板正；它不属于拟物，也尚未被扁平完全定义。  

那是 Windows 7 的 Aero 仍在发光、iOS 7 掀起扁平革命的年代。  
人们惊叹于 Windows 8 与 Windows 10 的高效与方正，感叹于 Material 与 卡片的精美。
有人在玻璃与光影之间寻找新生，有人在模糊与透明之间规划未来。

那是一个尚未被统一的设计时代——有人在色块的碰撞里找秩序，有人在拟物的圆润里寻温度。风格各异，百花齐放。  

直到扁平大圆角几乎将一切格式化。

有人说，2010 年代的繁荣不过是时代过渡，是通往“唯一正确答案”之前无数试错的注脚。  
那个答案是否真正正确，如今没有人能够确定。

但总会有人怀念那个时代独特的美感。

---

### 关于「Aria Continuo」

**Continuo**，即“通奏低音”。

这是乐曲中贯穿全曲的低音线，这些音符用近乎沉默的底色，支撑着所有响亮奏鸣的旋律。  

我希望它能够接过 Aria 的接力棒。

在如今扁平大圆角独奏高歌的今天，  
继续奏响那段绵延不绝的、关于美的想象。

---

> 原 README 请参见 [`README_old.md`](./README_old.md)

---

## 预览

<p align="center">
  <img src="https://github.com/Siphils/Typecho-Theme-Aria/blob/master/screenshot.png?raw=true" alt="Aria Screenshot">
</p>

---

### 使用方法

#### 快速安装

**将主题文件解压到 Typecho 的 `/usr/themes/` 目录下即可完成安装。**

#### 更新指南

| 版本范围 | 更新步骤 | 备注 |
|---------|--------|------|
| **1.16.0 及更早版本** | 1. 保存当前配置文件<br>2. 删除旧主题文件<br>3. 解压新版本到原目录<br>4. 切换到其他主题再切换回来以清除旧数据 | 必须手动清除旧数据 |
| **1.17.0 及以后版本** | 直接将新版本文件覆盖旧文件 | 无需额外操作 |

> [!TIP]
> 从原版 Aria 更新到任意版本，最简单的方法是：  
> 1. 打开 Aria 的配置页  
> 2. 删除旧主题文件  
> 3. 解压新版本到原目录  
> 4. 用新标签页打开 Aria Continuo 配置页（不要关闭原来打开的 Aria 配置页），然后切换到其他主题再切换回来以清除 Typecho 保存的旧配置数据  
> 5. 从 Aria 的配置页，把为数不多的设置项一个一个复制到新标签页，保存并关闭网页  
> 6. 配置迁移完成  
> 由于 Aria 本身就没多少配置项，而且大概也没有用户在使用了，所以我建议花五分钟复制粘贴一下。  
> ~~绝对不是我懒得写更新脚本~~

#### 更多资料

- [旧版 Wiki（原项目文档）](https://eriri.ink/archives/Aria-manual.html)

> [!IMPORTANT]
> Aria中的一些功能，在 Aria Continuo 已经移除或更换，原 Wiki 的信息对于 Aria Continuo 来说有可能已经过时。

---

## 更新日志

### 2026-05-22 1.18.0  

* 调整：图片查看改为内置灯箱实现  
  * 主题不再使用 `jQuery` 与 `Fancybox`  
  * 正文图片统一支持同页分组查看，评论图片保持单张查看  
  * 更换了模糊效果的灯箱效果
  * **破坏性更新：如果要使用灯箱，需要手动重新打开一下灯箱设置**  
* 调整：图片灯箱配置丢弃历史命名   
  * 后台开关统一为 `enableImageLightbox`  
  * 不再继续保留 `enableFancybox`、`ENABLE_FANCYBOX`、`Aria.fancybox` 等旧命名兼容  
* 新增：项目内部最小检查链  
  * 新增 `npm run check`，统一执行 PHP、JS、JSON、主线 JS lint 与版本一致性检查  
  * 增加 `.editorconfig` 与最小 `eslint` 配置  

<details>
<summary><strong>展开更多Aria Continuo更新日志</strong></summary>

### 2026-05-22 1.17.1  

* 修复：在检查更新借口接口添加时间戳查询参数，防止浏览器缓存导致检测更新问题

### 2026-05-22 1.17.0  

* **破坏性更新：不再支持旧Aria主题的无缝升级，请手动复制设置到相应栏目保存**
* 调整：主题配置模型更换为 `Aria Continuo` 自己的独立字段  
  * 设置项不再读取旧版本的 `AriaConfig`，取消与旧版本Aria设置的兼容  
  * 统一后台保存链、运行时读取链与前台生效链，修复了旧版本中全新安装时部分设置项保存后不生效或变回默认值的问题  
* 调整：导航配置切换为完整 JSON 数组格式，并修正默认示例  
  * `navConfig` 现在只支持完整 JSON 数组  
  * 更换了默认导航栏配置，提供了一个完整的示例  
* 新增：后台内置主题配置导入 / 导出  
  * 导出时只包含与默认值不同的配置项，并直接下载到本地 JSON 文件  
  * 具有在覆盖非默认配置时的提示功能
* 调整：继续完善配置与导航相关的后台使用体验  
  * 懒加载占位图开关读取链已统一到独立字段方法  

### 2026-05-20 1.16.0  

* 调整：`jQuery` 依赖大幅收缩  
  * `base.js`、`action.js`、`toc.js`、`comment.js` 已改为原生实现，不再依赖 `jQuery`  
  * 删除不再需要的 `jquery-resize.js` 模块  
* 新增：可选本地化图标包机制  
  * 内置 `Remix Icon v4.9.1`、`Bootstrap Icons v1.13.1`、`Font Awesome Free v7.2.0`  
  * 后台新增 `附加图标包` 多选开关，默认关闭，仅加载主题自带 `iconfont`  
  * 启用后整站可用，包括导航配置、文章 HTML、自定义注入内容  
  * `Font Awesome Free` 兼容 `v4` 写法  
* 调整：`OwO` 表情加载体验重构  
  * `OwO` CSS/JS 改为在评论区接近视口时通过 `IntersectionObserver` 预加载并初始化  
  * 评论模板直出外观一致的占位入口，加载成功后无缝切换为真实表情面板  
  * 加载中或加载失败时点击占位按钮有明确提示，体验更一致  
  * 首屏距离评论区较远的页面不再一开始就抢占表情资源  
* 修正：`go-top` 返回顶部按钮在滚动过程中不再反复闪烁  
* 调整：评论回复/取消回复从 `TypechoComment` 全局脚本迁到主题前端模块  
* 调整：继续收口 `OwO` 为评论表单按需加载资源（CSS + JS 均不再全站固定装载）

### 2026-05-20 1.15.0  

* 调整：完成前端历史运行时依赖收口  
  * 代码复制从 `ClipboardJS` 切换为原生 Clipboard API，并保留旧环境回退  
  * 通知提示从 `Notyf` 切换为主题自有轻量实现，继续复用原有视觉样式  
  * 代码块行号从 `highlightjs-line-numbers` 切换为主题自有实现，并避免行号结构污染复制内容  
  * 目录平滑滚动、导航吸顶隐藏、滚动入场动画分别从 `SmoothScroll`、`Headroom`、`WOW` 切换为浏览器原生或主题自有实现  
* 调整：继续完善交互与可配置性  
  * 新增“启用导航栏吸顶隐藏”后台开关，默认开启，关闭后导航栏不再因滚动自动收起  
  * 修复 Fancybox 关闭时的焦点回退导致页面瞬间跳动的问题  
  * 导航吸顶隐藏逻辑改为主题自有实现，并保留原有 `headroom*` 类名契约  

### 2026-05-20 1.14.0  

* 调整：移除 `PJAX`：  
  * 后台不再保留 `PJAX` 开关，前台主路径已完全回归普通服务端页面跳转  
  * 删除前端运行时中的 `PJAX` 配置、模板中的 `#pjax-container` 相关残留，以及评论退出链接、打赏/二维码按钮上的 `no-pjax` 标记  
  * 停止加载并移除 `assets/js/jquery.pjax.min.js`，同时清理 `MathJax` 与前端初始化流程中对 `pjax:complete` 的历史依赖  
* 调整：更换评论 `AJAX` 实现方式  
  * 评论头像异步获取由 `jQuery.ajax` 切换为 `fetch`  
  * 评论提交由 `serializeArray() + $.ajax + $.parseHTML()` 切换为 `FormData/URLSearchParams + fetch + DOMParser`  
  * 评论回复状态跟踪、表单提交绑定与局部 DOM 插入改为基于原生事件与原生 DOM 的实现  
  * 保留原有不刷新提交、局部插入、回复/取消、Notyf 提示与评论区 `MathJax` 增量补排版体验  
* 调整：以新方法替换旧 `jquery-lazyload`：  
  * 图片懒加载改为 `IntersectionObserver + 原生属性` 实现  
  * 保留主题原有的 `loading.svg` 占位体验，图片进入视口后再切换真实资源  
  * 文章卡片缩略图改为统一使用自定义数据标记驱动背景图懒加载  
  * 停止加载并移除 `assets/js/jquery.lazyload.min.js`  
* 继续收口主题内部职责边界：  
  * `ThemeViewData` 继续承接页脚完整视图数据，以及搜索占位文本、首页副标题等显示层读取逻辑  
  * 新增 `ThemeSiteLookup`，统一承接管理员头像与页面信息查询  
  * 继续收束 `Utils`，删除一批已迁移后的旧私有残留方法  
  * 导航渲染、运行时配置、脚本资源、`MathJax` 兼容层等能力的归属进一步明确，主题结构更接近“入口装配 + helper 组织”的可维护形态  
* 兼容性与稳定性调整：  
  * 修复页脚备案图标可能拖慢 `window.load`、进而影响旧前端链路初始化时机的问题  
  * 图片型备案图标新增 `loading="lazy"`、`decoding="async"`、`fetchpriority="low"`、`referrerpolicy="no-referrer"`  
  * 兼容将备案图标写成图标类名字符串的旧配置方式，避免误当外部图片请求  


### 2026-05-17 1.13.1  

* 修复评论 Markdown 结构输出：  
  * 移除评论模板中包裹 `<?php $comments->content(); ?>` 的额外外层 `<p>`，避免主题模板继续破坏已经生成好的评论 HTML 结构  
  * 当 Typecho 已开启“在评论中使用 Markdown 语法”时，主题会在请求内自动补齐评论 Markdown 常用的基础标签白名单，恢复链接、引用、列表、代码、粗体、斜体、删除线等常见结构  
  * 自动补齐仅作为兼容层生效：不修改数据库中的原始设置，不覆盖用户已手动配置的允许标签，也不默认放开标题标签  
* 继续收紧前端内容注入边界：  
  * 目录项改为读取标题纯文本，不再把标题内容直接作为 HTML 写入目录链接  
  * Fancybox 外层链接改为通过 DOM API 创建，不再手工拼接 `href` 与 `data-caption` 属性字符串  
* 兼容性与稳定性调整：  
  * 一言文案改为使用 `.text()` 注入，避免将外部返回内容直接作为 HTML 插入  
  * AJAX 评论响应解析不再保留脚本节点，并为异常响应场景补充更稳妥的安全回退  
  * `Headroom` 初始化增加导航节点缺失保护，降低旧结构或异常页面下的前端报错概率  
  * 评论回复前缀中的父评论作者名加入显式转义  

### 2026-05-16 1.13.0  

* 新增可配置项：  
  * 新增评论框背景图开关（字段名：`customCommentBoxBackgroundEnabled`）与地址设置（字段名：`customCommentBoxBackgroundUrl`），默认关闭  
  * 开启后支持相对主题目录路径和绝对 URL，图片显示在评论输入框右下角  
  * 关闭时不显示评论框背景图，恢复原版输入框样式
* 继续收口模板与视图数据边界：  
  * `Utils` 新增 `getPostViewData()`，统一承接文章页与页面页头部展示所需的阅读量后缀、分类/标签/上下篇/TOC 显示开关  
  * `Utils` 新增 `getPostCardViewData()`，统一承接文章卡片所需的缩略图、加载占位图、分类分隔符、懒加载与分隔线开关  
  * 文章页与页面页共用 `<article>` 主体结构下沉为 `components/post-content.php` 统一片段  
  * 评论展示层与表单配置（邮箱/网址必填态、Markdown 提示、评论区邮件通知等）继续从片段内收口到统一视图数据  
  * 页头片段进一步压缩直读配置，站点标题与站点首页 URL 并入统一视图数据  
* 兼容性与稳定性调整：  
  * 加固 AJAX 评论对响应内容的解析：成功回调改为独立解析响应树，失败提示不再误用当前页面标题和容器  
  * 恢复自定义 JS 在 `main.js` 之前的正确加载顺序，修复后台设置说明与实际顺序不一致的回归问题  
  * 统一 MathJax 在 PJAX 链路中的触发入口，降低 PJAX 完成后数学公式排版的脆弱性  
  * 评论作者名与外链地址补上显式 HTML 转义，低版本兼容脚本地址由 `http://` 统一改为 `https://`  
* 性能优化：  
  * `getPostView()` 优先复用当前上下文中的 `views` 字段（如列表或单篇上下文已自带该字段），减少无效数据库查询  

### 2026-05-16 1.12.0  

* 新增可配置项：  
  * 新增网页背景图开关（字段名：`customPageBackgroundEnabled`）与地址设置（字段名：`customPageBackgroundUrl`），默认关闭  
  * 支持填写相对于主题目录的路径，例如 `/assets/img/background.webp`，也支持直接填写绝对 URL  
  * 后台仅在开启该功能时显示背景图地址输入框  
  * 关闭时会使用Aria风格的 `body` 样式，开启后再应用自定义背景图  
* 继续收口模板与视图数据边界：  
  * `header.php` 继续收口页头资源、站点标题、站点首页 URL、搜索框与 Hero 所需数据，页头片段进一步改为消费统一视图数据  
  * `footer.php` 继续收口脚本清单、自定义注入、统计代码、MathJax 兼容层和评论区补排版逻辑  
  * 评论展示层新增统一视图数据入口，等待提示、关闭提示、UA 开关与评论区 MathJax 忽略态不再散落在片段内直接读取  
* 继续整理主题设置与显示行为：  
  * 网页背景图设置已接入后台联动显示  
  * `MathJaxConfig`、页脚链接、备案信息、页头资源、评论展示相关配置继续从模板中下沉到 `Utils`  
* 兼容性与稳定性调整：  
  * MathJax 配置、兼容层与评论区追加渲染逻辑继续整理，减少模板中的长内联逻辑  
  * 归档时间轴不再依赖 `pageSize=10000` 的查询方式，显示结构保持不变，同时去掉固定文章数量上限  
  * 对导航页映射、上下篇、评论链路与浏览量输出补充请求内缓存  
  * 评论回复链路中父级评论与作者信息的读取继续压缩，减少展示层上的重复查询  

### 2026-05-15 1.11.0  

* 还原前端样式为可维护源码：  
  * 将 `style.min.css` 进一步拆分为 `assets/css/restored/base.css`、`layout.css`、`post.css`、`comments.css`、`extras.css`  
  * 前台样式入口改为直接加载这些可读源码文件  
* 清理模板中的表现层耦合：  
  * 将 `header.php` 中页头高度、背景图、站点信息显隐相关的内联样式改为 class 与 CSS 变量驱动  
  * 页头背景图不再依赖模板内直接拼接的表现层样式，后续样式调整可以继续在 CSS 源码层处理  
* 拆分主题设置：  
  * `functions.php` 现在只保留主题装配入口  
  * 新增 `inc/` 目录，拆出常量、依赖、字段、Hook、初始化逻辑  
  * 新增 `admin/` 目录，承接后台配置定义与后台 UI 资源  
* 整理主题设置页：  
  * 新增 `admin/theme-config-ui.php`，收口后台设置页的样式、联动脚本与说明面板  
  * 重新整理设置项顺序，将站点形象、导航、内容增强、评论、页脚、特殊页面、高级注入、开关设置等按职责分组  
  * 结构化MathJax 与一言相关设置：  
    * 将 `enableMathJax`、`enableMathJaxInComments`、`showHitokoto` 从旧的 `AriaConfig` 总开关中拆出为独立设置项  
    * `MathJax 配置`、`评论区启用 MathJax 解析` 改为仅在启用 MathJax 后显示  
    * `自定义一言接口地址` 改为仅在启用一言后显示  
    * 保留对旧 `AriaConfig` 数据的回退兼容，老站点未重新保存前不会立刻失效  
* 统一若干单独开关的样式与行为：  
  * 将 `启用首页分类排除` 、 `显示页脚备案信息` 这样仅有开关两种状态的选项改为与其它开关相同的控件  
  * 各项开关控制的配置项仅在开关打开的情况下显示  
* 增强配置读取层：  
  * `Utils` 新增独立功能开关优先、旧配置回退的统一读取方式  
  * 前台对 MathJax / 一言的读取逻辑同步切换为兼容模式  
* 修复与稳定性调整：  
  * 降低模板、配置与前端初始化之间的耦合

</details>

### 2026-05-15 1.10.0  

* 新增后台设置中的可配置项：  
  * 页脚站点名称与链接（字段名：`footerSiteName` / `footerSiteUrl`）：用于替代页脚里硬编码的站点名与跳转地址  
  * 页脚署名显示方式（字段名：`footerCreditsMode`）：可选“原主题署名 / Continuo 署名 / 自定义署名 / 隐藏署名”  
  * 自定义署名文本与链接（字段名：`footerCreditsText` / `footerCreditsLink`）：仅在选择“自定义署名”时使用  
  * 页脚备案信息（字段名：`footerRecords`）：按条目配置，写几条显示几条  
  * 页脚备案显示开关（字段名：`footerRecordsEnabled`）：关闭时不显示备案信息，但保留配置内容  
  * Copyright 年份（字段名：`cpr`）：支持 `{Y}` 等动态年份占位符（示例：`2022-{Y}`）  
  * 首页副标题独立于网页副标题（字段名：`heroSubtitle`）：支持单独自定义首页标题下方的副标题  
  * 搜索框 placeholder 可配置（字段名：`searchPlaceholder`）  
  * 可以指定首页不显示的分类，新增了首页文章分类过滤规则（字段名：`homeExcludeCategories`）并支持开关（字段名：`homeExcludeCategoriesEnabled`）  
  * 评论等待审核提示（字段名：`commentWaitingText`）  
  * 评论关闭提示（字段名：`commentClosedText`）  
  * 404 标题与描述（字段名：`notFoundTitle` / `notFoundDescription`）  
* 修复 1.9.0 中 MathJax 的失效问题：  
  * 仅在启用 MathJax 时加载资源（避免重复加载与不安全资源）  
  * 更换为 MathJax v4.1.2  
  * 兼容旧的配置写法 `MathJax.Hub.Config(...)` 与 PJAX 场景下的重新排版调用  
* 修复：`footer.php` 中无效的文档结构（移除重复的 `<!DOCTYPE html>` / `<html>` 等文档级标签，恢复为片段模板）  
* 修复：后台“页脚署名模式”无法切换（表单控件由 `Radio` 改为 `Select`）  
* 修复：`themeInit()` 中 AJAX 头像请求判断不准确（显式判断请求方法与 `action` 值，补充 `email` 缺失兜底）  
* 调整：移除浏览量统计中的运行期改表逻辑（不再在页面渲染时执行 `ALTER TABLE`；字段不存在时安全回退为 `0`；修正单篇首次访问显示值）  
* 调整：补充统一的配置读取辅助方法（判断配置是否存在、读取字符串配置、读取 1/0 开关配置、拆分 slug/列表文本），并将模板中的重复判断逻辑包装为统一调用  

---

<p align="center">
──────────── ✦ ────────────
</p>

<p align="center">
行板乐章 · 时光荏苒<br>
Andante · 2019 → 2026
</p>

<p align="center">
──────────── ✦ ────────────
</p>

---

### 2019-2-23 1.9.0  

* **警告：由于部分配置变量名修改，部分配置信息可能丢失，请更新前先做数据库或配置信息备份**  
* 移除了评论框图片  
* 移除小部分文件  
* 大部分代码重写  
* 文章/评论部分功能解析移至后端  
* 增加了对MathJax的支持  
* 增加了对DPlayer的pjax重载支持  
* 增加了对目录条目的滚动监听  
* 增加了文章卡片显示动画  
* 增加了评论区文章作者标识  
* Fixed #21  

<details>
<summary><strong>查看更早的Aria更新日志</strong></summary>

### 2019-1-22 1.8.4  

* 代码块样式稍做优化  
* 代码块右上角增加了拷贝代码的按钮  
* 部分样式稍作修改  
* 去除了搜索框的背景图  
* 去除部分冗余代码  
* 修复了博客信息中Gravatar头像丢失的问题  
* 修复其他一些小BUG  
* 增加了文章目录(支持h2-h4三层解析)的功能，文章/页面编辑页面可以选择开启或关闭  

### 2018-12-3 1.8.3  

* 现在点击文章左下角的"Read More"的按钮会在新页面打开文章  
* 现在首页文章日期格式会按照`后台->设置->阅读->文章日期格式`的设置输出  
* 现在子菜单会根据内容自动撑开而不是适配父级菜单宽度（感谢P3TERX)  
* 修改评论输出函数，适配`后台->设置->评论`的多项配置选项(同时修复了包括但不限于开启评论审核导致输出错误的bug)  
* 手机端子菜单间距微调（防止误触等）

### 2018-11-10 1.8.2  

* 增加了自定义gravatar头像源的配置，现在你可以选择自己想用的源了  
* 增加了对Meting和MathJax的PJAX结束后的重载  
* 增加了文章内部分图片不使用fancybox的方法  
* 导航栏解析新增`slug`参数  
* 修复了一个归档页面的输出bug  
* 修复了一个ajax评论的bug  
* 修复了子菜单超出边框的bug  
* 现在设置项`头像URL`为空时默认根据用户的邮箱调用gravatar头像  
* 自定义pjax重载函数从`userAction`更换为`Aria.reloadAction`  
* 微调部分样式  
* **修改了导航栏图标的解析，现在使用主题自带的图标需要加上`iconfont`，即完整的一个class**  

### 2018-10-11 1.8.1  

* 增加了自定义「一言」接口地址的设置项，现在你可以使用自己的接口了  
* 增加了PJAX重载函数的接口  
* 现在评论回复日期是根据你自己的设定进行输出了  
* 修复了一个短代码注册函数的bug  
* 修复了第一篇文章上下篇的显示bug(若只有一篇文章则不会显示上一篇/下一篇)  
* 对部分样式的细节进行优化处理  
* 处理部分css动画  
* 优化部分js代码  
* 对文章的代码块进行美化（css风格来自[Mashiro](https://2heng.xin)）

### 2018-10-2 1.8.0  

* 增加了对导航二级菜单的支持  
* 增加了代码高亮块的语言类型提示  
* 修复了一个底部链接组件的bug  
* 重写显示上下篇的代码  
* 微调部分样式  

### 2018-9-27 1.7.1  

* 增加了`底部链接组件`的设置项  
* 增加了评论的 `useragent` 和对应的设置开关  
* 增加了主题最新版本检测的后台按钮  
* 重写归档页面的样式  
* 修复了一个后台开关设置的bug  

### 2018-9-12 1.7.0  

* 修改大部分样式  
* 评论区重写  
* 友情链接页面的样式整合到全局css（可以在任意文章/页面使用`[link-box]` `[link-item]`）
* 文章短代码优化  
* 删除部分函数  
* 修复部分BUG  

### 2018-8-12 1.6.1  

* 优化了ajax评论代码  
* 后台主题设置使用semantic-ui进行美化  
* 部分细节微调  
* 增加了notyf对于评论成功或者失败的提示（可以配合SmartSpam插件使用）
* 增加了图片懒加载的开关  
* 增加了对统计代码的重载(目前支持谷歌分析和百度统计)  

### 2018-8-7 1.6.0

* `友情链接`页面部分样式微调  
* css和js文件改为本地调用  
* 增加了pjax页面切换特效  
* 增加了ajax评论选择开关   
* 移除了底部的页面加载时间  

### 2018-7-30 1.5

* 增加了文章底部打赏功能和文章二维码功能
* 增加了评论部分**不接收邮件回复**的按钮（需要配合插件[CommentToMail](https://9sb.org/58 "CommentToMail")使用）
* 修复了一个无法输出缩略图和预览内容的bug  
* `iconfont`的使用方式从`Unicode`修改为`Font Class`的方式  
* 修改部分样式  
* 重写部分代码  

### 2018-7-9 1.4  

* 增加了一些设置的开关  
* 增加了处理主题配置信息相关功能  
* 增加了导航栏的配置功能  
* 修改大部分样式  
* 重写一部分函数  
* `Prism.js`更换为`highlight.js`  
* ......  

### 2018-6-24 1.3  

* 修改部分字体样式  
* 修改首页卡片样式  
* 修改部分文章样式  
* 修改底部样式  
* 底部添加了一言  
* 去除了思源黑体

### 2018-6-20 1.2  
* 更新了友情链接页面短代码匹配以及部分样式  
* 增加了pjax无刷新加载  
* 增加页面进度条  
* 去除了首页文章预览内容的显示  

### 2018-6-15 1.1 Beta  

* 评论头像换用cn.gravatar.org的源  
* 增加了用户评论的UA显示  
* 更新了文章底部上一篇/下一篇的样式  
* 更新了默认随机缩略图的显示  
* 更新了部分样式  
* 修复了归档页面输出时光轴的BUG  

</details>

---

## 相关开源项目

- [highlight.js](https://highlightjs.org/)
- [Bento Grid System](https://github.com/fenbox/bento)
- [MathJax](https://www.mathjax.org/)
- [DIYgod/OwO](https://github.com/DIYgod/OwO)
- [animate.css](https://daneden.github.io/animate.css/)
- [Remix Icon](https://remixicon.com/)
- [Bootstrap Icons](https://icons.getbootstrap.com/)
- [Font Awesome Free](https://fontawesome.com/)

<p align="center">
──────────── ✦ ────────────
</p>

<p align="center">
连奏 · 致谢所有的协奏者<br>
Legato · Then and Now
</p>

<p align="center">
──────────── ✦ ────────────
</p>

## 曾经使用过的开源项目

- [jQuery](https://jquery.com/)
- [jquery-pjax](https://github.com/defunkt/jquery-pjax)
- [jquery-lazyload](https://appelsiini.net/projects/lazyload/)
- [highlightjs-line-numbers](https://wcoder.github.io/highlightjs-line-numbers.js/)
- [Fancybox3](https://fancyapps.com/fancybox/3/)
- [clipboard.js](https://zenorocha.github.io/clipboard.js)
- [smooth-scroll](https://github.com/cferdinandi/smooth-scroll)
- [headroom.js](http://wicky.nillia.ms/headroom.js/)
- [wow.js](https://wowjs.uk/)
- [NProgress](https://github.com/rstacruz/nprogress)

> 尽管这些项目、库的内容或实现方法已经移除，但我们永远感谢前人做出的伟大贡献，没有这些项目，无论是Aria还是Aria Continuo都无法顺利问世。

---

## 推荐插件（非必需）

- [CommentToMail](https://9sb.org/58)
- [SmartSpam](http://www.yovisun.com/archive/typecho-plugin-smartspam.html)
- [Sticky](https://github.com/hitop/typechoSticky)

> 这些是Aria遗留的推荐项，一些内容已经不再可用；还未更新。

---

<div align="center">

### 咏叹调・通奏低音

书写自己的篇章，让期许不再落幕。

#### Aria Continuo

---

<p align="center">
──────────── ✦ ────────────
</p>

回声 · Echo<br>
念念不忘，必有回响 · Echoes and Continuations

<p align="center">
──────────── ✦ ────────────
</p>

</div>
