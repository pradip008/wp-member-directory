<?php
// Register the meta box for member-url
function add_member_url_meta_box() {
    add_meta_box(
        'member_url_meta_box',         // ID of the meta box
        'Member URL',                  // Title of the meta box
        'display_member_url_meta_box', // Callback function to display the meta box content
        'member',                      // Post type (your custom post type)
        'normal',                      // Where to display (normal = in the main content area)
        'high'                         // Priority of the meta box
    );
}
add_action('add_meta_boxes', 'add_member_url_meta_box');

// Display the meta box content
function display_member_url_meta_box($post) {
    // Get the current value of the member-url field
    $member_url = get_post_meta($post->ID, 'member-url', true);
    ?>
    <label for="member-url">Enter Member URL:</label>
    <input type="url" id="member-url" name="member-url" value="<?php echo esc_url($member_url); ?>" class="widefat" />
    <p class="description">Enter the URL for the member's profile or website.</p>
    <?php
}


// Save the member-url custom field when the post is saved
function save_member_url_meta($post_id) {
    // Check if we are saving a member post
    if (get_post_type($post_id) != 'member') {
        return $post_id;
    }

    // Verify nonce for security
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // Check if the current user has permission to edit the post
    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    // Save the member-url custom field
    if (isset($_POST['member-url'])) {
        $member_url = sanitize_text_field($_POST['member-url']);
        update_post_meta($post_id, 'member-url', $member_url);
    }
}
add_action('save_post', 'save_member_url_meta');



function add_member_social_meta_boxes() {
    // Add meta box for each social media link
    add_meta_box('member_instagram_meta_box', 'Instagram URL', 'display_member_instagram_meta_box', 'member', 'normal', 'high');
    add_meta_box('member_facebook_meta_box', 'Facebook URL', 'display_member_facebook_meta_box', 'member', 'normal', 'high');
    add_meta_box('member_linkedin_meta_box', 'LinkedIn URL', 'display_member_linkedin_meta_box', 'member', 'normal', 'high');
    add_meta_box('member_website_meta_box', 'Website URL', 'display_member_website_meta_box', 'member', 'normal', 'high');
}
add_action('add_meta_boxes', 'add_member_social_meta_boxes');


// Instagram URL
function display_member_instagram_meta_box($post) {
    $instagram_url = get_post_meta($post->ID, 'instagram-url', true);
    ?>
    <label for="instagram-url">Enter Instagram URL:</label>
    <input type="url" id="instagram-url" name="instagram-url" value="<?php echo esc_url($instagram_url); ?>" class="widefat" />
    <p class="description">Enter the member's Instagram profile URL.</p>
    <?php
}

// Facebook URL
function display_member_facebook_meta_box($post) {
    $facebook_url = get_post_meta($post->ID, 'facebook-url', true);
    ?>
    <label for="facebook-url">Enter Facebook URL:</label>
    <input type="url" id="facebook-url" name="facebook-url" value="<?php echo esc_url($facebook_url); ?>" class="widefat" />
    <p class="description">Enter the member's Facebook profile URL.</p>
    <?php
}

// LinkedIn URL
function display_member_linkedin_meta_box($post) {
    $linkedin_url = get_post_meta($post->ID, 'linkedin-url', true);
    ?>
    <label for="linkedin-url">Enter LinkedIn URL:</label>
    <input type="url" id="linkedin-url" name="linkedin-url" value="<?php echo esc_url($linkedin_url); ?>" class="widefat" />
    <p class="description">Enter the member's LinkedIn profile URL.</p>
    <?php
}

// Website URL
function display_member_website_meta_box($post) {
    $website_url = get_post_meta($post->ID, 'website-url', true);
    ?>
    <label for="website-url">Enter Website URL:</label>
    <input type="url" id="website-url" name="website-url" value="<?php echo esc_url($website_url); ?>" class="widefat" />
    <p class="description">Enter the member's personal or business website URL.</p>
    <?php
}


function save_member_social_meta($post_id) {
    // Check if we are saving a member post
    if (get_post_type($post_id) != 'member') {
        return $post_id;
    }

    // Verify nonce for security
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // Check if the current user has permission to edit the post
    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    // Save Instagram URL
    if (isset($_POST['instagram-url'])) {
        $instagram_url = sanitize_text_field($_POST['instagram-url']);
        update_post_meta($post_id, 'instagram-url', $instagram_url);
    }

    // Save Facebook URL
    if (isset($_POST['facebook-url'])) {
        $facebook_url = sanitize_text_field($_POST['facebook-url']);
        update_post_meta($post_id, 'facebook-url', $facebook_url);
    }

    // Save LinkedIn URL
    if (isset($_POST['linkedin-url'])) {
        $linkedin_url = sanitize_text_field($_POST['linkedin-url']);
        update_post_meta($post_id, 'linkedin-url', $linkedin_url);
    }

    // Save Website URL
    if (isset($_POST['website-url'])) {
        $website_url = sanitize_text_field($_POST['website-url']);
        update_post_meta($post_id, 'website-url', $website_url);
    }
}
add_action('save_post', 'save_member_social_meta');

