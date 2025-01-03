=== Muki AI Summary ===
Contributors: muki
Donate link: https://muki.tw/muki-ai-summary
Tags: ai, summary, content openai, article
Requires at least: 6.0
Tested up to: 6.7
Stable tag: 1.0.4
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

使用 OpenAI 為您的文章生成摘要。

== 介紹 ==

Muki AI Summary 串接 OpenAI Key，幫助您的自動產生文章摘要。
這個工具可以讓您的讀者快速了解文章的主要內容，提高用戶體驗並增加網站的參與度。

主要特點：
* 自動生成文章摘要
* 兩種模型可選 (GPT 3.5 turbo, GPT 4o)
* 可自定義摘要長度
* 支持多種語言 (英文、繁體中文、簡體中文、日文)
* 易於使用的設定界面

== 使用的外部服務 ==

此外掛使用 OpenAI 的 API：

* 用途：使用 AI 生成文章摘要
* API 網址：
  - https://api.openai.com/v1/chat/completions
* 資料傳輸：
  - 時機：僅在生成摘要時(手動或啟用自動生成時)
  - 發送內容：文章標題和內容
  - 接收內容：AI 生成的摘要文字
* 隱私權與條款：
  - OpenAI 隱私權政策：https://openai.com/privacy/
  - OpenAI 服務條款：https://openai.com/terms/
  - OpenAI API 資料使用政策：https://openai.com/policies/api-data-usage-policies

注意：您的 OpenAI API 金鑰和文章內容會直接傳送到 OpenAI 的伺服器。使用此外掛前，請詳閱 OpenAI 的隱私權政策和服務條款。

== 安裝步驟 ==

1. 將 `muki-ai-summary` 文件夾上傳到 `/wp-content/plugins/` 目錄
2. 在 WordPress 的 `外掛` 中啟用 Muki AI Summary 外掛
3. 前往 `設定 > Muki AI Summary` 進行設定

== 常見問與答 ==

= 這個外掛如何工作？ =

Muki AI Summary 使用 OpenAI 分析您的文章內容，然後生成一個簡潔而準確的摘要。

= 使用這個外掛時，我的內容安全嗎？ =

在生成摘要時，您的文章內容會被傳送到 OpenAI 的伺服器。OpenAI 維持嚴格的資料隱私和安全標準。不過，我們建議您在使用此外掛前，先詳閱 OpenAI 的隱私權政策和服務條款。您可以在上方的「使用的外部服務」部分找到這些文件的連結。

= 我可以自定義摘要的長度嗎？ =

是的，您可以在外掛設置中自定義摘要的長度。

== 外掛截圖 ==

1. 外掛設置界面
2. 文章中的 AI 摘要範例

== 更新紀錄 ==

= 1.0.4 =
修復單篇文章不會自動生成 AI 的 bug

= 1.0.1 =
新增多種語言: 英文、繁體中文、簡體中文、日文

= 1.0.0 =
* 初始版本發布

== 升級須知 ==

= 1.0.0 =
這是 Muki AI Summary 的首次發布版本。
