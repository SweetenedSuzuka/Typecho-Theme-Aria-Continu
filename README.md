# Typecho-Theme-Aria-Continuo 
> 书写自己的篇章  
> 让期许不再落幕

----
此项目目前只是旧版Aria的功能优化和BUG修复，还不具备风格更新。  
这个情况会持续很久，因为Aria原版的内容很多是编译后的产物，还原需要一定时间。
----

**Aria Continuo** 是 [Siphils](https://github.com/Siphils) 开发的 Typecho 主题 [Aria](https://github.com/Siphils/Typecho-Theme-Aria) 的第三方延续版本。  
Aria，即咏叹调。一种简约而富有美感的抒情音乐风格——这正是它所追求的：简洁与精致的并存。  
曾经有过这样一种设计语言：它不迷恋超大圆角，也不偏执于锐利的板正；它不属于拟物，也尚未被扁平完全定义。  
那是 Windows 7 的 Aero 还在发光、iOS 7 掀起扁平革命的年代。人们惊叹于 Windows 8 和 10 的高效与方正。那是一个尚未被统一的设计时代——有人在色块的碰撞里找秩序，有人在拟物的圆润里寻温度。风格各异，百花齐放。  
直到扁平大圆角把一切格式化。  
有人说，2010 年代的那场繁荣不过是时代的过渡，是通向唯一正确答案之前无数试错的注脚。那个答案是不是对的，如今没有人能确定。但我相信，一定有人在怀念那个时代独特的美感。  
**Aria Continuo**，即通奏低音——乐曲中贯穿全曲的低音线，近乎沉默的底色，支撑着所有响亮奏鸣的旋律。  
希望它能接过 Aria 的接力棒。在扁平大圆角独奏高歌的今天，于世界的一隅，继续奏响那段绵延不绝的、关于美的想象。  

> 原 README 请参见 `README_old.md`。  

![screenshot](https://github.com/Siphils/Typecho-Theme-Aria/blob/master/screenshot.png?raw=true)  

## 使用方法  
[旧版 Wiki（原项目）](https://eriri.ink/archives/Aria-manual.html)  

## 更新  
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
* 新增自定义配置：  
  * 评论等待审核提示（字段名：`commentWaitingText`）  
  * 评论关闭提示（字段名：`commentClosedText`）  
  * 404 标题与描述（字段名：`notFoundTitle` / `notFoundDescription`）  
* 修复原版 MathJax 的失效问题：  
  * 仅在启用 MathJax 时加载资源（避免重复加载与不安全资源）  
  * 更换为 MathJax v4.1.2  
  * 兼容旧的配置写法 `MathJax.Hub.Config(...)` 与 PJAX 场景下的重新排版调用  
* 修复：`footer.php` 中无效的文档结构（移除重复的 `<!DOCTYPE html>` / `<html>` 等文档级标签，恢复为片段模板）  
* 修复：后台“页脚署名模式”无法切换（表单控件由 `Radio` 改为 `Select`）  
* 修复：`themeInit()` 中 AJAX 头像请求判断不准确（显式判断请求方法与 `action` 值，补充 `email` 缺失兜底）  
* 调整：移除浏览量统计中的运行期改表逻辑（不再在页面渲染时执行 `ALTER TABLE`；字段不存在时安全回退为 `0`；修正单篇首次访问显示值）  
* 调整：补充统一的配置读取辅助方法（判断配置是否存在、读取字符串配置、读取 1/0 开关配置、拆分 slug/列表文本），并将模板中的重复判断逻辑收口为统一调用  

> 以下为旧版更新日志

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
## 使用的开源项目:  
* [highlight.js](https://highlightjs.org/ "highlight.js")  
* [Bento Grid System](https://github.com/fenbox/bento "Bento Grid System")  
* [Fancybox3](https://fancyapps.com/fancybox/3/ "fancybox3")  
* [jQuery](https://jquery.com/ "jQuery")  
* [DIYgod/OwO](https://github.com/DIYgod/OwO "OwO")  
* [headroom.js](http://wicky.nillia.ms/headroom.js/ "headroom.js")  
* [jquery-pjax](https://github.com/defunkt/jquery-pjax "jquery-pjax")  
* [NProgress](https://github.com/rstacruz/nprogress "NProgress")  
* [animate.css](https://daneden.github.io/animate.css/ "animate.css")     
* [jquery-lazyload](https://appelsiini.net/projects/lazyload/ "jquery-lazyload")  
* [smooth-scroll](https://github.com/cferdinandi/smooth-scroll "smooth-scroll")  
* [highlightjs-line-numbers](https://wcoder.github.io/highlightjs-line-numbers.js/ "hljs-line-numbers")  
* [clipboard.js](https://zenorocha.github.io/clipboard.js "clipboard")  
## 部分插件推荐（非必需）
* [CommentToMail](https://9sb.org/58 "CommentToMail")*链接的文章中最后一个插件*  
* [SmartSpam](http://www.yovisun.com/archive/typecho-plugin-smartspam.html "SmartSpam")  
* [Sticky](https://github.com/hitop/typechoSticky "Sticky")
