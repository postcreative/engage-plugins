<?php

/* ----------------------------------------
* Create post type
----------------------------------------- */

function ewd_create_post_type_donation() 
{
	global $ewd_options;
	
	if( $ewd_options['ewd_donation_slug'] != '') {
		$item_slug = $ewd_options['ewd_donation_slug'];
	} else {
		$item_slug = 'donation';
	}

	$labels = array(
		'name'               => esc_html__('Donations','ewd'),
		'singular_name'      => esc_html__('Donation','ewd' ),
		'add_new'            => esc_html__('Add New','ewd'),
		'add_new_item'       => esc_html__('Add New Donation','ewd'),
		'edit_item'          => esc_html__('Edit Donation','ewd'),
		'new_item'           => esc_html__('New Donation','ewd'),
		'view_item'          => esc_html__('View Donation','ewd'),
		'search_items'       => esc_html__('Search Donation','ewd'),
		'not_found'          => esc_html__('No donation found','ewd'),
		'not_found_in_trash' => esc_html__('No donation found in trash','ewd'), 
		'parent_item_colon'  => ''
	);
	
	// Enable or disable donor picture
	if( isset($ewd_options['ewd_donation_enable_donor_picture']) AND $ewd_options['ewd_donation_enable_donor_picture'] == 'on') {
		$supports = array('title', 'editor', 'thumbnail');
	} else {
		$supports = array('title', 'editor');
	}

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true, 
		'query_var'          => true,
		'can_export'         => true,
		'rewrite'            => array('slug' => $item_slug,'with_front' => true),
		'capability_type'    => 'post',
		'hierarchical'       => false,
		'supports'           => $supports,
		'menu_icon'          => EWD_PLUGIN_URL . '/includes/images/cpt_icon_donation.png',
	); 

	register_post_type( 'donation' , $args );

}

add_action('init', 'ewd_create_post_type_donation');