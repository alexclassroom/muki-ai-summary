<?php
/**
 * Plugin Name:        Muki AI Summary
 * Plugin URI:         https://muki.tw/muki-ai-summary
 * Description:        WordPress plugin to generate article summaries using OpenAI
 * Requires at least:  6.0
 * Requires PHP:       7.0
 * Version:            1.0.5
 * Author:             Muki Wu
 * Author URI:         https://muki.tw
 * License:            GPL-2.0-or-later
 * License URI:        http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:        muki-ai-summary
 * Domain Path:        /languages
 */

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

// add version number
define('MUKI_AI_SUMMARY_VERSION', '1.0.4');

// Setup menu
function muki_ai_summary_menu() {
  add_options_page(__( 'Muki AI Summary Settings', 'muki-ai-summary' ), 'Muki AI Summary', 'manage_options', 'muki-ai-summary', 'muki_ai_summary_options_page');
}
add_action('admin_menu', 'muki_ai_summary_menu');

// Settings page content
function muki_ai_summary_options_page() {
  // Check user permissions
  if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.', 'muki-ai-summary'));
  }

  // Add a button to clear all AI summary data
  if (isset($_POST['muki_ai_clean_summaries'])) {
    check_admin_referer('muki_ai_clean_summaries_nonce');
    muki_ai_clean_all_summaries();
    echo '<div class="notice notice-success"><p>' . 
      __('All AI summary data has been cleared.', 'muki-ai-summary') . 
      '</p></div>';
  }
  ?>
  <div class="wrap">
    <h1><?php esc_html_e('Muki AI Summary Settings', 'muki-ai-summary'); ?></h1>
    <form method="post" action="options.php">
      <?php
      settings_fields('muki_ai_summary_options');
      do_settings_sections('muki-ai-summary');
      submit_button();
      ?>
    </form>
    <form method="post" action="">
      <?php wp_nonce_field('muki_ai_clean_summaries_nonce'); ?>
      <input type="submit" name="muki_ai_clean_summaries" class="button button-secondary" value="<?php esc_attr_e( 'Clear All AI Summary Data', 'muki-ai-summary' ); ?>" onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to clear all AI summary data? This action cannot be undone.', 'muki-ai-summary' ); ?>');">
    </form>
    <div class="muki-ai-summary-usage">
      <h2><?php esc_html_e('Using AI-generated summaries in themes', 'muki-ai-summary'); ?></h2>
      <p><?php esc_html_e('You can use the following function in your theme to display AI-generated summaries:', 'muki-ai-summary'); ?></p>
      <pre><code>if (function_exists('muki_ai_get_summary')) {
            echo muki_ai_get_summary(get_the_ID());
          }</code></pre>
      <p><?php esc_html_e('This function will return the AI-generated summary for the current post. If the summary doesn\'t exist, it will return null.', 'muki-ai-summary'); ?></p>
      <h2><?php esc_html_e('Customizing AI Summary Style', 'muki-ai-summary'); ?></h2>
      <p><?php printf(esc_html__( 'AI summaries use the class %1$smuki_ai_summary--excerpt%2$s for styling. You can customize the style in your theme\'s style.css file:', 'muki-ai-summary' ),'<code>','</code>'); ?></p>
      <pre><code>.muki_ai_summary--excerpt {
            /* Add your custom styles here */
          }</code></pre>
    </div>
  </div>
  <?php
}

// Register settings
function muki_ai_summary_register_settings() {
  register_setting('muki_ai_summary_options', 'muki_ai_openai_key', 'sanitize_text_field');
  register_setting('muki_ai_summary_options', 'muki_ai_summary_position', 'sanitize_text_field');
  register_setting('muki_ai_summary_options', 'muki_ai_replace_excerpt', 'intval');
  register_setting('muki_ai_summary_options', 'muki_ai_model', 'sanitize_text_field');
  register_setting('muki_ai_summary_options', 'muki_ai_summary_language', 'sanitize_text_field');
  register_setting('muki_ai_summary_options', 'muki_ai_summary_title', 'sanitize_text_field');
  register_setting('muki_ai_summary_options', 'muki_ai_summary_max_length', 'intval');

  add_settings_section(
    'muki_ai_summary_main',
    __('Main Settings', 'muki-ai-summary'),
    'muki_ai_summary_section_callback',
    'muki-ai-summary'
  );

  add_settings_field(
    'muki_ai_openai_key',
    __('OpenAI API Key', 'muki-ai-summary'),
    'muki_ai_openai_key_callback',
    'muki-ai-summary',
    'muki_ai_summary_main'
  );

  add_settings_field(
    'muki_ai_summary_position',
    __('Summary Display Position', 'muki-ai-summary'),
    'muki_ai_summary_position_callback',
    'muki-ai-summary',
    'muki_ai_summary_main'
  );

  add_settings_field(
    'muki_ai_replace_excerpt',
    __('Replace Native Excerpt in Post Lists', 'muki-ai-summary'),
    'muki_ai_replace_excerpt_callback',
    'muki-ai-summary',
    'muki_ai_summary_main'
  );

  add_settings_field(
    'muki_ai_model',
    __('OpenAI Model', 'muki-ai-summary'),
    'muki_ai_model_callback',
    'muki-ai-summary',
    'muki_ai_summary_main'
  );

  add_settings_field(
    'muki_ai_summary_language',
    __('Summary Language', 'muki-ai-summary'),
    'muki_ai_summary_language_callback',
    'muki-ai-summary',
    'muki_ai_summary_main'
  );

  add_settings_field(
    'muki_ai_summary_title',
    __('AI Summary Title', 'muki-ai-summary'),
    'muki_ai_summary_title_callback',
    'muki-ai-summary',
    'muki_ai_summary_main'
  );

  add_settings_field(
    'muki_ai_summary_max_length',
    __('Summary Max Length', 'muki-ai-summary'),
    'muki_ai_summary_max_length_callback',
    'muki-ai-summary',
    'muki_ai_summary_main'
  );

  register_setting('muki_ai_summary_options', 'muki_ai_summary_auto_generate', 'muki_ai_sanitize_auto_generate');

  add_settings_field(
    'muki_ai_summary_auto_generate',
    __('Auto-generate AI Summary', 'muki-ai-summary'),
    'muki_ai_summary_auto_generate_callback',
    'muki-ai-summary',
    'muki_ai_summary_main'
  );
}
add_action('admin_init', 'muki_ai_summary_register_settings');

// Settings callback functions
function muki_ai_summary_section_callback() {
  echo '<p>' . __('Please set your Muki AI Summary plugin options.', 'muki-ai-summary') . '</p>';
}

function muki_ai_openai_key_callback() {
  $key = get_option('muki_ai_openai_key');
  echo "<input type='text' name='muki_ai_openai_key' value='" . esc_attr($key) . "' class='regular-text'>";
}

function muki_ai_summary_position_callback() {
  $position = get_option('muki_ai_summary_position', 'top');
  ?>
  <select name="muki_ai_summary_position">
    <option value="top" <?php selected($position, 'top'); ?>><?php esc_html_e('Top of the article', 'muki-ai-summary'); ?></option>
    <option value="bottom" <?php selected($position, 'bottom'); ?>><?php esc_html_e('Bottom of the article', 'muki-ai-summary'); ?></option>
  </select>
  <?php
}

function muki_ai_replace_excerpt_callback() {
  $replace = get_option('muki_ai_replace_excerpt', 0);
  echo "<input type='checkbox' name='muki_ai_replace_excerpt' value='1' " . checked(1, $replace, false) . ">";
}

function muki_ai_model_callback() {
  $model = get_option('muki_ai_model', 'gpt-3.5-turbo');
  ?>
  <select name="muki_ai_model">
    <option value="gpt-3.5-turbo" <?php selected($model, 'gpt-3.5-turbo'); ?>><?php esc_html_e('GPT-3.5-turbo', 'muki-ai-summary'); ?></option>
    <option value="gpt-4o" <?php selected($model, 'gpt-4o'); ?>><?php esc_html_e('GPT-4', 'muki-ai-summary'); ?></option>
  </select>
  <p class="description"><?php esc_html_e('Choose the OpenAI model to use.', 'muki-ai-summary'); ?></p>
  <?php
}

function muki_ai_summary_language_callback() {
  $language = get_option('muki_ai_summary_language', 'zh-TW');
  ?>
  <select name="muki_ai_summary_language">
    <option value="zh-TW" <?php selected($language, 'zh-TW'); ?>><?php esc_html_e('Traditional Chinese', 'muki-ai-summary'); ?></option>
    <option value="zh-CN" <?php selected($language, 'zh-CN'); ?>><?php esc_html_e('Simplified Chinese', 'muki-ai-summary'); ?></option>
    <option value="en" <?php selected($language, 'en'); ?>><?php esc_html_e('English', 'muki-ai-summary'); ?></option>
    <option value="jp" <?php selected($language, 'jp'); ?>><?php esc_html_e('Japanese', 'muki-ai-summary'); ?></option>
  </select>
  <?php
}

function muki_ai_summary_title_callback() {
  $title = get_option('muki_ai_summary_title', __('AI Generated Summary', 'muki-ai-summary'));
  echo "<input type='text' name='muki_ai_summary_title' value='" . esc_attr($title) . "' class='regular-text'>";
  echo "<p class='description'>" . __('Enter the title to display before the AI summary.', 'muki-ai-summary') . "</p>";
}

// Callback function for auto-generating AI summaries
function muki_ai_summary_auto_generate_callback() {
  $options = get_option('muki_ai_summary_auto_generate', array(
    'single' => false,
    'list' => false
  ));
  ?>
  <label>
    <input type="checkbox" name="muki_ai_summary_auto_generate[single]" value="1" <?php checked(isset($options['single']) && $options['single'], true); ?>>
    <?php esc_html_e('Single post page', 'muki-ai-summary'); ?>
  </label>
  <p class="description"><?php esc_html_e('Choose where to auto-generate AI summaries (if not already generated).', 'muki-ai-summary'); ?></p>
  <?php
}

// Add new callback function to display summary max length setting field
function muki_ai_summary_max_length_callback() {
  $max_length = get_option('muki_ai_summary_max_length', 120);
  echo "<input type='number' name='muki_ai_summary_max_length' value='" . esc_attr($max_length) . "' min='50' max='500' step='10' class='small-text'>";
  echo "<p class='description'>" . __('Set the maximum character count (for Chinese characters) or word count (for English) for AI summaries. Default is 120.', 'muki-ai-summary') . "</p>";
}

// Generate summary function
function muki_ai_generate_summary($content, $post_id, $force_regenerate = false) {
  // Check user permissions
  if (!current_user_can('edit_posts')) {
    muki_ai_log_error('Unauthorized access attempt');
    return false;
  }

  $cached_summary = muki_ai_get_cached_summary($post_id);
  $summary_meta = get_post_meta($post_id, 'muki_ai_summary_meta', true);

  $current_model = get_option('muki_ai_model', 'gpt-3.5-turbo');
  $current_language = get_option('muki_ai_summary_language', 'zh-TW');

  if (
    $cached_summary && !$force_regenerate &&
    $summary_meta['model'] === $current_model &&
    $summary_meta['language'] === $current_language
  ) {
    return $cached_summary;
  }

  $api_key = get_option('muki_ai_openai_key');
  $model = get_option('muki_ai_model', 'gpt-3.5-turbo');
  $language = get_option('muki_ai_summary_language', 'zh-TW');
  $max_length = get_option('muki_ai_summary_max_length', 120);

  $system_message = "You are a professional article summarizer. Create a concise summary in 1-2 paragraphs, separated by a blank line. Ensure the summary is complete and doesn't end abruptly. Avoid using terms like 'the author' or 'this article'. Focus on key points and maintain the original tone.";

  if (empty($api_key)) {
    update_option('muki_ai_last_error', __( 'OpenAI API key not set', 'muki-ai-summary' ));
    return false;
  }

  $post = get_post($post_id);
  $title = $post->post_title;

  $language_prompts = [
    'zh-TW' => "用繁體中文總結文章，字數{$max_length}以內。句子簡潔易讀，直接陳述要點。中英文和數字間加半形空格，如：Apple 手機；3 個 AI 工具。",
    'zh-CN' => "用简体中文总结文章，字数{$max_length}以内。句子简洁易读，直接陈述要点。中英文和数字间加半角空格，如：Apple 手机；3 个 AI 工具。",
    'en' => "Summarize in English, {$max_length} words max. Use concise, direct sentences.",
    'jp' => "日本語で要約、{$max_length}文字以内。簡潔で読みやすい文で要点を直接述べる。日本語と英語・数字の間に半角ペース、例：Apple iPhone、3 AI ツール。"
  ];

  $prompt = $language_prompts[$language] . "\n\n" . 
    "Title: " . $title . "\n\n" . $content;
    
  $request_body = array(
    'model' => $model,
    'messages' => array(
      array(
        'role' => 'system',
        'content' => $system_message
      ),
      array(
        'role' => 'user',
        'content' => $prompt
      )
    ),
    'max_tokens' => 300,
    'temperature' => 0.5,
  );

  $response = wp_remote_post('https://api.openai.com/v1/chat/completions', array(
    'headers' => array(
      'Authorization' => 'Bearer ' . $api_key,
      'Content-Type' => 'application/json',
    ),
    'body' => json_encode($request_body),
    'timeout' => $model === 'gpt-4' ? 60 : 30,
    'data_format' => 'body',
  ));

  if (is_wp_error($response)) {
    $error_message = $response->get_error_message();
    update_option('muki_ai_last_error', 'OpenAI API Error: ' . $error_message);
    error_log('OpenAI API Error: ' . $error_message);
    return false;
  }

  $response_code = wp_remote_retrieve_response_code($response);
  $response_body = wp_remote_retrieve_body($response);

  if ($response_code !== 200) {
    $error_message = "HTTP Error {$response_code}: " . $response_body;
    update_option('muki_ai_last_error', $error_message);
    error_log($error_message);
    error_log('Request: ' . print_r($request_body, true));
    return false;
  }

  $body = json_decode($response_body, true);

  if (isset($body['choices'][0]['message']['content'])) {
    $summary = trim($body['choices'][0]['message']['content']);

    // Check if the summary is complete
    $last_char = mb_substr($summary, -1);
    if ($language === 'ja') {
      if ($last_char !== '。' && $last_char !== '！' && $last_char !== '？') {
        $summary .= '。';
      }
    } else {
      if ($last_char !== '.' && $last_char !== '!' && $last_char !== '?') {
        $summary .= '...';
      }
    }

    // Replace consecutive newline characters with a single newline character, and add extra newline characters between paragraphs
    $summary = preg_replace('/\n+/', "\n\n", $summary);

    muki_ai_set_cached_summary($post_id, $summary);
    update_post_meta($post_id, 'muki_ai_summary_meta', array(
      'model' => $current_model,
      'language' => $current_language
    ));
    return $summary;
  }

  $error_message = 'Invalid OpenAI API response: ' . print_r($body, true);
  update_option('muki_ai_last_error', $error_message);
  error_log($error_message);
  error_log('Request: ' . print_r($request_body, true));
  return false;
}

// Cache-related functions
function muki_ai_get_cached_summary($post_id) {
  return get_post_meta($post_id, 'muki_ai_summary', true);
}

function muki_ai_set_cached_summary($post_id, $summary) {
  update_post_meta($post_id, 'muki_ai_summary', $summary);
}

// Add sidebar button
function muki_ai_summary_meta_box() {
  add_meta_box('muki-ai-summary', __( 'Generate AI Summary', 'muki-ai-summary' ), 'muki_ai_summary_meta_box_callback', 'post', 'side', 'high');
}
add_action('add_meta_boxes', 'muki_ai_summary_meta_box');

// Sidebar content
function muki_ai_summary_meta_box_callback($post) {
  wp_nonce_field('muki_ai_summary_nonce', 'muki_ai_summary_nonce');
  $current_summary = get_post_meta($post->ID, 'muki_ai_summary', true);
  ?>
  <button id="muki-ai-generate-summary" class="button"><?php esc_html_e('Generate AI Summary', 'muki-ai-summary'); ?></button>
  <div id="muki-ai-summary-result">
    <?php if (!empty($current_summary)): ?>
      <h4><?php esc_html_e('Current AI Summary:', 'muki-ai-summary'); ?></h4>
      <p><?php echo wp_kses_post($current_summary); ?></p>
    <?php endif; ?>
  </div>
  <?php
}

// Add AJAX handler
function muki_ai_summary_ajax_handler() {
  // check nonce
  if (!check_ajax_referer('muki_ai_summary_nonce', 'nonce', false)) {
    muki_ai_log_error('Invalid nonce in AJAX request');
    wp_send_json_error('Security check failed');
    return;
  }

  // check user permissions
  if (!current_user_can('edit_posts')) {
    muki_ai_log_error('Unauthorized user attempted to generate summary');
    wp_send_json_error('Insufficient permissions');
    return;
  }

  $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
  
  // check post_id
  if (!$post_id || !get_post($post_id)) {
    muki_ai_log_error('Invalid post ID: ' . $post_id);
    wp_send_json_error('Invalid post ID');
    return;
  }

  $post = get_post($post_id);
  if (!$post) {
    error_log('Post not found');
    wp_send_json_error('Post not found');
    return;
  }

  try {
    error_log('Generating summary for post ' . $post_id);
    $summary = muki_ai_generate_summary($post->post_content, $post_id, true);

    if ($summary) {
      update_post_meta($post_id, 'muki_ai_summary', wp_kses_post($summary));
      error_log('Summary generated successfully');
      wp_send_json_success($summary);
    } else {
      $error = get_option('muki_ai_last_error', 'Unknown error');
      error_log('Failed to generate summary: ' . $error);
      wp_send_json_error('Failed to generate summary: ' . $error);
    }
  } catch (Exception $e) {
    error_log('Exception occurred: ' . $e->getMessage());
    wp_send_json_error('An exception occurred: ' . $e->getMessage());
  }
}
add_action('wp_ajax_muki_ai_generate_summary', 'muki_ai_summary_ajax_handler');

// Enqueue scripts
function muki_ai_summary_enqueue_scripts() {
  $auto_generate_options = get_option('muki_ai_summary_auto_generate', array('single' => false, 'list' => false));

  if (is_admin()) {
    wp_enqueue_script('muki-ai-summary-admin-js', plugin_dir_url(__FILE__) . 'src/js/muki-ai-summary-admin.js', array('jquery'), MUKI_AI_SUMMARY_VERSION, true);
    wp_localize_script('muki-ai-summary-admin-js', 'mukiAiSummary', array(
      'ajax_url' => admin_url('admin-ajax.php'),
      'nonce' => wp_create_nonce('muki_ai_summary_nonce')
    ));
  } elseif (($auto_generate_options['single'] && is_single()) || ($auto_generate_options['list'] && !is_single())) {
    wp_enqueue_script('muki-ai-summary-js', plugins_url('src/js/muki-ai-summary.js', __FILE__), array('jquery'), MUKI_AI_SUMMARY_VERSION, true);
    wp_localize_script('muki-ai-summary-js', 'mukiAiSummary', array(
      'ajax_url' => admin_url('admin-ajax.php'),
      'nonce' => wp_create_nonce('muki_ai_summary_nonce'),
      'is_single' => is_single(),
      'auto_generate_single' => $auto_generate_options['single'],
      'auto_generate_list' => $auto_generate_options['list']
    ));
  }
}
add_action('wp_enqueue_scripts', 'muki_ai_summary_enqueue_scripts');
add_action('admin_enqueue_scripts', 'muki_ai_summary_enqueue_scripts');

// Replace excerpt in post lists
function muki_ai_replace_excerpt($excerpt) {
  global $post;
  
  $replace_excerpt = get_option('muki_ai_replace_excerpt', false);
  $auto_generate_options = get_option('muki_ai_summary_auto_generate', array('single' => false, 'list' => false));
  
  if ($replace_excerpt && !is_single() && isset($auto_generate_options['list']) && $auto_generate_options['list']) {
    $ai_summary = get_post_meta($post->ID, 'muki_ai_summary', true);
    
    if (empty($ai_summary)) {
      // if no summary, return original excerpt
      return $excerpt;
    } else {
      $ai_summary = '<div class="muki_ai_summary--excerpt">' . implode('</p><p>', array_filter(explode("\n\n", $ai_summary))) . '</p></div>';
      return wp_kses_post($ai_summary);
    }
  }
  
  return $excerpt;
}
add_filter('get_the_excerpt', 'muki_ai_replace_excerpt', 10, 1);

// Display AI summary in post content
function muki_ai_display_summary($content) {
  global $post;
  // 只在單篇文章頁面處理
  if (!is_single()) {
    return $content;
  }

  $ai_summary = get_post_meta($post->ID, 'muki_ai_summary', true);
  $auto_generate_options = get_option('muki_ai_summary_auto_generate', array('single' => false));
  $summary_position = get_option('muki_ai_summary_position', 'top');
  $summary_title = get_option('muki_ai_summary_title', __('AI Generated Summary', 'muki-ai-summary'));

  // 如果已有摘要，直接顯示
  if (!empty($ai_summary)) {
    $summary_html = '<div class="muki-ai-summary"><h4 class="muki-ai-summary-title">' . 
      esc_html($summary_title) . '</h4><p>' . 
      str_replace("\n\n", "</p><p>", $ai_summary) . '</p></div>';
    
    if ($summary_position === 'top') {
      $content = $summary_html . $content;
    } else {
      $content .= $summary_html;
    }
  } elseif ($auto_generate_options['single']) {
    // 如果沒有摘要且啟用了自動生成，顯示載入中
    $loading_html = '<div class="muki-ai-summary muki-ai-summary--loading" data-post-id="' . $post->ID . '">
                      <div class="muki-ai-summary__loading-spinner"></div>
                      <p>' . __('Generating AI summary...', 'muki-ai-summary') . '</p>
                    </div>';
    
    if ($summary_position === 'top') {
      $content = $loading_html . $content;
    } else {
      $content .= $loading_html;
    }
  }
  
  return $content;
}
add_filter('the_content', 'muki_ai_display_summary');

// Function for themes to use
function muki_ai_get_summary($post_id = null)
{
  if (!$post_id) {
    $post_id = get_the_ID();
  }
  $summary = get_post_meta($post_id, 'muki_ai_summary', true);

  $auto_generate_options = get_option('muki_ai_summary_auto_generate', array('single' => false, 'list' => false));
  $is_single = is_single();

  // only generate summary if auto-generate is enabled
  if (empty($summary) && (($is_single && $auto_generate_options['single']) || (!$is_single && $auto_generate_options['list']))) {
    $post = get_post($post_id);
    $summary = muki_ai_generate_summary($post->post_content, $post_id);
    if ($summary) {
      update_post_meta($post_id, 'muki_ai_summary', $summary);
    } else {
      // if cannot generate summary, return empty string instead of error message
      return '';
    }
  }

  if (!empty($summary)) {
    // Ensure the summary is wrapped in <p> tags
    $summary = '<div class="muki_ai_summary--excerpt"><p>' . str_replace("\n\n", "</p><p>", $summary) . '</p></div>';
    return wp_kses_post($summary);
  }

  // if no summary and auto-generate is not enabled, return empty string
  return '';
}

// Enqueue styles
function muki_ai_summary_enqueue_styles() {
  wp_enqueue_style('muki-ai-summary-style', plugin_dir_url(__FILE__) . 'src/css/muki-ai-summary-style.css');
}
add_action('wp_enqueue_scripts', 'muki_ai_summary_enqueue_styles');

// Error handling and logging
function muki_ai_log_error($message) {
  error_log('Muki AI Summary Error: ' . $message);
  update_option('muki_ai_last_error', $message);
}

// Use muki_ai_log_error function where appropriate, e.g.:
// muki_ai_log_error('OpenAI API call failed: ' . $error_message);

// Sanitize function
function muki_ai_sanitize_auto_generate($input) {
  $sanitized_input = array(
    'single' => isset($input['single']) ? 1 : 0,
    'list' => isset($input['list']) ? 1 : 0
  );
  return $sanitized_input;
}

// New AJAX handler for single post page
function muki_ai_generate_summary_for_single() {
  // check nonce
  if (!check_ajax_referer('muki_ai_summary_nonce', 'nonce', false)) {
    muki_ai_log_error('Invalid nonce in single post AJAX request');
    wp_send_json_error('Security check failed');
    return;
  }

  // check user permissions
  if (!current_user_can('edit_posts')) {
    muki_ai_log_error('Unauthorized user attempted to generate summary on single post');
    wp_send_json_error('Insufficient permissions');
    return;
  }

  $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
  
  // check post_id
  if (!$post_id || !get_post($post_id)) {
    muki_ai_log_error('Invalid post ID in single post request: ' . $post_id);
    wp_send_json_error('Invalid post ID');
    return;
  }

  $post = get_post($post_id);
  if (!$post) {
    wp_send_json_error('Post not found');
  }

  $summary = muki_ai_generate_summary($post->post_content, $post_id, true);
  if ($summary) {
    update_post_meta($post_id, 'muki_ai_summary', wp_kses_post($summary));
    $summary_title = get_option('muki_ai_summary_title', 'AI Generated Summary');
    $summary_html = '<h4 class="muki-ai-summary-title">' . esc_html($summary_title) . '</h4><p>' . str_replace("\n\n", "</p><p>", $summary) . '</p>';
    wp_send_json_success($summary_html);
  } else {
    $error = get_option('muki_ai_last_error', 'Unknown error');
    wp_send_json_error('Failed to generate summary: ' . $error);
  }
}
add_action('wp_ajax_muki_ai_generate_summary_for_single', 'muki_ai_generate_summary_for_single');
add_action('wp_ajax_nopriv_muki_ai_generate_summary_for_single', 'muki_ai_generate_summary_for_single');

// clear all summaries
function muki_ai_clean_all_summaries() {
  // check user permissions
  if (!current_user_can('manage_options')) {
    muki_ai_log_error('Unauthorized user attempted to clean all summaries');
    return;
  }

  global $wpdb;
  $wpdb->delete($wpdb->postmeta, array('meta_key' => 'muki_ai_summary'));
  $wpdb->delete($wpdb->postmeta, array('meta_key' => 'muki_ai_summary_meta'));
  delete_option('muki_ai_last_error');
}

function muki_ai_summary_activate() {
  $default_auto_generate = array(
    'single' => false,
    'list' => false
  );
  add_option('muki_ai_summary_auto_generate', $default_auto_generate);
}
register_activation_hook(__FILE__, 'muki_ai_summary_activate');

// Load text domain
function muki_ai_summary_load_textdomain() {
  load_plugin_textdomain(
    'muki-ai-summary',
    false,
    dirname(plugin_basename(__FILE__)) . '/languages'
  );
}
add_action('plugins_loaded', 'muki_ai_summary_load_textdomain');
