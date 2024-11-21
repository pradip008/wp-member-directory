jQuery(document).ready(function($) {
    $('#member-search-form').on('submit', function(e) {
        e.preventDefault();  // Prevent form submission
        
        // Get the search term and selected category
        var search_term = $('#member-search-text').val();
        var category_id = $('#member-category').val();

        // Make the AJAX request using POST method
        $.ajax({
            url: ajaxurl, // WordPress's AJAX handler URL
            type: 'POST',
            data: {
                action: 'member_search', // Custom action name for the AJAX handler
                s: search_term,          // Search term
                category: category_id,   // Selected category
            },
            success: function(response) {
                // Update the member directory (same container as shortcode) with the AJAX response
                $('.member-directory').html(response);
            },
            error: function(xhr, status, error) {
                // Error handling
                $('.member-directory').html('<p>An error occurred while processing your request. Please try again.</p>');
            }
        });
    });
});
