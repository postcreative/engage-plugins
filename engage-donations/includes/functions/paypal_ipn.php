<?php

/*
|--------------------------------------------------------------------------
| PAYPAL IPN
|--------------------------------------------------------------------------
*/

include( '../../../../../wp-load.php' );
//include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// Do not process IPN if plugin not active
/*
if( !is_plugin_active('easy-wordpress-donations/easy-wordpress-donations.php') ) {
	echo "is not active";
	exit();
}
*/

global $ewd_options;

ini_set('log_errors', true);
ini_set('error_log', dirname(__FILE__).'/ipn_errors.log');


// instantiate the IpnListener class
include('ipnlistener.php');
$listener           = new IpnListener();
$listener->use_curl = false;

// Use of sandbox ?
if( $ewd_options['ewd_donation_enable_paypal_sandbox'] == 'on' ) {
	$listener->use_sandbox = true;
}

/*
$listener->use_ssl = false;
*/

try {
    $listener->requirePostMethod();
    $verified = $listener->processIpn();
} catch (Exception $e) {
    error_log($e->getMessage());
    exit(0);
}

/*
The processIpn() method returned true if the IPN was "VERIFIED" and false if it was "INVALID".
*/
if ($verified) {

    if( $_POST['payment_status'] == 'Completed' ) {
    	
    	// Report
    	if( $ewd_options['ewd_donation_enable_ipn_verifier'] == 'on' ) {
    		@mail(get_bloginfo('admin_email'), 'Verified IPN ', $listener->getTextReport());
    	}
    	
    	// Retrieve user ID and referral user ID
    	if( isset( $_POST['custom'] ) ) {
			
			// Update donation draft into DB
			$donation = array(
				'ID'          => $_POST['custom'],
				'post_status' => 'publish',
			);
			
			// Insert the post into the database
			$donation_id = wp_update_post( $donation );
			
			// Send Thank you email
			if( $ewd_options['ewd_donation_enable_thanks_email']  == 'on' ) {
				ewd_send_thank_you_email( $donation_id );
			}
			
			// Send admin notification email
			if( $ewd_options['ewd_donation_enable_admin_email']  == 'on' ) {
				ewd_send_admin_email( $donation_id );
			}
			
			// Add Pushover notification
			do_action( 'ewd_pushover_new_donation_notification', $donation_id);
		}

	} else {
	    /*
	    An Invalid IPN *may* be caused by a fraudulent transaction attempt. It's
	    a good idea to have a developer or sys admin manually investigate any 
	    invalid IPN.
	    */
	    // Report
	    if( $ewd_options['ewd_donation_enable_ipn_verifier']  == 'on' ) {
	    	@mail(get_bloginfo('admin_email'), 'Invalid IPN', $listener->getTextReport());
	    }
	}
	
}
