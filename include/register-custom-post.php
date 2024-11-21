<?php

// Register Custom Post Type "Member"
function register_member_cpt() {
    $labels = array(
        'name'                  => 'Members',
        'singular_name'         => 'Member',
        'menu_name'             => 'Members',
        'name_admin_bar'        => 'Member',
        'add_new'               => 'Add New',
        'add_new_item'          => 'Add New Member',
        'new_item'              => 'New Member',
        'edit_item'             => 'Edit Member',
        'view_item'             => 'View Member',
        'all_items'             => 'All Members',
        'search_items'          => 'Search Members',
        'not_found'             => 'No members found.',
        'not_found_in_trash'    => 'No members found in Trash.',
        'parent_item_colon'     => '',
        'all_items'             => 'All Members',
    );

    $args = array(
        'label'                 => 'Members',
        'description'           => 'Custom post type for members.',
        'labels'                => $labels,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'rewrite'               => array('slug' => 'members'),
        'capability_type'       => 'post',
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt'),
        'taxonomies'            => array('member-category'), // Associate taxonomy
        'show_in_rest'          => true,  // For Gutenberg support
    );

    register_post_type('member', $args);
}
add_action('init', 'register_member_cpt');

// Register Custom Taxonomy "Member-Category" for the "Member" CPT
function register_member_category_taxonomy() {
    $labels = array(
        'name'                       => 'Member Categories',
        'singular_name'              => 'Member Category',
        'search_items'               => 'Search Member Categories',
        'all_items'                  => 'All Member Categories',
        'parent_item'                => 'Parent Member Category',
        'parent_item_colon'          => 'Parent Member Category:',
        'edit_item'                  => 'Edit Member Category',
        'update_item'                => 'Update Member Category',
        'add_new_item'               => 'Add New Member Category',
        'new_item_name'              => 'New Member Category Name',
        'menu_name'                  => 'Member Categories',
    );

    $args = array(
        'hierarchical'              => true,  // Set to true for category-like taxonomy (can have parent/child)
        'labels'                    => $labels,
        'show_ui'                   => true,
        'show_admin_column'         => true,
        'show_in_rest'              => true,  // For Gutenberg and REST API support
        'rewrite'                   => array('slug' => 'member-category'),
    );

    register_taxonomy('member-category', array('member'), $args);
}
add_action('init', 'register_member_category_taxonomy');
