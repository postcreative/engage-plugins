<?php

/* ----------------------------------------
* Add metaboxes
----------------------------------------- */

function ewd_add_donation_meta_boxes() {
    add_meta_box(
		'donation_main_meta_box',
		__('Donation Details', 'ewd'),
		'ewd_show_main_donation_meta_box',
		'donation',
		'normal',
		'high');
}
add_action('add_meta_boxes', 'ewd_add_donation_meta_boxes');

function ewd_add_donor_meta_boxes() {
    add_meta_box(
		'donation_side_meta_box',
		__('Donor Information', 'ewd'),
		'ewd_show_side_donation_meta_box',
		'donation',
		'side',
		'low');
}
add_action('add_meta_boxes', 'ewd_add_donor_meta_boxes');

/* ----------------------------------------
* Metabox fields array
----------------------------------------- */
$options = get_option('ewd_settings');

// Amount list
/*
if( $options['ewd_repeatable'] != '') {
	$amounts = array();
	foreach ($options['ewd_repeatable'] as $amount) {
		$add = array('label' => $amount, 'value' => $amount);
		array_push($amounts, $add);
	}
}
*/

$prefix = 'ewd_';
$ewd_meta_fields = array(
	array(
		'label' => __('Donor Name', 'ewd'),
		'desc'  => __('Choose the donor name.', 'ewd'),
		'id'    => $prefix.'donor',
		'type'  => 'users_list'
	),

	array(
	'label' => __('Amount', 'ewd'),
	'desc'  => __('Indicate the donation amount (ex: 20) in ', 'ewd').' '.$options['ewd_donation_currency_code'],
	'id'	=> $prefix.'amount',
	'type'	=> 'text'
	),
	array(
		'label' => __('Custom Message', 'ewd'),
		'desc'  => __('Write optional information about the donation or leave blank.', 'ewd'),
		'id'    => $prefix.'information',
		'type'  => 'textarea'
	),
	array(
		'label' => __('Hide donor name?', 'ewd'),
		'desc'  => __('Check to hide the donor name, and display donation as anonymous.', 'ewd'),
		'id'    => $prefix.'hide_donor',
		'type'  => 'checkbox'
	),
	array(
		'label' => __('Hide donation amount?', 'ewd'),
		'desc'  => __('Check to hide the donation amount.', 'ewd'),
		'id'    => $prefix.'hide_amount',
		'type'  => 'checkbox'
	)
);

/* ----------------------------------------
* Main Metabox Callback
----------------------------------------- */

function ewd_show_main_donation_meta_box() {

	global $ewd_meta_fields, $post, $ewd_options, $wpdb;
	
	$post_id = $post->ID;
	
	// Use nonce for verification
	echo '<input type="hidden" name="ewd_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';

	// Begin the field table and loop
	echo '<table class="form-table">';
	foreach ($ewd_meta_fields as $field) {
		// get value of this field if it exists for this post
		$meta 		= get_post_meta($post_id, $field['id'], true);
		
		// begin a table row with
		echo '<tr>
				<th><label for="'.$field['id'].'">'.$field['label'].'</label></th>
				<td>';
				switch($field['type']) {
					
					// text
					case 'text':
						echo '<input type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="30" />
							<br /><span class="description">'.$field['desc'].'</span>';
					break;
					// textarea
					case 'textarea':
						echo '<textarea name="'.$field['id'].'" id="'.$field['id'].'" cols="60" rows="4">'.$meta.'</textarea>
							<br /><span class="description">'.$field['desc'].'</span>';
					break;
					// checkbox
					case 'checkbox':
						if( $meta == 'on' ) { $checked = ' checked="checked"'; } else { $checked = ''; }
						echo '<input type="checkbox" name="'.$field['id'].'" id="'.$field['id'].'"'.$checked.'/>
							<label for="'.$field['id'].'">'.$field['desc'].'</label>';
					break;
					// select
					case 'select':
						echo '<select name="'.$field['id'].'" id="'.$field['id'].'">';
						foreach ($field['options'] as $option) {
							echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="'.$option['value'].'">'.$option['label'].'</option>';
						}
						echo '</select><br /><span class="description">'.$field['desc'].'</span>';
					break;
					// select
					case 'users_list':
						$donor_id  = $post->post_author;
						echo '<select name="'.$field['id'].'" id="'.$field['id'].'">';
						$wp_user_search = $wpdb->get_results("SELECT ID, display_name FROM $wpdb->users ORDER BY display_name");
							foreach ( $wp_user_search as $userid ) {
								$user_id       = (int) $userid->ID;
								$display_name  = stripslashes($userid->display_name);
								echo '<option', $donor_id == $user_id ? ' selected="selected"' : '', ' value="'.$user_id.'">'.$display_name.'</option>';
							}
						echo '</select><br /><span class="description">'.$field['desc'].'</span>';
					break;
				
				} //end switch
		echo '</td></tr>';
	} // end foreach
	echo '</table>'; // end table
}

/* ----------------------------------------
* Side Metabox Callback
----------------------------------------- */

function ewd_show_side_donation_meta_box() {

	global $post, $ewd_options, $wpdb;
	
	$post_status = $post->post_status;
	
	$post_id = $post->ID;
	
	// Author information
	$author_id      = $post->post_author;
	
	$user_info = get_userdata( $author_id );
	$author_email = $user_info->user_email;
	
	$author_phone   = get_user_meta( $author_id, 'phone', true);
	$author_address = get_user_meta( $author_id, 'address', true);
	$author_city    = get_user_meta( $author_id, 'city', true);
	$author_zipcode = get_user_meta( $author_id, 'zipcode', true);
	
	$author_state      = get_user_meta( $author_id, 'state', true);
	$author_occupation = get_user_meta( $author_id, 'occupation', true);
	$author_employer   = get_user_meta( $author_id, 'employer', true);
	
	$output = '';
	
	if( $author_email ) {
		$output .= '<p>';
		$output .= __('Email', 'ewd').': ';
		$output .= '<a href="mailto:'.$author_email.'">';
		$output .= $author_email;
		$output .= '</a></p>';
	}
	
	if( $author_phone ) {
		$output .= '<p>';
		$output .= __('Phone Number', 'ewd').': ';
		$output .= $author_phone;
		$output .= '</p>';
	}
	
	if( $author_address ) {
		$output .= '<p>';
		$output .= __('Address', 'ewd').': ';
		$output .= $author_address;
		$output .= '</p>';
	}
	
	if( $author_city ) {
		$output .= '<p>';
		$output .= __('City', 'ewd').': ';
		$output .= $author_city;
		$output .= '</p>';
	}
	
	if( $author_zipcode ) {
		$output .= '<p>';
		$output .= __('Zipcode', 'ewd').': ';
		$output .= $author_zipcode;
		$output .= '</p>';
	}
	
	if( $author_state ) {
		$output .= '<p>';
		$output .= __('State', 'ewd').': ';
		$output .= $author_state;
		$output .= '</p>';
	}
	
	if( $author_occupation ) {
		$output .= '<p>';
		$output .= __('Occupation', 'ewd').': ';
		$output .= $author_occupation;
		$output .= '</p>';
	}
	
	if( $author_employer ) {
		$output .= '<p>';
		$output .= __('Employer', 'ewd').': ';
		$output .= $author_employer;
		$output .= '</p>';
	}
	
	if( !$author_phone && !$author_address && !$author_city && !$author_zipcode && !$author_state && !$author_occupation && !$author_employer ) {
		_e( 'No extra information', 'ewd');
	} else {
		if( isset($post_status) && ( $post_status == 'publish' OR $post_status == 'draft' ) ) {
			echo $output;
		}
	}
}


/* ----------------------------------------
* Save Metabox data
----------------------------------------- */

function ewd_save_custom_meta($post_id) {
    
    global $ewd_meta_fields, $typenow;

	// verify nonce
	if ( isset($_POST['ewd_meta_box_nonce']) && !wp_verify_nonce($_POST['ewd_meta_box_nonce'], basename(__FILE__)))
		return $post_id;
	// check autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return $post_id;
	// check permissions
	if ( $typenow == 'donation') {
		if (!current_user_can('edit_page', $post_id))
			return $post_id;
		} elseif (!current_user_can('edit_post', $post_id)) {
			return $post_id;
	}

	// loop through fields and save the data
	foreach ($ewd_meta_fields as $field) {
		
		// Update meta fields
		if( get_post_meta( $post_id, $field['id'], true) != '' ) {
			$old = get_post_meta($post_id, $field['id'], true);
		} else {
			$old = '';
		}
		if( isset( $_POST[$field['id']] ) ) {
			$new = $_POST[$field['id']];
		}
		
		if( isset($new) ) {
			if ($new && $new != $old) {
				update_post_meta($post_id, $field['id'], $new);
			} elseif ('' == $new && $old) {
				delete_post_meta($post_id, $field['id'], $old);
			}
		}
		
	} // end foreach
	
}
add_action('save_post', 'ewd_save_custom_meta');  

