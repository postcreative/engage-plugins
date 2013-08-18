<?php

// Add button to visual editor
add_action('init', 'ewd_add_shortcode_button');
function ewd_add_shortcode_button(){

	if ( current_user_can('edit_posts') &&  current_user_can('edit_pages') ){  
		 add_filter('mce_external_plugins', 'ewd_add_shortcode_plugin');  
		 add_filter('mce_buttons_3', 'ewd_register_shortcode_button');  
	   }  	

}
function ewd_register_shortcode_button($buttons){
	array_push($buttons, "donationform", "space", "separator");
	array_push($buttons, "donationslist", "space", "separator");
	array_push($buttons, "donationstarget", "space", "separator");
	array_push($buttons, "donationscollectedfunds", "space", "separator");
	array_push($buttons, "donationsprogressbar", "space", "separator");

	return $buttons;
}
function ewd_add_shortcode_plugin($plugin_array) {  
   $plugin_array['donationform']            = EWD_PLUGIN_URL . '/includes/js/shortcodes/donationform.js';  
   $plugin_array['donationslist']           = EWD_PLUGIN_URL . '/includes/js/shortcodes/donationslist.js';  
   $plugin_array['donationstarget']         = EWD_PLUGIN_URL . '/includes/js/shortcodes/donationstarget.js';  
   $plugin_array['donationscollectedfunds'] = EWD_PLUGIN_URL . '/includes/js/shortcodes/donationscollectedfunds.js';  
   $plugin_array['donationsprogressbar']    = EWD_PLUGIN_URL . '/includes/js/shortcodes/donationsprogressbar.js';  
   return $plugin_array;  
}

/**
 * Create [donation_progress_bar] shortcode
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function ewd_donation_progress_bar_shortcode( $atts, $content = null ) {
	
	return ewd_create_progress_bar();
}

add_shortcode("donations_progress_bar", "ewd_donation_progress_bar_shortcode");

/**
 * Create [donation_collected_funds format="money/percent"] shortcode
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function ewd_donation_collected_funds_shortcode( $atts, $content = null ) {

	global $ewd_options;

	extract(shortcode_atts(array(
		"format" => ''
	), $atts));
	
	if( $format == 'percent' ) {
		$target          = $ewd_options['ewd_donation_target'];
		$total_donations = ewd_get_total_donations_amount();
		$percent         = ($total_donations*100)/$target;
		$collected_funds = number_format($percent, 2, '.', '').'%';
	} elseif( $format == 'money') {
		$collected_funds = ewd_display_money( ewd_get_total_donations_amount() );
	} else {
		$collected_funds = ewd_get_total_donations_amount();
	}
	
	return $collected_funds;
}

add_shortcode("donations_collected_funds", "ewd_donation_collected_funds_shortcode");

/**
 * Create [donation_target format="money"] shortcode
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function ewd_donation_target_shortcode( $atts, $content = null ) {

	global $ewd_options;

	extract(shortcode_atts(array(
		"format" => ''
	), $atts));
	
	if( $format == 'money') {
		$target = ewd_display_money( $ewd_options['ewd_donation_target'] );
	} else {
		$target = $ewd_options['ewd_donation_target'];
	}
	
	return $target;
}

add_shortcode("donations_target", "ewd_donation_target_shortcode");

/**
 * Create [donation_form] shortcode
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function ewd_donation_form() {

	global $ewd_options;

	if( isset( $_POST['ewd_step_2'] ) && $_POST['ewd_step_2'] == 'on') {
	
		if( ewd_check_donation_form_errors() != '' ) {
			return ewd_check_donation_form_errors().ewd_create_donation_form();
		} else {
			return ewd_create_confirm_donation_form();
		}
	} else {
		return ewd_create_donation_form();
	}

}

add_shortcode("donation_form", "ewd_donation_form");


/**
 * Create [donations_list] shortcode
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function ewd_donations_list() {

	global $ewd_options;
	
	// Create the Query
	if( !isset( $ewd_options['ewd_donations_per_page']) OR $ewd_options['ewd_donations_per_page'] == '') {
		$posts_per_page = 5; 
	} else {
		$posts_per_page = $ewd_options['ewd_donations_per_page'];
	}
	$post_type 		= 'donation';
	$paged 			= (get_query_var('paged')) ? get_query_var('paged') : 1;
				
	$query = new WP_Query( array ( 
								'post_type'      => $post_type,
								'posts_per_page' => $posts_per_page,
								'paged'          => $paged,
								) 
						);
	
	//Get post type count
	$post_count = $query->post_count;
	$i = 1;
	
	ob_start();
	
	// Displays info
	if( $post_count > 0) :
	
		// Loop
		while ($query->have_posts()) : $query->the_post();
			
			$donation_id = get_the_ID();
			$hide_donor  = get_post_meta($donation_id, 'ewd_hide_donor', true);
			$hide_amount = get_post_meta($donation_id, 'ewd_hide_amount', true);
			$amount_value= get_post_meta($donation_id, 'ewd_amount', true);
			$amount      = ewd_display_money( $amount_value );
			$message     = get_the_content();
			$project 	 = get_the_term_list( $donation_id, 'donation-types', 'Associated Project: ', ' ', '' );

			// Donation Title
			if( isset($hide_donor) AND $hide_donor == 'on' ) {
				if( isset($hide_amount) AND $hide_amount == 'on' ) {
					echo '<h3>'.__('Hidden Amount Anonymous Donation ', 'ewd').'</h3>';
				} else {
					echo '<h3>'.$amount.' '._e('Anonymous Donation', 'ewd').'</h3>';
				}
			} else {
				if( isset($hide_amount) AND $hide_amount == 'on' ) {
					echo '<h3>'._e('Hidden Amount', 'ewd').' '.get_the_title().'</h3>';
				} else {
					echo '<h3>'.$amount.' '.get_the_title().'</h3>';
				}
			}

			// Get donation part of target in percent
			if( isset($ewd_options['ewd_donation_enable_target']) AND $ewd_options['ewd_donation_enable_target'] == 'on' AND isset($ewd_options['ewd_donation_target']) AND $ewd_options['ewd_donation_target'] != '') {
				
				$target            = $ewd_options['ewd_donation_target'];
				$percent           = ($amount_value*100)/$target;
				$percent_of_target = ' | '.number_format($percent, 2, '.', '').'% '.__('of global target', 'ewd').'.';
			}

			// Date & target %
			echo '<p class="ewd_donation_date">';
			echo __('On', 'ewd').' ';
			echo the_time( get_option('date_format') );
			if( isset($ewd_options['ewd_donation_enable_part_of_target']) AND $ewd_options['ewd_donation_enable_part_of_target'] == 'on' ) { 
				echo $percent_of_target; 
			}
			echo '</p>';

			// thumbnail
			if( isset($ewd_options['ewd_donation_enable_donor_picture']) AND $ewd_options['ewd_donation_enable_donor_picture'] == 'on' AND has_post_thumbnail( $donation_id ) ) {
				$the_post_thumbnail_attr = array(
					'class' => '',
					'alt'	=> get_the_title(),
					'title'	=> get_the_title()
				);
			
			echo '<div class="ewd_donor_profile">'.get_the_post_thumbnail( $donation_id, 'ewd_donor_profile', $the_post_thumbnail_attr ).'</div>';
			}

			// Donation Message
			if( isset($message) AND $message != '' ) { 
				echo '<p class="ewd_donation_message">';
				echo $message;
				echo '</p>';
			} 

			// Project
			if( $project ) {
				echo '<p class="ewd_associated_project">';
				echo $project;
				echo '</p>';
			} 
			echo '<hr />';

		$i++;
		endwhile;
		?>
		
		<div class="navigation">
		<?php
		// Pagination
		$total = $query->max_num_pages;
		// only bother with the rest if we have more than 1 page!
		if ( $total > 1 )  {
		     // get the current page
		     if ( !$current_page = get_query_var('paged') )
		          $current_page = 1;
		     // structure of "format" depends on whether we're using pretty permalinks
		     if( get_option('permalink_structure') ) {
			     $format = '?paged=%#%';
		     } else {
			     $format = 'page/%#%/';
		     }
		     echo paginate_links(array(
		          'base'     => get_pagenum_link(1) . '%_%',
		          'format'   => $format,
		          'current'  => $current_page,
		          'total'    => $total,
		          'mid_size' => 4,
		          'type'     => 'list'
		     ));
		}
		?>
		</div>
		<?php
		
		else:
		
		echo '<p class="ewd_no_donation">';
		_e('There is no donation for the moment.', 'ewd');
		echo '</p>';
		
	endif;
	
	$display = ob_get_clean();
	
	return $display;
	
	// Reset query to prevent conflicts
	wp_reset_query();

}

add_shortcode("donations_list", "ewd_donations_list");