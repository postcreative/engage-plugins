<?php

/* ----------------------------------------
* Actions and filters for Custom Post Type: donation
----------------------------------------- */

add_action('init', 'ewd_create_post_type_donation');


// get post
if ( isset( $_GET['post'] ) )
 	$post_id = $post_ID = (int) $_GET['post'];
elseif ( isset( $_POST['post_ID'] ) )
 	$post_id = $post_ID = (int) $_POST['post_ID'];
else
 	$post_id = $post_ID = 0;

$post = $post_type = $post_type_object = null;

if ( $post_id )
	$post = get_post( $post_id );

if ( $post ) {
	$post_type = $post->post_type;
	$post_type_object = get_post_type_object( $post_type );
}

if( isset( $_REQUEST['post_id'] ) ) {
	if ( $post_type == 'donation' OR (get_post_type( $_REQUEST['post_id'] ) == 'donation') ) {
		$ewd_options = get_option('ewd_settings');
		if( isset($ewd_options['ewd_donation_enable_donor_picture']) AND $ewd_options['ewd_donation_enable_donor_picture'] == 'on') {
			add_filter( 'gettext', 'ewd_featured_image_box_texts', 9999, 4 );
		}
	}
}

/* ----------------------------------------
* Tweak feature image box texts
----------------------------------------- */

function ewd_featured_image_box_texts( $translation, $text, $domain ) {

	$translations = &get_translations_for_domain( $domain );
	
		switch ( $text) {
			case "Featured Image":
			    return $translations->translate( __('Donor Picture', 'ewd') );
			    break;
			case "Set featured image":
			    return $translations->translate( __('Set donor picture', 'ewd') );
			    break;
			case "Use as featured image":
			    return $translations->translate( __('Use as donor picture', 'ewd') );
			    break;
			case "Remove featured image":
			    return $translations->translate( __('Remove donor picture', 'ewd') );
			    break;
		}
	
	    return $translation;
}
