<?php
error_reporting( 0 );
require '../../../../../wp-config.php';
require_once 'ewd_functions.php';
	
	$fp = fopen('donations_report.csv', 'w');
	
	$line = array(
				array(
					__('Donations Report', 'efm'),
					), 
				array(
					__('Date', 'ewd'), 
					__('Donor Firstname', 'ewd'), 
					__('Donor Lastname', 'ewd'), 
					__('Email', 'ewd'), 
					__('Phone', 'ewd'), 
					__('Address', 'ewd'), 
					__('Zipcode', 'ewd'), 
					__('City', 'ewd'), 
					__('Amount', 'ewd'),
					__('Project', 'ewd'),
					__('Anonymous', 'ewd'),
					__('Hidden Amount', 'ewd')
					) 
				);
				
	global $ewd_options;
	
	// Create the Query
	$posts_per_page = -1;
	$post_type 		= 'donation';
	$post_status 	= 'publish';
				
	$query = new WP_Query( array ( 
								'post_type'      => $post_type,
								'posts_per_page' => $posts_per_page,
								'post_status'    => $post_status,
								'no_found_rows'  => 1,
								) 
						);
	
	//Get post type count
	$post_count = $query->post_count;
	
	// Displays info
	if( $post_count > 0) :
	
		// Loop
		while ($query->have_posts()) : $query->the_post();
		
			// Donor Data
			$author_id       = get_the_author_meta( 'ID' );
			$donor_email     = get_the_author_meta( 'user_email' );
			$donor_firstname = get_the_author_meta( 'user_firstname' );
			$donor_lastname  = get_the_author_meta( 'user_lastname' );
			
			$author_phone   = get_user_meta( $author_id, 'phone', true);
			$author_address = get_user_meta( $author_id, 'address', true);
			$author_city    = get_user_meta( $author_id, 'city', true);
			$author_zipcode = get_user_meta( $author_id, 'zipcode', true);
			
			// Donation data
			$donation_id     = get_the_ID();
			$donation_date   = get_the_time( get_option('date_format') );
			$donation_amount = ewd_display_money( get_post_meta($donation_id, 'ewd_amount', true) );
			$hide_donor      = get_post_meta($donation_id, 'ewd_hide_donor', true);
			$hide_amount     = get_post_meta($donation_id, 'ewd_hide_amount', true);
			$projects 		 = wp_get_object_terms( $donation_id, 'donation-types');
			$project_list = '';
			foreach( $projects as $project) {
				$project_list .= $project->name;
			}
			
			// Display yes/no for hidden fields
			if( isset($_POST['hide_donor']) && $_POST['hide_donor'] == 'on' ) {
				$hide_donor      = __('YES', 'ewd');
			} else {
				$hide_donor      = __('NO', 'ewd');
			}
			if( isset($_POST['hide_amount']) && $_POST['hide_amount'] == 'on' ) {
				$hide_amount      = __('YES', 'ewd');
			} else {
				$hide_amount      = __('NO', 'ewd');
			}
			
			array_push( $line, array($donation_date, $donor_firstname, $donor_lastname, $donor_email, $author_phone, $author_address, $author_city, $author_zipcode, $donation_amount, $project_list, $hide_donor, $hide_amount) );
			
		endwhile;
	
	endif;
		
	
	foreach ($line as $fields) {
		fputcsv($fp, $fields);
	}
	
	fclose($fp);
	
	// The user will receive the file type
	header('Content-type: application/csv');
	 
	// Call the file
	header('Content-Disposition: attachment; filename="donations_report.csv"');
	 
	// Read the source
	readfile('donations_report.csv');
	
	unlink('donations_report.csv');
	
?>