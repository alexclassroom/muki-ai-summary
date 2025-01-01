jQuery(document).ready(function ($) {
  if (typeof mukiAiSummary === 'undefined') {
    console.error('mukiAiSummary is not defined. Check if wp_localize_script is called correctly.');
    return;
  }

  const loadingDiv = $('.muki-ai-summary--loading');
  if (loadingDiv.length > 0 && mukiAiSummary.is_single && mukiAiSummary.auto_generate_single) {
    const postId = loadingDiv.data('post-id');
    if (postId) {
      generateSummary(postId);
    }
  }

  function generateSummary(postId) {
    $.ajax({
      url: mukiAiSummary.ajax_url,
      type: 'POST',
      data: {
        action: 'muki_ai_generate_summary_for_single',
        post_id: postId,
        nonce: mukiAiSummary.nonce
      },
      beforeSend: function () {
        console.log('開始發送請求...');
      },
      success: function (response) {
        if (response.success) {
          loadingDiv.html(response.data);
          loadingDiv.removeClass('muki-ai-summary--loading');
        } else {
          loadingDiv.hide();
          console.error('Failed to generate summary:', response.data);
        }
      },
      error: function (xhr, status, error) {
        loadingDiv.hide();
        console.error('AJAX error:', status, error);
      }
    });
  }
});
