jQuery(document).ready(function ($) {
  if (typeof mukiAiSummary === 'undefined') {
    console.error('mukiAiSummary is not defined. Check if wp_localize_script is called correctly.');
    return;
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
      success: function (response) {
        var $summaryContainer = $('.muki-ai-summary--loading[data-post-id="' + postId + '"]');
        if (response.success) {
          // 更新內容而不是替換整個元素
          $summaryContainer.removeClass('muki-ai-summary--loading').addClass('muki-ai-summary');
          $summaryContainer.html(response.data);
        } else {
          $summaryContainer.html('<p class="error">Failed to generate summary: ' + response.data + '</p>');
        }
      },
      error: function () {
        $('.muki-ai-summary--loading[data-post-id="' + postId + '"]').html('<p class="error">An error occurred. Please try again later.</p>');
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
      generateSummary(postId);
    } else {
      console.error('Unable to find post ID on the page');
    }
  } else if (!mukiAiSummary.is_single && mukiAiSummary.auto_generate_list) {
    $('[id^="post-"]').each(function () {
      var postId = $(this).attr('id').split('-')[1];
      if (postId) {
        generateSummary(postId);
      } else {
        console.error('Unable to extract post ID from element');
      }
    });
  }
});
