<?php

function ewd_check_donation_form_errors() {
	/* ---------------------------------------------------- */
	/* Error Handling										*/
	/* ---------------------------------------------------- */

	$first_name_error = '';
	$last_name_error  = '';
	$email_error      = '';
	$amount_error     = '';
	$robot_error      = '';
	$output           = '';
	
	if( isset( $_POST['ewd_step_2'] ) ) {
	
		// First Name
		if( isset($_POST['donor_firstname']) && trim( $_POST['donor_firstname'] ) === '') {
			$first_name_error = __('Please enter your first name.', 'ewd');
			$has_error = true;
		}
	
		// Last Name
		if( isset($_POST['donor_lastname']) && trim( $_POST['donor_lastname'] ) === '') {
			$last_name_error = __('Please enter your last name.', 'ewd');
			$has_error = true;
		}
	
		// Email
		if( isset($_POST['donor_email']) && trim( $_POST['donor_email'] ) === '')  {
			$email_error = __('Please enter your email address.', 'ewd');
			$has_error = true;
		} else if( !eregi( "^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$", trim( $_POST['donor_email'] ) ) ) {
			$email_error = __('You entered an invalid email address.', 'ewd');
			$has_error = true;
		}
		
		// Predefined and custom amount
		if( isset( $_POST['custom_amount'] ) && $_POST['custom_amount'] == '' ) {
			$amount_error = __('Please choose your donation amount.', 'ewd');
			$has_error = true;
		} elseif ( isset($_POST['amount']) && $_POST['amount'] == '' && isset($_POST['custom_amount']) && $_POST['custom_amount'] == '') {
			$amount_error = __('Please enter your donation amount.', 'ewd');
			$has_error = true;
		}
		
		// Robot
		if( isset($_POST['robot']) && isset( $_POST['antispam'] ) ) {
		
			$ewd_antispam_data = get_transient( 'ewd_antispam_'.$_POST['antispam'] );
			
			if( $_POST['robot'] != $ewd_antispam_data[2] ) {
				$robot_error = __('Are you a robot ?', 'ewd');
				$has_error = true;
			}
		}
	
		if( isset($has_error) ) {
			// First Name
			if( $first_name_error != '' )
				$output .= '<span class="ewd_error">'.$first_name_error.'</span>';
			// Last Name
			if( $last_name_error != '' )
				$output .= '<span class="ewd_error">'.$last_name_error.'</span>';
			// Email
			if( $email_error != '' )
				$output .= '<span class="ewd_error">'.$email_error.'</span>';
			// Amount
			if( $amount_error != '' )
				$output .= '<span class="ewd_error">'.$amount_error.'</span>';
			// Robot
			if( $robot_error != '' )
				$output .= '<span class="ewd_error">'.$robot_error.'</span>';
		}
		
		return $output;
	}
}

/**
 * Display amounts in donation form
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function ewd_display_amounts(){

	global $ewd_options;
	
	// Display available amounts
	$output = '<p class="donation">';
	
	switch( $ewd_options['ewd_amount_display'] ) {
	
		case 'list':
			$output .= ewd_list_predefined_amounts();
			break;
		case 'custom':
			$output .= ewd_custom_amount_field();
			break;
		default :
			$output .= ewd_list_predefined_amounts();
			$output .= ewd_custom_amount_field();
			break;
	}

	$output .= '</p>';
	
	return $output;
}
		    	
/**
 * Modify Enter title here" field
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function ewd_change_default_title( $title ){
     $screen = get_current_screen();
 
     if  ( 'donation' == $screen->post_type ) {
          $title = __('Donation by [ enter donor name here ]', 'ewd');
     }
 
     return $title;
}

add_filter( 'enter_title_here', 'ewd_change_default_title' );

/**
 * Create donation form
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function ewd_create_donation_form() {

	global $ewd_options, $wpdb;
	
	if( isset( $_POST['donor_firstname'] ) ) { 	$first_name = $_POST['donor_firstname']; }
	if( isset( $_POST['donor_lastname'] ) ) { 	$last_name  = $_POST['donor_lastname']; }
	if( isset( $_POST['donor_email'] ) ) { 		$email      = $_POST['donor_email']; }
	if( isset( $_POST['donor_phone'] ) ) { 		$phone      = $_POST['donor_phone']; }
	if( isset( $_POST['donor_address'] ) ) { 	$address    = $_POST['donor_address']; }
	if( isset( $_POST['donor_city'] ) ) { 		$city       = $_POST['donor_city']; }
	if( isset( $_POST['donor_zipcode'] ) ) { 	$zipcode    = $_POST['donor_zipcode']; }
	if( isset( $_POST['donor_message'] ) ) { 	$message    = $_POST['donor_message']; }
	if( isset( $_POST['donor_state'] ) ) { 		$state   	= $_POST['donor_state']; }
	if( isset( $_POST['donor_occupation'] ) ) { $occupation = $_POST['donor_occupation']; }
	if( isset( $_POST['donor_employer'] ) ) { 	$employer   = $_POST['donor_employer']; }
	if( isset( $_POST['robot'] ) ) { 			$robot      = $_POST['robot']; }
	
	ob_start();
	?>
	
	<form action="" method="post" class="ewd_form">

	    <div id="amounts">
	    
	    	<h2><?php _e( 'Your donation amount', 'ewd' ); ?></h2>
	    
		    <?php
		    	// Display predefined amounts, custom amount or both 
			    echo ewd_display_amounts();
		   ?>
	    </div>

		<div id="donor_information">
		
			<h2><?php _e( 'Your Personal Information', 'ewd' ); ?></h2>
			
			<div class="donor_information_col_one">

				<p>
					<label for="donor_firstname" class="ewd_required"><?php _e('Firstname', 'ewd'); ?></label>
			   		<input type="text" name="donor_firstname" id="donor_firstname" value="<?php if(isset($first_name)) { echo $first_name; } ?>" placeholder="<?php _e('Firstname', 'ewd'); ?>" title="<?php _e('Firstname', 'ewd'); ?>" />
				</p>
				
			    <p>
					<label for="donor_phone"><?php _e('Phone Number', 'ewd'); ?></label>
			    	<input type="text" name="donor_phone" id="donor_phone" value="<?php if(isset($phone)) { echo $phone; } ?>" placeholder="<?php _e('Phone Number', 'ewd'); ?>" title="<?php _e('Phone Number', 'ewd'); ?>" />
			    </p>

			</div>
			
			<div class="donor_information_col_two">
			
				<p>
					<label for="donor_lastname" class="ewd_required"><?php _e('Lastname', 'ewd'); ?></label>
			    	<input type="text" name="donor_lastname" id="donor_lastname" value="<?php if(isset($last_name)) { echo $last_name; } ?>" placeholder="<?php _e('Lastname', 'ewd'); ?>" title="<?php _e('Lastname', 'ewd'); ?>" />
				</p>
	
			    <p>
					<label for="donor_email" class="ewd_required"><?php _e('Email', 'ewd'); ?></label>
			    	<input type="text" name="donor_email" id="donor_email" value="<?php if(isset($email)) { echo $email; } ?>" placeholder="<?php _e('Email', 'ewd'); ?>" title="<?php _e('Email', 'ewd'); ?>" />
			    </p>

			</div>

		    <p>
					<label for="donor_address"><?php _e('Address', 'ewd'); ?></label>
				<input type="text" name="donor_address" id="donor_address" class="wide" value="<?php if(isset($address)) { echo $address; } ?>" placeholder="<?php _e('Address', 'ewd'); ?>" title="<?php _e('Address', 'ewd'); ?>" />
		    </p>
		    
			<div class="donor_information_col_one">
				
			    <p>
					<label for="donor_city"><?php _e('City', 'ewd'); ?></label>
			    	<input type="text" name="donor_city" id="donor_city" value="<?php if(isset($city)) { echo $city; } ?>" placeholder="<?php _e('City', 'ewd'); ?>" title="<?php _e('City', 'ewd'); ?>" />
			    </p>
			    
			 <?php /*   <p>
					<label for="donor_state"><?php _e('State', 'ewd'); ?></label>
			    	<input type="text" name="donor_state" id="donor_state" value="<?php if(isset($state)) { echo $state; } ?>" placeholder="<?php _e('State', 'ewd'); ?>" title="<?php _e('State', 'ewd'); ?>" />
			    </p>
			    
			    <p>
					<label for="donor_employer"><?php _e('Employer', 'ewd'); ?></label>
			    	<input type="text" name="donor_employer" id="donor_employer" value="<?php if(isset($employer)) { echo $employer; } ?>" placeholder="<?php _e('Employer', 'ewd'); ?>" title="<?php _e('Employer', 'ewd'); ?>" />
			    </p>
*/ ?>
			</div>
			
			<div class="donor_information_col_two">
			
				<p>
					<label for="donor_zipcode"><?php _e('Zip Code / Postcode', 'ewd'); ?></label>
			    	<input type="text" name="donor_zipcode" id="donor_zipcode" value="<?php if(isset($zipcode)) { echo $zipcode; } ?>" placeholder="<?php _e('Zipcode / Postcode', 'ewd'); ?>" title="<?php _e('Zip Code / Postcode', 'ewd'); ?>" />
				</p>
				
<?php /*			    <p>
					<label for="donor_occupation"><?php _e('Occupation', 'ewd'); ?></label>
			    	<input type="text" name="donor_occupation" id="donor_occupation" value="<?php if(isset($occupation)) { echo $occupation; } ?>" placeholder="<?php _e('Occupation', 'ewd'); ?>" title="<?php _e('Occupation', 'ewd'); ?>" />
			    </p>
*/ ?>
			</div>
		    
		    <div class="clear"></div>
		    <p>
					<label for="donor_message"><?php _e('Optional Message', 'ewd'); ?></label>
		    	<textarea name="donor_message" id="donor_message" placeholder="<?php _e('Write an optional message here', 'ewd'); ?>"  placeholder="<?php _e('Optional message', 'ewd'); ?>"><?php if(isset($message)) { echo $message; } ?></textarea>
		    </p>
		    
			<?php
			// List donations types (taxonomies)
			$donation_types = get_terms( 'donation-types', array(
				'orderby'    => 'count',
			 	'hide_empty' => 0
			) );
	
			if( $donation_types != false) {
			?>
			
			<h2><?php _e( 'Choose the project to help', 'ewd' ); ?></h2>
			
			<?php
				foreach($donation_types as $type) {
				
					$term_meta = get_option( "ewd_taxonomy_".$type->term_id);
					
					echo '<span class="donation_radio project"><input type="radio" name="donation_type" id="donation_type" value="'.$type->term_id.'">';
					echo $type->name;
					if( $term_meta['project_target'] != '' ) {
						echo ' - '.__('Target', 'ewd').': '.ewd_display_money( $term_meta['project_target'] );
					}
					echo '</span>';
					if( $type->description ) {
						echo '<p>'.$type->description.'</p>';
					}
				}
			?>
			
			<?php } ?>
			
			<h2><?php _e( 'Your Donation Information', 'ewd' ); ?></h2>
			
			<p>
		    <span class="donation_radio"><input type="radio" name="recurring_payment" id="no_recurring_payment" value=""> <?php _e('No recurring payment', 'ewd'); ?></span>
		    <span class="donation_radio"><input type="radio" name="recurring_payment" id="recurring_payment_weekly" value="weekly"> <?php _e('Weekly recurring payment', 'ewd'); ?></span>
		    <span class="donation_radio"><input type="radio" name="recurring_payment" id="recurring_payment_monthly" value="montly"> <?php _e('Monthly recurring payment', 'ewd'); ?></span>
		    <span class="donation_radio"><input type="radio" name="recurring_payment" id="recurring_payment_yearly" value="yearly"> <?php _e('Yearly recurring payment', 'ewd'); ?></span>
		    </p>
		    
		    <p>
		    <input type="checkbox" name="hide_donor" id="hide_donor" value="on"> <?php _e('Make an anonymous donation', 'ewd'); ?>
		    </p>
		    
		    <p>
		    <input type="checkbox" name="hide_amount" id="hide_amount" value="on"> <?php _e('Hide donation amount in our donors public list', 'ewd'); ?>
			</p>
			
		    <p>
		    <?php
		    _e('Anti-spam: How much is', 'ewd'); 
		    
		    if( !isset( $robot ) ) {
			    // Store antispam
			    $ewd_antispam_rand = rand();
			    ewd_antispambot( $ewd_antispam_rand );
			    ?>
			    <input type="hidden" name="antispam" id="antispam" value="<?php echo $ewd_antispam_rand; ?>">
			    <?
				// Get antispam data
				$ewd_antispam_data = get_transient( 'ewd_antispam_'.$ewd_antispam_rand );
			} else {
				$ewd_antispam_data = get_transient( 'ewd_antispam_'.$_POST['antispam'] );
			}

		    echo ' '.$ewd_antispam_data[0].'+'.$ewd_antispam_data[1].' ?';
		    
		    ?> <input type="text" name="robot" id="robot" value="<?php if(isset($robot)) { echo $robot; } ?>">
		    
			</p>

		</div>
		
		<?php
		// get Submit donation button text
		if( isset( $ewd_options['ewd_donation_submit_button'] ) && $ewd_options['ewd_donation_submit_button'] != '') {
			$submit_donation = $ewd_options['ewd_donation_submit_button'];
		} else {
			$submit_donation = __( 'Submit donation', 'ewd' );
		} 
		?>
		<input type="hidden" name="ewd_step_2" value="on">
	    <input type="submit" value="<?php echo $submit_donation; ?>">
	
	</form>
<?php
	return ob_get_clean();
}

/**
 * Create donation confirmation + PayPal form
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function ewd_antispambot( $rand ) {

	// Available value
	$number_range = range(1,20);
	
	// Get numbers
	$numbers = array_rand($number_range, 2);
	
	// Calculate
	$calculation = $numbers[0]+$numbers[1];
	
	// Store value in a transient for 60s
	set_transient( 'ewd_antispam_'.$rand, array($numbers[0], $numbers[1], $calculation, 120 ) );

	return $calculation;
}

/**
 * Create donation confirmation + PayPal form
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function ewd_create_confirm_donation_form() {

	global $ewd_options, $ewd_base_dir;
	
	// Needed to check if user already exists
	//require_once(ABSPATH . WPINC . '/registration.php');
	
	ob_start();
	
	// Donor information
	if( isset( $_POST['donor_firstname'] ) ) { 	$donor_firstname = $_POST['donor_firstname']; }
	if( isset( $_POST['donor_lastname'] ) ) { 	$donor_lastname  = $_POST['donor_lastname']; }
	if( isset( $_POST['donor_email'] ) ) { 		$donor_email     = $_POST['donor_email']; }
	if( isset( $_POST['donor_message'] ) ) { 	$donor_message   = $_POST['donor_message']; }
	if( isset( $_POST['donor_phone'] ) ) { 		$donor_phone     = $_POST['donor_phone']; }
	if( isset( $_POST['donor_address'] ) ) { 	$donor_address   = $_POST['donor_address']; }
	if( isset( $_POST['donor_city'] ) ) { 		$donor_city      = $_POST['donor_city']; }
	if( isset( $_POST['donor_zipcode'] ) ) { 	$donor_zipcode   = $_POST['donor_zipcode']; }
	
	// Donation information
	if( isset( $_POST['donation_type'] ) ) {  	$donation_type   = $_POST['donation_type']; }
	
	// Payment information
	$paypal_account    = $ewd_options['ewd_paypal_account'];
	if( isset( $_POST['recurring_payment'] ) ) { $recurring_payment = $_POST['recurring_payment']; }
	$paypal_url        = ewd_paypal_url();
	$notify_url  	   = EWD_PLUGIN_URL . '/includes/functions/paypal_ipn.php';
	$return_url  	   = get_permalink( $ewd_options['ewd_return_url'] ). '?ewd_action=return';
	$cancel_url  	   = get_permalink( $ewd_options['ewd_cancel_url'] ). '?ewd_action=cancel';
	$header_logo 	   = $ewd_options['ewd_paypal_header_logo'];
	
	// Get donation amount and re-format for PayPal standard (ex: 20.00)
	if( isset( $_POST['custom_amount'] ) && $_POST['custom_amount'] != '' ) {
		// Check if string contains .
		if (strpos($_POST['custom_amount'],'.') !== false) {
			$donation_amount = $_POST['custom_amount'];
		} else {
			$donation_amount =  $_POST['custom_amount'].'.00';
		}
	}
	if( isset( $_POST['amount'] ) && $_POST['amount'] != '' ) {
		// Check if string contains .
		if (strpos($_POST['amount'],'.') !== false) {
			$donation_amount = $_POST['amount'];
		} else {
			$donation_amount = $_POST['amount'].'.00';
		}
	}
	
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

	// Thanks title
	echo '<h2>';
	_e('Dear', 'ewd');
	echo ' '.$_POST['donor_firstname'].' '.$_POST['donor_lastname'];
	echo '</h2>';
	
	echo '<p>'.$ewd_options['ewd_donation_confirm_message'].'</p>';

	
	// Attach donation tu user if exists, otherwise create user
	if( email_exists( $donor_email ) ) {
		$user_id         = email_exists( $donor_email );
		$user_info       = get_userdata($user_id);
		$donor_firstname = $user_info->user_firstname;
		$donor_lastname  = $user_info->user_lastname;
	} else {
		$user_id = wp_insert_user( array (
									'user_pass'     => wp_generate_password(),
									'user_login'    => $donor_firstname.'_'.$donor_lastname,
									'user_nicename' => $donor_firstname.'_'.$donor_lastname,
									'user_email'    => $donor_email,
									'display_name'  => ucfirst($donor_firstname).' '.strtoupper($donor_lastname),
									'first_name'    => $donor_firstname,
									'last_name'     => $donor_lastname,
									'role' 			=> 'donor'
									) 
								) ;
	}
	
	// Set user data
	update_user_meta( $user_id, 'phone', $donor_phone );
	update_user_meta( $user_id, 'address', $donor_address );
	update_user_meta( $user_id, 'city', $donor_city );
	update_user_meta( $user_id, 'zipcode', $donor_zipcode );
	
	// Insert donation draft into DB
	$donation = array(
		'post_title'   => __('Donation by', 'ewd').' '.ucfirst($donor_firstname).' '.strtoupper($donor_lastname),
		'post_content' => $donor_message,
		'post_status'  => 'draft',
		'post_author'  => $user_id,
		'post_type'    => 'donation'
	);
	
	// Insert the post into the database
	$donation_id = wp_insert_post( $donation );
	
	// Donation TYpe
	if( isset($donation_type) && $donation_type != '') {
		wp_set_post_terms( $donation_id, array($donation_type), 'donation-types' );
	}

	// Donation meta
	update_post_meta($donation_id, 'ewd_amount', $donation_amount);
	update_post_meta($donation_id, 'ewd_message', $donor_message);
	if( isset( $_POST['hide_donor'] ) ) { update_post_meta($donation_id, 'ewd_hide_donor', $_POST['hide_donor']); }
	if( isset( $_POST['hide_amount'] ) ) { update_post_meta($donation_id, 'ewd_hide_amount', $_POST['hide_amount']); }
	
	// Set recurring payment type
	if( isset( $recurring_payment ) && $recurring_payment != '') {
		update_post_meta( $donation_id, 'ewd_recurring', $recurring_payment );
	}
	?>
	
	<h3><?php _e('Donation Details', 'ewd'); ?></h3>
	
	<p>
		<ul>
			<li><strong><?php _e('Donor Name', 'ewd'); ?></strong>: <?php echo $donor_firstname.' '.$donor_lastname; ?></li>
			<li><strong><?php _e('Donor Email', 'ewd'); ?></strong>: <?php echo $donor_email; ?></li>
			<li><strong><?php _e('Donation Amount', 'ewd'); ?></strong>: <?php echo $donation_amount; ?> <?php echo $ewd_options['ewd_donation_currency_code']; ?></li>
			<li><strong><?php _e('Hide Your Name?', 'ewd'); ?></strong>: <?php echo $hide_donor; ?></li>
			<li><strong><?php _e('Hide Donation Amount?', 'ewd'); ?></strong>: <?php echo $hide_amount; ?></li>
			<li><strong><?php _e('Recurring Payment', 'ewd'); ?></strong>: <?php if( isset( $recurring_payment ) && $recurring_payment != '') { echo __('YES', 'ewd').' ('.$recurring_payment.')'; } else { _e('NO', 'ewd'); } ?></li>
			<?php if( isset($donation_type) && $donation_type != '') { ?>
			<li><strong><?php _e('Category', 'ewd'); ?></strong>: 
			<?php 
			$term = get_term_by('id', $donation_type, 'donation-types'); 
			echo $term->name;
			?></li>
			<?php } ?>
			<li><strong><?php _e('Message', 'ewd'); ?></strong>:<br /><?php echo stripslashes($donor_message); ?></li>
		</ul>
	</p>
	
	<form action="<?php echo $paypal_url; ?>" method="POST" class="ewd_form">

	    <?php 
	    if( isset( $recurring_payment ) && $recurring_payment != '') {

		    ?>
		    <input type="hidden" name="item_name" 	value="<?php echo ucfirst( $recurring_payment ).' '.__('Recurring Donation to', 'ewd'); ?> <?php bloginfo('name'); ?>" /> 
		    <input type="hidden" name="cmd" 		value="_xclick-subscriptions" />
		    <input type="hidden" name="src" 		value="1" />
			<input type="hidden" name="sra" 		value="1" />
			<input type="hidden" name="a3" 			value="<?php echo $donation_amount; ?>" />
			<input type="hidden" name="p3" 			value="1" />
			<input type="hidden" name="t3" 			value="<?php echo ewd_get_recurring_payment_period( $recurring_payment ); ?>" />
			
	    <?php } else { ?>
	    
		    <input type="hidden" name="cmd" 		value="_xclick" />
		    <input type="hidden" name="amount" 		value="<?php echo $donation_amount; ?>" />
	    <?php } ?>
	    
	    <input type="hidden" name="business" 		value="<?php echo $paypal_account; ?>" />
	    <input type="hidden" name="item_number" 	value="donation_<?php echo rand(); ?>" />
	    <input type="hidden" name="item_name" 		value="<?php _e('Donation to', 'ewd'); ?> <?php bloginfo('name'); ?>" /> 
	    <input type="hidden" name="no_shipping" 	value="1" />
	    <input type="hidden" name="no_note" 		value="1" />
	    <input type="hidden" name="currency_code" 	value="<?php echo $ewd_options['ewd_donation_currency_code']; ?>" />
	    <input type="hidden" name="lc" 				value="<?php if( $ewd_options['ewd_donation_language'] != '') { echo $ewd_options['ewd_donation_language']; } else { echo 'EN'; } ?>" />

		<input type="hidden" name="notify_url" 		value="<?php echo $notify_url; ?>" />
		<input type="hidden" name="image_url" 		value="<?php echo $header_logo; ?>" />
		<input type="hidden" name="return" 			value="<?php echo $return_url; ?>" />
		<input type="hidden" name="cancel_return" 	value="<?php echo $cancel_url; ?>" />
		<input type="hidden" name="custom" 			value="<?php echo $donation_id; ?>" />
	    
	    <?php if ( get_option('permalink_structure') ) { ?>
	    	<?php
	    	if( isset($ewd_options['ewd_donation_form_page_url']) && $ewd_options['ewd_donation_form_page_url'] != '' ) {
		    	$return_url = $ewd_options['ewd_donation_form_page_url'];
	    	}
	    	?>
			<input type="hidden" name="return" value="<?php echo $return_url; ?>?donation_id=<?php echo $donation_id; ?>" />
		<?php } else { ?>
			<input type="hidden" name="return" value="<?php echo $return_url; ?>&donation_id=<?php echo $donation_id; ?>" />
		<?php } ?>
	
		<br /><br />
	    <input type="submit" value="<?php _e('Confirm and go to PayPal', 'ewd'); ?>">

	</form>
	<?php
	
	return ob_get_clean();

}


/**
 * Get recurring payment period
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function ewd_get_recurring_payment_translatable_period( $_post ) {

	// Check is sandbox mode is enable
	if( $_post ) {

	    switch( $_post ) {
		    case 'weekly':
		    	$period = __('Weekly', 'ewd');
		    	break;
		    case 'montly':
		    	$period = __('Montly', 'ewd');
		    	break;
		    case 'yearly':
		    	$period = __('Yearly', 'ewd');
		    	break;
	    }

	}
	
	return $period;
}

/**
 * Get recurring payment type
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function ewd_get_recurring_payment_period( $_post ) {

	// Check is sandbox mode is enable
	if( $_post ) {

	    switch( $_post ) {
		    case 'weekly':
		    	$period = 'W';
		    	break;
		    case 'montly':
		    	$period = 'M';
		    	break;
		    case 'yearly':
		    	$period = 'Y';
		    	break;
	    }

	}
	
	return $period;
}

/**
 * Check is PayPal is in test mode
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function ewd_paypal_url() {

	global $ewd_options;

	// Check is sandbox mode is enable
	if( $ewd_options['ewd_donation_enable_paypal_sandbox'] == 'on' ) {
		$paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/websc';
	} else {
		$paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
	}
	
	return $paypal_url;
	
}

/**
 * Display donation custom amount field
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function ewd_custom_amount_field() {

	global $ewd_options;
	
	$output = '';

	// enable custom Amount
	$output .= '<label><input name="amount" type="text" placeholder="'.__( 'Custom Amount in', 'ewd' ).' '.$ewd_options['ewd_donation_currency_code'].'" title="'.__( 'Custom Amount', 'ewd' ).'"></label>';
	
	return $output;
}

/**
 * Display donation predefined amounts list
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function ewd_list_predefined_amounts() {
	
	global $ewd_options;
	
	$output = '';

	// Check if there are custom amounts
	if( $ewd_options['ewd_repeatable'] != '' ) {

    	// Amount list
    	foreach ($ewd_options['ewd_repeatable'] as $amount) {
    		$output .= '<label><input name="custom_amount" type="radio" value="'.$amount.'"><span>'.ewd_display_money( $amount ).'</span></label>';
    	}
    
    } else {
    	$output .= __('No amount available', 'ewd'); 
    }
    
    return $output;
}

/**
 * Create progress bar
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function ewd_create_progress_bar() {
	
	global $ewd_options;
	
	// Get vars
	$target          = $ewd_options['ewd_donation_target'];
	$total_donations = ewd_get_total_donations_amount();
	
	if( $target && $total_donations ) {
		$percent         = ($total_donations*100)/$target;
		
		// Enable stripes ?
		if( isset( $ewd_options['ewd_donation_progressbar_stripes'] ) && $ewd_options['ewd_donation_progressbar_stripes'] == 'on' ) {
			$enable_stripes = 'progress-striped ';
		} else {
			$enable_stripes = '';
		}
		
		// Enable stripes ?
		if( isset( $ewd_options['ewd_donation_progressbar_stripes_animation'] ) && $ewd_options['ewd_donation_progressbar_stripes_animation'] == 'on' ) {
			$enable_stripes_animation = 'active ';
		} else {
			$enable_stripes_animation = '';
		}
		
		$output = '<div class="progress '.$enable_stripes.$enable_stripes_animation.'bar">';
		$output .= '<div class="bar" style="width: '.$percent.'%"></div>';
		$output .= '</div>';
		
		return $output;
	}
}

/**
 * Send Thank you email
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function ewd_send_thank_you_email( $donation_id ) {

	global $ewd_options;
	
	if( $ewd_options['ewd_donation_email_sender'] != '' AND $ewd_options['ewd_donation_thanks_email'] != '') {
	
		// Create the Query
		$posts_per_page = 1;
		$post_type 		= 'donation';
		$post_status 	= 'publish';
		$post_id 		= $donation_id;
					
		$query = new WP_Query( array ( 
									'post_type'      => $post_type,
									'posts_per_page' => $posts_per_page,
									'post_status'    => $post_status,
									'page_id'        => $post_id,
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
				$donor_id        = $post->post_author;
				$user_info       = get_userdata($donation_donor);
				$donor_email     = $user_info->user_email;
				$donor_firstname = $user_info->user_firstname;
				$donor_lastname  = $user_info->user_lastname;
				
				// Donation data
				$donation_id     = get_the_ID();
				$donation_date   = get_the_time( get_option('date_format') );
				$donation_amount = ewd_display_money( get_post_meta($donation_id, 'ewd_amount', true) );
				
				// Email parameters
				$headers = 'From: '.get_bloginfo('name').' <'.$ewd_options['ewd_donation_email_sender'].'>' . "\r\n";
				$message = $ewd_options['ewd_donation_thanks_email'];
	
				$message = str_replace('%FIRSTNAME%', $donor_firstname, $message);
				$message = str_replace('%LASTNAME%', $donor_lastname, $message);
				$message = str_replace('%DATE%', $donation_date, $message);
				$message = str_replace('%AMOUNT%', $donation_amount, $message);
				
				// Send email
				@wp_mail($donor_email, __('Thank you for your donation', 'ewd'), $message, $headers);
				
			endwhile;
			
		endif;
		
	}
}

/**
 * Send Admin Notification email
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function ewd_send_admin_email( $donation_id ) {

	global $ewd_options;
	$admin_email = get_bloginfo('admin_email');
	
	if( $admin_email != '' AND $ewd_options['ewd_donation_admin_email'] != '') {
	
		// Create the Query
		$posts_per_page = 1;
		$post_type 		= 'donation';
		$post_status 	= 'publish';
		$post_id 		= $donation_id;
					
		$query = new WP_Query( array ( 
									'post_type'      => $post_type,
									'posts_per_page' => $posts_per_page,
									'post_status'    => $post_status,
									'page_id'        => $post_id,
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
				$donor_id        = $post->post_author;
				$user_info       = get_userdata($donation_donor);
				$donor_email     = $user_info->user_email;
				$donor_firstname = $user_info->user_firstname;
				$donor_lastname  = $user_info->user_lastname;
				
				// Donation data
				$donation_id     = get_the_ID();
				$donation_date   = get_the_time( get_option('date_format') );
				$donation_amount = ewd_display_money( get_post_meta($donation_id, 'ewd_amount', true) );
				
				// Email parameters
				$headers = 'From: '.get_bloginfo('name').' <'.$ewd_options['ewd_donation_email_sender'].'>' . "\r\n";
				$message = $ewd_options['ewd_donation_admin_email'];
	
				$message = str_replace('%FIRSTNAME%', $donor_firstname, $message);
				$message = str_replace('%LASTNAME%', $donor_lastname, $message);
				$message = str_replace('%DATE%', $donation_date, $message);
				$message = str_replace('%AMOUNT%', $donation_amount, $message);
				
				// Send email
				@wp_mail( $admin_email, __('You received a new donation', 'ewd'), $message, $headers );
				
			endwhile;
			
		endif;
		
	}
}

/**
 * Get post meta value
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function get_meta_option($var, $post_id=NULL) {

	if($post_id) return get_post_meta($post_id, $var, true);
	if(is_404()) return get_post_meta(0, $var, true); //Fixed 404 page
    global $post;
    return get_post_meta($post->ID, $var, true);

}


/**
 * Add donor user role
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
$donor_role = add_role('donor', __('Donor', 'ewd'), array(
    'read' 			=> true, // True allows that capability
    'edit_posts' 	=> false,
    'delete_posts' 	=> false,
));

/**
 * Register custom image size format
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
if ( function_exists( 'add_image_size' ) ) { 
	add_image_size( 'ewd_donor_profile', 100, 100, true ); //(cropped)
	add_image_size( 'ewd_donor_profile_mini', 40, 40, true ); //(cropped)
}

/**
 * Return number money formatted
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function ewd_display_money($amount) {

	global $ewd_options;
	
	$position = $ewd_options['ewd_currency_position'];
	$currency_symbol = $ewd_options['ewd_donation_currency_symbol'];
	
	if( isset($position) AND $position == 'before' ) {
		return $currency_symbol.$amount;
	} else {
		return $amount.$currency_symbol;
	}
}

/**
 * Return total donations amount received
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function ewd_get_total_donations_amount() {

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
	$i = 1;
	
	$total_amount = '';
	
	// Displays info
	if( $post_count > 0) :
	
		// Loop
		while ($query->have_posts()) : $query->the_post();
			
			$donation_id     = get_the_ID();
			$donation_amount = get_post_meta($donation_id, 'ewd_amount', true);
			
			$total_amount 	+= $donation_amount;
			
		endwhile;
		
	endif;
	
	return $total_amount;
	
}

/**
 * Display Admin settings tabs
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function ewd_show_custom_tabs() {
	
	global $ewd_custom_tabs;
	
	echo '<h2 class="nav-tab-wrapper">';
	foreach ($ewd_custom_tabs as $tab) {
		echo '<a href="#'.$tab['id'].'" class="nav-tab">'.$tab['label'].'</a>';
	}
	echo '<a href="#help" class="nav-tab">'.__('Help', 'ewd').'</a>';
	echo '</h2>';
}

/**
 * Construct admin settings fields
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function ewd_show_custom_fields() {

	global $ewd_custom_meta_fields;
	$prefix = 'ewd_';
	
	// Use nonce for verification
	echo '<input type="hidden" name="custom_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';

	// Begin the field table and loop
	echo '<div id="tab_container">';

	
	
	foreach ($ewd_custom_meta_fields as $field) {
		// get value of this field if it exists for this post
		$ewd_options = get_option('ewd_settings');
		
		// Begin a new tab
		if( $field['type'] == 'tab_start') {
			echo '<div class="tab_content" id="'.$field['id'].'">';
			echo '<table class="form-table">';
		}

		// begin a table row with
		echo '<tr>';

				if( $field['type'] != 'tab_start' && $field['type'] != 'tab_end') {
					if( $field['type'] == 'title') {
						echo '<th colspan="2"><h3 id="ewd_settings['.$field['id'].']">'.$field['label'].'</h3></th>';
					} else {
						echo '<th><label for="ewd_settings['.$field['id'].']">'.$field['label'].'</label></th>';
					}
				}
				
		if( $field['type'] != 'tab_start' && $field['type'] != 'tab_end') {
		echo	'<td>';
				
				switch($field['type']) {
					// text
					case 'text':
						if( isset( $ewd_options[$field['id']] ) ) { $meta = $ewd_options[$field['id']]; }
						echo '<input type="text" name="ewd_settings['.$field['id'].']" id="ewd_settings['.$field['id'].']" value="'.$meta.'" size="30" class="regular-text" />
							<span class="description">'.$field['desc'].'</span>';
					break;
					// text
					case 'password':
						if( isset( $ewd_options[$field['id']] ) ) { $meta = $ewd_options[$field['id']]; }
						echo '<input type="password" name="ewd_settings['.$field['id'].']" id="ewd_settings['.$field['id'].']" value="'.$meta.'" size="30" class="regular-text" />
							<span class="description">'.$field['desc'].'</span>';
					break;
					// textarea
					case 'textarea':
						if( isset( $ewd_options[$field['id']] ) ) { $meta = $ewd_options[$field['id']]; }
						echo '<textarea name="ewd_settings['.$field['id'].']" id="ewd_settings['.$field['id'].']" cols="60" rows="4">'.$meta.'</textarea>
							<br /><span class="description">'.$field['desc'].'</span>';
					break;
					// checkbox
					case 'checkbox':
						if( isset( $ewd_options[$field['id']] ) && $ewd_options[$field['id']] != '' ) {
							echo '<input type="checkbox" name="ewd_settings['.$field['id'].']" id="ewd_settings['.$field['id'].']" ',$ewd_options[$field['id']] ? ' checked="checked"' : '','/>';
						} else {
							echo '<input type="checkbox" name="ewd_settings['.$field['id'].']" id="ewd_settings['.$field['id'].']" />';
						}
							echo '&nbsp;<label for="ewd_settings['.$field['id'].']"><span class="description">'.$field['desc'].'</span></label>';
					break;
					// select
					case 'select':
						echo '<select name="ewd_settings['.$field['id'].']" id="ewd_settings['.$field['id'].']">';
						foreach ($field['options'] as $option) {
							echo '<option', $ewd_options[$field['id']] == $option['value'] ? ' selected="selected"' : '', ' value="'.$option['value'].'">'.$option['label'].'</option>';
						}
						echo '</select>&nbsp;<span class="description">'.$field['desc'].'</span>';
					break;
					// radio
					case 'radio':
						foreach ( $field['options'] as $option ) {
							echo '<input type="radio" name="ewd_settings['.$field['id'].']" id="ewd_settings['.$option['value'].']" value="'.$option['value'].'" ',$ewd_options[$field['id']] == $option['value'] ? ' checked="checked"' : '',' />
									<label for="'.$option['value'].'">'.$option['label'].'</label><br />';
						}
						echo '<span class="description">'.$field['desc'].'</span>';
					break;
					// checkbox_group
					case 'checkbox_group':
						foreach ($field['options'] as $option) {
							echo '<input type="checkbox" value="'.$option['value'].'" name="ewd_settings['.$field['id'].'][]" id="ewd_settings['.$option['value'].']"',$ewd_options[$field['id']] && in_array($option['value'], $ewd_options[$field['id']]) ? ' checked="checked"' : '',' />
									<label for="'.$option['value'].'">'.$option['label'].'</label><br />';
						}
						echo '<span class="description">'.$field['desc'].'</span>';
					break;
					// tax_select
					case 'tax_select':
						echo '<select name="ewd_settings['.$field['id'].']" id="ewd_settings['.$field['id'].']">
								<option value="">-- '.__('Select','ewd').' --</option>'; // Select One
						$terms = get_terms($field['id'], 'get=all');
						$selected = wp_get_object_terms('', 'ewd_settings['.$field['id'].']');
						foreach ($terms as $term) {
							if ($selected && $term->slug == $ewd_options[$field['id']] )
								echo '<option value="'.$term->slug.'" selected="selected">'.$term->name.'</option>';
							else
								echo '<option value="'.$term->slug.'">'.$term->name.'</option>';
						}
						$taxonomy = get_taxonomy($field['id']);
						echo '</select><br /><span class="description"><a href="'.get_bloginfo('home').'/wp-admin/edit-tags.php?taxonomy='.$field['id'].'">'.__('Manage', 'ewd').' '.$taxonomy->label.'</a></span>';
					break; 
					// post_list
					case 'post_list':
						$items = get_posts( array (
							'post_type'	=> $field['post_type'],
							'posts_per_page' => -1
						));
						echo '<select name="ewd_settings['.$field['id'].']" id="ewd_settings['.$field['id'].']">
								<option value="">-- '.__('Select','ewd').' --</option>'; // Select One
							foreach($items as $item) {
								if( $item->post_type == 'page' OR $item->post_type == 'post') {
									$post_type = str_replace('page', __('page', 'ewd'), $item->post_type);
									$post_type = str_replace('post', __('post', 'ewd'), $item->post_type);
								} else { $post_type = $item->post_type; }
								echo '<option value="'.$item->ID.'"',$ewd_options[$field['id']] == $item->ID ? ' selected="selected"' : '','>'.$post_type.': '.$item->post_title.'</option>';
							} // end foreach
						echo '</select>&nbsp;<span class="description">'.$field['desc'].'</span>';
					break;        
					// date
					case 'date':
						echo '<input type="text" class="datepicker" name="ewd_settings['.$field['id'].']" id="ewd_settings['.$field['id'].']" value="'.$ewd_options[$field['id']].'" size="30" />
								<span class="description">'.$field['desc'].'</span>';
					break;
					// image
					case 'image':
						$image = EWD_PLUGIN_URL.'/includes/images/no_image.png';
						echo '<span class="custom_default_image" style="display:none">'.$image.'</span>';
						if ($ewd_options[$field['id']]) { $image = wp_get_attachment_image_src($ewd_options[$field['id']], 'medium');	$image = $image[0]; }
						echo	'<input name="ewd_settings['.$field['id'].']" id="ewd_settings['.$field['id'].']" type="hidden" class="custom_upload_image" value="'.$ewd_options[$field['id']].'" />
									<img src="'.$image.'" class="custom_preview_image" alt="" /><br />
										<input class="custom_upload_image_button button" type="button" value="'.__('Choose Image', 'ewd').'" />
										<small> <a href="#" class="custom_clear_image_button">'.__('Remove Image', 'ewd').'</a></small>
										<br clear="all" /><span class="description">'.$field['desc'].'';
					break;
					// slider
					case 'slider':
					$field_id = $field['id'];
					$value = $ewd_options["$field_id"] != '' ? $ewd_options["$field_id"] : '0';
						echo '<div id="'.$field['id'].'-slider"></div>
								<input type="text" name="ewd_settings['.$field['id'].']" id="ewd_val_slider_'.$field['id'].'" value="'.$value.'" size="5" />
								<br /><span class="description">'.$field['desc'].'</span>';
					break;
					// repeatable
					case 'repeatable':
						echo '
								<ul id="ewd_settings['.$field['id'].']-repeatable" class="custom_repeatable">';
						$i = 0;

						if ( $ewd_options[$field['id']] ) {
							foreach($ewd_options[$field['id']] as $row) {
								echo '<li><span class="sort hndle"><img src="' . EWD_PLUGIN_URL . '/includes/images/cursor_move.png" /></span>
											<input type="text" name="ewd_settings['.$field['id'].']['.$i.']" id="ewd_settings['.$field['id'].']" value="'.$row.'" size="30" />
											<a class="repeatable-remove button" href="#">'.__('Delete','ewd').'</a></li>';
								$i++;
							}
						} else {
							echo '<li><span class="sort hndle">|||</span>
										<input type="text" name="ewd_settings['.$field['id'].']['.$i.']" id="ewd_settings['.$field['id'].']" value="" size="30" />
										<a class="repeatable-remove button" href="#">'.__('Delete','ewd').'</a></li>';
						}
						echo '</ul>';
						echo '<a class="repeatable-add button" href="#">'.__('Add','ewd').'</a>';
						echo '<br /><span class="description">'.$field['desc'].'</span>';
						
					break;
					// colorpicker
					case 'colorpicker':
						echo '<input type="text" class="color" name="ewd_settings['.$field['id'].']" id="ewd_settings['.$field['id'].']" value="'.$ewd_options[$field['id']].'" size="30" />
								<br /><span class="description">'.$field['desc'].'</span>';
						break;

				} //end switch
		}
		echo '</td></tr>';
		
		
		// End a tab
		if( $field['type'] == 'tab_end') {
			echo '</table>';
			echo '</div>';
		}
		
	} // end foreach
	
	
	ewd_help_center();
	
	echo '</div>'; // End Div tab container
}