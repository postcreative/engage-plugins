<?php

/**
 * Setup Taxonomies
 *
 * Registers the custom taxonomies.
 *
 * @access      private
 * @since       1.0
 * @return      void
*/

function ewd_setup_taxonomies() {

	$slug = 'donations';
	if( defined( 'EWD_ISSUE_SLUG' ) ) {
		$slug = EWD_ISSUE_SLUG;
	}

	/*  Projects Taxonomy */
	
	$projects_labels = array(
		'name' 				=> _x( 'Projects', 'taxonomy general name', 'ewd' ),
		'singular_name' 	=> _x( 'Project', 'taxonomy singular name', 'ewd' ),
		'search_items' 		=> __( 'Search Projects', 'ewd'  ),
		'all_items' 		=> __( 'All Projects', 'ewd'  ),
		'parent_item' 		=> __( 'Parent Project', 'ewd'  ),
		'parent_item_colon' => __( 'Parent Project:', 'ewd'  ),
		'edit_item' 		=> __( 'Edit Project', 'ewd'  ),
		'update_item' 		=> __( 'Update Project', 'ewd'  ),
		'add_new_item' 		=> __( 'Add New Project', 'ewd'  ),
		'new_item_name' 	=> __( 'New Project Name', 'ewd'  ),
		'menu_name' 		=> __( 'Projects', 'ewd'  ),
	);

	$projects_args = apply_filters( 'ewd_projects_args', array(
			'hierarchical' 	=> true,
			'labels' 		=> apply_filters('ewd_projects_labels', $projects_labels),
			'show_ui' 		=> true,
			'show_in_nav_menus' => true,
			'query_var' 	=> 'projects',
			'show_admin_column' => true,
			'rewrite' 		=> array('slug' => $slug . '/projects', 'with_front' => false, 'hierarchical' => true )
		)
	);

	register_taxonomy( 'donation-types', array('donation'), $projects_args );

}
add_action( 'init', 'ewd_setup_taxonomies', 10 );

