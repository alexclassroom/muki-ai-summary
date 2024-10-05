jQuery(document).ready(function ($) {
  if (typeof mukiAiSummary === 'undefined') {
    console.error('mukiAiSummary is not defined. Check if wp_localize_script is called correctly.');
    return;
  }

  function generateSummary(postId, element) {
    // 檢查是否已經有摘要內容（不只是檢查元素）
    if ($(element).find('.muki-ai-summary').text().trim().length > 0) {
      return; // 如果已經有摘要內容，不再生成
    }

    // 檢查是否正在加載
    if ($(element).find('.muki-ai-summary--loading').length > 0) {
      return; // 如果正在加載，不重複請求
    }

    // 添加加載指示器
    $(element).prepend('<div class="muki-ai-summary--loading">正在生成 AI 摘要...</div>');

    $.ajax({
      url: mukiAiSummary.ajax_url,
      type: 'POST',
      data: {
        action: 'muki_ai_generate_summary_for_single',
        post_id: postId,
        nonce: mukiAiSummary.nonce
      },
      success: function (response) {
        if (response.success) {
          $('.muki-ai-summary--loading').replaceWith(response.data);
        } else {
          $('.muki-ai-summary--loading').hide();
          console.error('Failed to generate summary:', response.data);
        }
      },
      error: function (xhr, status, error) {
        $('.muki-ai-summary--loading').hide();
        console.error('AJAX error:', status, error);
      }
    });
  }

  function findPostId() {
    var postElement = $('[id^="post-"]').first();
    if (postElement.length) {
      return postElement.attr('id').split('-')[1];
    }
    return null;
  }

  if (mukiAiSummary.is_single && mukiAiSummary.auto_generate_single) {
    var postId = findPostId();
    if (postId) {
      var $article = $('#post-' + postId);
      generateSummary(postId, $article);
    }
  } else if (!mukiAiSummary.is_single && mukiAiSummary.auto_generate_list) {
    $('[id^="post-"]').each(function () {
      var postId = $(this).attr('id').split('-')[1];
      generateSummary(postId, this);
    });
  }
});
