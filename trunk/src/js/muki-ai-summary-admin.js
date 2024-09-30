if (typeof mukiAiSummary === 'undefined') {
  console.error('mukiAiSummary is not defined. Check if wp_localize_script is called correctly.');
}

jQuery(document).ready(function ($) {

  var generateButton = $('#muki-ai-generate-summary');
  var resultDiv = $('#muki-ai-summary-result');

  function generateSummary() {
    var postId = $('#post_ID').val();

    $.ajax({
      url: mukiAiSummary.ajax_url,
      type: 'POST',
      data: {
        action: 'muki_ai_generate_summary',
        post_id: postId,
        nonce: mukiAiSummary.nonce
      },
      beforeSend: function () {
        generateButton.prop('disabled', true).text('Generating...');
        resultDiv.html('<p>Generating summary, please wait...</p>');
      },
      success: function (response) {
        if (response.success) {
          resultDiv.html('<h4>AI Summary:</h4><p>' + response.data + '</p>');
        } else {
          resultDiv.html('<p class="error">Generation failed: ' + response.data + '</p>');
        }
      },
      error: function (xhr, status, error) {
        console.error('AJAX error:', status, error);
        console.log('XHR object:', xhr);
        resultDiv.html('<p class="error">An error occurred, please try again later.</p>');
      },
      complete: function () {
        console.log('AJAX request completed');
        generateButton.prop('disabled', false).text('Generate AI Summary');
      }
    });
  }

  generateButton.on('click', function (e) {
    e.preventDefault();
    generateSummary();
  });
});
