<?php

/**
 * Create custom columns for "donation-types" taxonomies
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function ewd_donation_types_columns( $columns ) {

    $newcolumns = array(
        'cb' 			=> '<input type  = "checkbox" />',
        'name'          => __('Name'),
        'target'        => __('Target', 'ewd'),
        'slug'          => __('Slug'),
        'posts'         => __('Donations', 'ewd')
        );
	
	return $newcolumns;
    
}
add_filter("manage_edit-donation-types_columns", 'ewd_donation_types_columns'); 

/**
 * Create custom columns for "donation-types" taxonomies
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function ewd_donation_types_columns_content( $out, $column_name, $post_id) {

	switch ($column_name) {
        case 'target':
        	$term_meta = get_option( "ewd_taxonomy_$post_id" ); 
            if( $term_meta['project_target'] != 0 ) {
            	$out .= ewd_display_money( $term_meta['project_target'] ); 
            } else {
            	$out .= '-'; 
            }
            break;
 
        default:
            break;
    }
    return $out;    
}
add_filter("manage_donation-types_custom_column", 'ewd_donation_types_columns_content', 10, 3); 