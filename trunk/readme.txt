=== Muki AI Summary ===
Contributors: muki
Donate link: https://muki.tw/muki-ai-summary
Tags: ai, summary, content openai, article
Requires at least: 6.0
Tested up to: 6.7
Stable tag: 1.0.3
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Generate article summaries using Open AI.

== Description ==

Muki AI Summary integrates with Open AI Key to help you automatically generate article summaries.
This tool allows your readers to quickly understand the main content of the article, improving user experience and increasing website engagement.

Main features:
* Automatically generate article summaries
* Two models to choose from (GPT 3.5 turbo, GPT 4o)
* Customizable summary length
* Supports multiple languages (English, Traditional Chinese, Simplified Chinese, Japanese)
* Easy-to-use settings interface

= External Services =

This plugin interacts with OpenAI's API service:

* Purpose: Generate article summaries using artificial intelligence
* API URL:
  - https://api.openai.com/v1/chat/completions
* Data Transmission:
  - When: Only when generating a summary (manually or auto-generate if enabled)
  - Content Sent: Article title and content
  - Content Received: AI-generated summary text
* Privacy & Terms:
  - OpenAI Privacy Policy: https://openai.com/privacy/
  - OpenAI Terms of Service: https://openai.com/terms/
  - OpenAI API Data Usage Policy: https://openai.com/policies/api-data-usage-policies

Note: Your OpenAI API key and article content are sent directly to OpenAI's servers. Please review OpenAI's privacy policy and terms of service before using this plugin.

== Installation ==

1. Upload the `muki-ai-summary` folder to the `/wp-content/plugins/` directory
2. Activate the Muki AI Summary plugin in the `Plugins` section of WordPress
3. Go to `Settings > Muki AI Summary` to configure

== Frequently Asked Questions ==

= How does this plugin work? =

Muki AI Summary uses Open AI to analyze your article content and then generate a concise and accurate summary.

= Is my content secure when using this plugin? =

When generating summaries, your article content is sent to OpenAI's servers. OpenAI maintains strict data privacy and security standards. However, we recommend reviewing OpenAI's privacy policy and terms of service before using this plugin. You can find links to these documents in the "External Service Usage" section above.

= Can I customize the length of the summary? =

Yes, you can customize the length of the summary in the plugin settings.

== Screenshots ==

1. Plugin settings interface
2. AI summary example in the article

== Changelog ==

= 1.0.0 =
* Initial version release

== Upgrade Notice ==

= 1.0.0 =
This is the first release of Muki AI Summary.
