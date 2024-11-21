<?php
/*
 * Plugin Name:       Member Directory
 * Description:       Filter Member
 * Version:           1.10.3
 * Requires at least: 5.2
 * Requires PHP:      7.2

 */


 // Hook into WordPress to enqueue styles and scripts
 function wp_member_directory_enqueue_assets() {
     // Enqueue the plugin's CSS file
     wp_enqueue_style(
         'wp-member-directory-style', // Handle for the CSS file
         plugin_dir_url(__FILE__) . 'style.css', // Path to the CSS file (adjust path if necessary)
         array(), // Dependencies (if any)
         '1.0', // Version number
         'all' // Media (all, print, screen, etc.)
     );
 
     // Enqueue the plugin's JS file
     wp_enqueue_script(
         'wp-member-directory-ajax', // Handle for the JS file
         plugin_dir_url(__FILE__) . 'ajax.js', // Path to the JS file (adjust path if necessary)
         array('jquery'), // Dependencies (in this case, jQuery)
         '1.0', // Version number
         true // Load in footer (true for better performance)
     );
 
     // Pass the localized `ajaxurl` to JavaScript (necessary for AJAX)
     wp_localize_script('wp-member-directory-ajax', 'ajaxurl', admin_url('admin-ajax.php'));
 }
 
 // Hook the function to the 'wp_enqueue_scripts' action
 add_action('wp_enqueue_scripts', 'wp_member_directory_enqueue_assets');

// Function to enqueue Font Awesome CSS
function wp_member_directory_enqueue_styles() {
    // Enqueue Font Awesome from CDN
    wp_enqueue_style(
        'font-awesome',  // Handle (unique name for this stylesheet)
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', // Font Awesome CDN URL
        array(), // Dependencies (none in this case)
        null, // Version (optional, set it to 'null' if you don't want to specify a version)
        'all' // Media type
    );
}

// Hook to enqueue Font Awesome stylesheet on the frontend
add_action('wp_enqueue_scripts', 'wp_member_directory_enqueue_styles');

 require( plugin_dir_path(__FILE__) . '/include/member-custom-field.php' );
 require( plugin_dir_path(__FILE__) . '/include/register-custom-post.php' );


 


//only presidents category show
function presidents_circle_shortcode($atts) {
    // Shortcode attributes
    $atts = shortcode_atts(array(
        'posts_per_page' => 10,  // Default number of posts per page
        'category' => '',  // Category filter (if any)
        'search' => '',  // Search term filter
    ), $atts, 'member_directory');

    // Query arguments
    $args = array(
        'post_type' => 'member',  // Your custom post type "member"
        'posts_per_page' => $atts['posts_per_page'],
        'post_status' => 'publish',
    );

    // Filter by the "President's Circle" category (assuming you know the slug)
    $presidents_circle_slug = 'presidents-circle';  // Replace with the actual slug if different

    // Adding the tax query for President's Circle category
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'member-category',  // Custom taxonomy "member-category"
            'field'    => 'slug',  // Searching by slug
            'terms'    => $presidents_circle_slug,
            'operator' => 'IN',
        ),
    );

    // Optional: Apply search term if provided
    if (!empty($atts['search'])) {
        $args['s'] = $atts['search'];  // Search by keyword
    }

    // The query
    $query = new WP_Query($args);

    // Output the member list
    ob_start();

    if ($query->have_posts()) { ?>
    <div class="president-member-directory">
    <?php   
    while ($query->have_posts()) {
            $query->the_post();
            $categories = get_the_terms(get_the_ID(), 'member-category');
            $image = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'); // Adjust image size if needed

            $member_url = get_post_meta(get_the_ID(), 'member-url', true);

            $instagram_url = get_post_meta(get_the_ID(), 'instagram-url', true);
            $facebook_url = get_post_meta(get_the_ID(), 'facebook-url', true);
            $linkedin_url = get_post_meta(get_the_ID(), 'linkedin-url', true);
            $website_url = get_post_meta(get_the_ID(), 'website-url', true);
            ?>
            <div class="member">
                <div class="member-div">
                    <div class="member-inner2-mobile radius-img">
                    <?php if ($image): ?>
                            <img src="<?php echo esc_url($image); ?>" alt="<?php the_title(); ?>">
                        <?php endif; ?>
                    </div> 
                    <div class="member-inner1">
                        <h2><?php the_title(); ?></h2>
                        <p class="description"><?php the_content(); ?></p>
                        <?php if ($categories): ?>
                            <p class='category'>Category: <?php echo esc_html($categories[0]->name); ?></p>
                        <?php endif; ?>
                        <div class='member-url'>
                            <a href='<?php echo esc_url($member_url); ?>' target="_blank">Visit Profile</a> 
                        </div>
                    </div> 
                
                    <div class="member-inner2 radius-img">
                        <?php if ($image): ?>
                            <img src="<?php echo esc_url($image); ?>" alt="<?php the_title(); ?>">
                        <?php endif; ?>
                        <div class='social-link'>
                            <a href='<?php echo esc_url($instagram_url); ?>' target='_blank'><i class="fab fa-instagram"></i></a>
                            <a href='<?php echo esc_url($facebook_url); ?>' target='_blank'><i class="fab fa-facebook-square"></i></a>
                            <a href='<?php echo esc_url($linkedin_url); ?>' target='_blank'><i class="fab fa-linkedin"></i></a>
                            <a href='<?php echo esc_url($website_url); ?>' target='_blank'><i class="fas fa-globe-americas"></i></a>
                        
                        </div>    
                    </div>    
                    <span class="premium-member-label">Presidents Circle</span>
                
                </div>
            
            </div>
            <?php
        }?> 
        </div>
        <?php
    } 
    else {
        echo '<p class="notfounddata">No members found in the President\'s Circle category.</p>';
    }

    // Reset post data
    wp_reset_postdata();

    return ob_get_clean();
}
add_shortcode('member_presidents_circle_shortcode', 'presidents_circle_shortcode');



 //search form code
function member_search_form_shortcode() {
    // Get all member-category terms
    $categories = get_terms(array(
        'taxonomy' => 'member-category', // Custom taxonomy for "member"
        'hide_empty' => false,           // Include categories even if they have no posts
    ));

    // Get the term ID of "President's Circle"
    $presidents_circle = get_term_by('name', 'President\'s Circle', 'member-category');
    $presidents_circle_id = $presidents_circle ? $presidents_circle->term_id : '';

    // Remove "President's Circle" category from the categories list
    if ($presidents_circle_id) {
        $categories = array_filter($categories, function($category) use ($presidents_circle_id) {
            return $category->term_id !== $presidents_circle_id;
        });
    }

    ob_start();
    ?>
    <form id="member-search-form" action="" method="POST"> <!-- Change GET to POST -->
        <input type="text" name="s" id="member-search-text" placeholder="Search Members" value="<?php echo isset($_POST['s']) ? esc_attr($_POST['s']) : ''; ?>">

        <select name="member-category" id="member-category">
            <option value="">Select Category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo esc_attr($category->term_id); ?>" <?php selected( isset($_POST['member-category']) ? $_POST['member-category'] : '', $category->term_id ); ?>>
                    <?php echo esc_html($category->name); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" id="search-btn">Search</button>
    </form>

    <div id="member-results">
        <!-- Member results will be loaded here dynamically -->
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('member_search_form', 'member_search_form_shortcode');


 // Shortcode for displaying the list of members with search and category filter
function member_directory_shortcode($atts) {
    // Shortcode attributes
    $atts = shortcode_atts(array(
        'posts_per_page' => 10,  // Default number of posts per page
        'category' => '',        // Category filter (if any)
        'search' => '',          // Search term filter
    ), $atts, 'member_directory');

    // Retrieve search and category filter values from the current POST request
    if (isset($_POST['s'])) {
        $atts['search'] = sanitize_text_field($_POST['s']);
    }
    if (isset($_POST['member-category'])) {
        $atts['category'] = intval($_POST['member-category']);
    }

    // Get the ID of the "President's Circle" category
    $presidents_circle = get_term_by('name', 'President\'s Circle', 'member-category');
    $presidents_circle_id = $presidents_circle ? $presidents_circle->term_id : '';

    // Query arguments
    $args = array(
        'post_type' => 'member',  // Your custom post type "member"
        'posts_per_page' => $atts['posts_per_page'],
        'post_status' => 'publish',
    );

    // Apply category filter if provided
    if (!empty($atts['category'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'member-category',  // Custom taxonomy "member-category"
                'field' => 'id',
                'terms' => $atts['category'],
            ),
        );
    }

    // Exclude the "President's Circle" category
    if ($presidents_circle_id) {
        if (!isset($args['tax_query'])) {
            $args['tax_query'] = array();
        }
        $args['tax_query'][] = array(
            'taxonomy' => 'member-category',
            'field' => 'id',
            'terms' => $presidents_circle_id,
            'operator' => 'NOT IN',  // Exclude this category
        );
    }

    // Apply search filter if provided
    if (!empty($atts['search'])) {
        $args['s'] = $atts['search'];  // Search by keyword
    }

    // Perform the query
    $query = new WP_Query($args);

    // Output the member list
    ob_start();

    if ($query->have_posts()) {
        ?>
        <div class="member-directory">
        <?php
        while ($query->have_posts()) {
            $query->the_post();
            $categories = get_the_terms(get_the_ID(), 'member-category');
            $image = get_the_post_thumbnail_url(get_the_ID(), 'full'); // Adjust image size if needed
            $member_url = get_post_meta(get_the_ID(), 'member-url', true);
            ?>
            <div class="member">
                <div class="member-div">
                        <div class="member-inner2-mobile">
                        <?php if ($image): ?>
                                <img src="<?php echo esc_url($image); ?>" alt="<?php the_title(); ?>">
                            <?php endif; ?>
                        </div> 
                    <div class="member-inner1">
                        <h3><?php the_title(); ?></h3>
                        <p><?php the_excerpt(); ?></p>
                        <?php if ($categories): ?>
                        <p class='category'>Category: <?php echo esc_html($categories[0]->name); ?></p>
                        <?php endif; ?>
                        <div class='member-url'>
                            <a href='<?php echo esc_url($member_url); ?>' target="_blank">Visit Profile</a> 
                        </div>
                    </div>
                    <div class="member-inner2">
                        <?php if ($image): ?>
                            <img src="<?php echo esc_url($image); ?>" alt="<?php the_title(); ?>">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
        </div>
        <?php
    } else {
        echo '<p class="notfounddata">No members found.</p>';
    }

    // Reset post data
    wp_reset_postdata();

    return ob_get_clean();
}
add_shortcode('member_directory', 'member_directory_shortcode');



// Handle AJAX request to search members
function handle_member_search_ajax() {
    // Ensure we're getting data via POST
    if (isset($_POST['s']) && isset($_POST['category'])) {
        $search_term = sanitize_text_field($_POST['s']);
        $category_id = intval($_POST['category']);
    } else {
        $search_term = '';
        $category_id = '';
    }

    // Get the ID of the "President's Circle" category
    $presidents_circle = get_term_by('name', 'President\'s Circle', 'member-category');
    $presidents_circle_id = $presidents_circle ? $presidents_circle->term_id : '';

    // Query arguments
    $args = array(
        'post_type' => 'member',  // Your custom post type "member"
        'posts_per_page' => -1,   // Get all matching posts (or set a limit)
        'post_status' => 'publish',
    );

    // Exclude the "President's Circle" category
    if ($presidents_circle_id) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'member-category',
                'field' => 'id',
                'terms' => $presidents_circle_id,
                'operator' => 'NOT IN',  // Exclude this category
            ),
        );
    }

    // Apply search filter if provided
    if (!empty($search_term)) {
        $args['s'] = $search_term;
    }

    if (!empty($category_id)) {
        $args['tax_query'][] = array(
            'taxonomy' => 'member-category',  // Custom taxonomy "member-category"
            'field' => 'id',
            'terms' => $category_id,
        );
    }

    // Perform the query
    $query = new WP_Query($args);

    // Check if there are any results
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $categories = get_the_terms(get_the_ID(), 'member-category');
            $image = get_the_post_thumbnail_url(get_the_ID(), 'full');
            $member_url = get_post_meta(get_the_ID(), 'member-url', true);
            ?>
            <div class="member">
                <div class="member-div">
                        <div class="member-inner2-mobile">
                            <?php if ($image): ?>
                                <img src="<?php echo esc_url($image); ?>" alt="<?php the_title(); ?>">
                            <?php endif; ?>
                        </div> 
                    <div class="member-inner1">
                        <h3><?php the_title(); ?></h3>
                        <p><?php the_excerpt(); ?></p>
                        <?php if ($categories): ?>
                        <p class='category'>Category: <?php echo esc_html($categories[0]->name); ?></p>
                        <?php endif; ?>
                        <div class='member-url'>
                            <a href='<?php echo esc_url($member_url); ?>' target="_blank">Visit Profile</a> 
                        </div>
                    </div>
                    <div class="member-inner2">
                        <?php if ($image): ?>
                            <img src="<?php echo esc_url($image); ?>" alt="<?php the_title(); ?>">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        echo '<p class="notfounddata" >Members Not Found.</p>';
    }

    // Reset post data
    wp_reset_postdata();

    // Always die in the end
    die();
}
add_action('wp_ajax_member_search', 'handle_member_search_ajax');
add_action('wp_ajax_nopriv_member_search', 'handle_member_search_ajax');









