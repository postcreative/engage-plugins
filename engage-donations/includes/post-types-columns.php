<?php

/* ----------------------------------------
* Edit columns
----------------------------------------- */

function ewd_edit_columns_donation($columns)
{
	global $ewd_options;
	
	// Columns if enable donor picture
	if( isset($ewd_options['ewd_donation_enable_donor_picture']) AND $ewd_options['ewd_donation_enable_donor_picture'] == 'on') {
	
		$newcolumns = array(
			"cb"                 => "<input type=\"checkbox\" />",
			"donation_thumbnail" => esc_html__('Donor Picture', 'ewd'),
			"title"              => esc_html__('Title', 'ewd'),
			"taxonomy-donation-types"    => esc_html__('Project', 'ewd'),
			"donation_amount"    => esc_html__('Amount', 'ewd'),
			"donation_recurring" => esc_html__('Recurring', 'ewd'),
			"donation_donor"     => esc_html__('Donor', 'ewd')
		);
	
	} else {
	
		$newcolumns = array(
			"cb"                => "<input type=\"checkbox\" />",
			"title"             => esc_html__('Title', 'ewd'),
			"taxonomy-donation-types"    => esc_html__('Project', 'ewd'),
			"donation_amount"   => esc_html__('Amount', 'ewd'),
			"donation_recurring" => esc_html__('Recurring', 'ewd'),
			"donation_donor"    => esc_html__('Donor', 'ewd')
		);

	}
	
	$columns = array_merge($newcolumns, $columns);
	
	return $columns;
}
add_filter("manage_edit-donation_columns", "ewd_edit_columns_donation");



/* ----------------------------------------
* Populate columns
----------------------------------------- */

function ewd_custom_columns_donation($column)
{
	global $post, $ewd_options;
	
	// donation data
	$donation_amount = get_meta_option('ewd_amount');
	$donation_donor  = $post->post_author;
	$donation_recurring = get_meta_option('ewd_recurring');
	
	// donor data
	$user_info = get_userdata($donation_donor);

	switch ($column)
	{

		case "donation_amount":
			if( $post->post_status == 'draft' ) 
			{
				if($donation_amount) { 
					echo '<s>'.ewd_display_money($donation_amount).'</s>'; 
				}
			} else {
				if($donation_amount) { 
					echo ewd_display_money($donation_amount); 
				}
			}
		break;

		case "donation_donor":
			if($donation_donor) { echo ucfirst($user_info->user_firstname).' '.strtoupper($user_info->user_lastname); }
		break;
		
		case "donation_recurring":
			if( $donation_recurring ) { 
				echo ewd_get_recurring_payment_translatable_period( $donation_recurring );
			} else { 
				echo _e('No', 'ewd'); 
			}
		break;

		case "donation_thumbnail":
		 if ( has_post_thumbnail() ) { the_post_thumbnail('ewd_donor_profile_mini'); }
		break;	
	}
}
add_action("manage_posts_custom_column",  "ewd_custom_columns_donation");