<?php

/* ----------------------------------------
* To retrieve a value use: $ewd_options[$prefix.'var']
----------------------------------------- */

$prefix = 'ewd_';

/* ----------------------------------------
* Create the TABS
----------------------------------------- */

$ewd_custom_tabs = array(
		array(
			'label'=> __('General', 'ewd'),
			'id'	=> $prefix.'general'
		),
		array(
			'label'=> __('Donation Form', 'ewd'),
			'id'	=> $prefix.'form'
		),
		array(
			'label'=> __('Donations List', 'ewd'),
			'id'	=> $prefix.'list'
		),
		array(
			'label'=> __('Paypal', 'ewd'),
			'id'	=> $prefix.'paypal'
		),
		array(
			'label'=> __('Emails', 'ewd'),
			'id'	=> $prefix.'emails'
		),
	);

/* ----------------------------------------
* Options Field Array
----------------------------------------- */

$ewd_custom_meta_fields = array(

	/* -- TAB 1 -- */
	array(
		'id'	=> $prefix.'general', // Use data in $ewd_custom_tabs
		'type'	=> 'tab_start'
	),
	
	array(
		'label'=> __('Please configure the main options below', 'ewd'),
		'id'	=> $prefix.'title1',
		'type'	=> 'title'
	),
	
	array(
		'label'	=> __('Donation slug', 'ewd'),
		'desc'	=> __("It's the slug used in urls donations, ex http://mysite.com/donation/donation_name", 'ewd'),
		'id'	=> $prefix.'donation_slug',
		'type'	=> 'text'
	),
	array(
		'label'	=> __('Enable Donations Target', 'ewd'),
		'desc'	=> __('Check this if you want to enable a donations amount target you want to reach', 'ewd'),
		'id'	=> $prefix.'donation_enable_target',
		'type'	=> 'checkbox'
	),
	array(
		'label'	=> __('Donations Target', 'ewd'),
		'desc'	=> __('Please enter target amount to reach', 'ewd'),
		'id'	=> $prefix.'donation_target',
		'type'	=> 'text'
	),
	array(
		'label'	=> __('Donations Total Amount Display', 'ewd'),
		'desc'	=> __('Please indicate how you want to display the total donations amount', 'ewd'),
		'id'	=> $prefix.'donation_target_amount_display',
		'type'	=> 'select',
		'options' => array (
			'one' => array (
				'label' => __('In value', 'ewd'),
				'value'	=> 'value'
			),
			'two' => array (
				'label' => __('In percent', 'ewd'),
				'value'	=> 'percent'
			),
		),
	),
	
	array(
		'type'	=> 'tab_end'
	),
	/* -- /TAB 1 -- */
	
	/* -- TAB 2 -- */
	array(
		'id'	=> $prefix.'form', // Use data in $ewd_custom_tabs
		'type'	=> 'tab_start'
	),
	
	array(
		'label'=> __('Please configure the donation form options below', 'ewd'),
		'id'	=> $prefix.'title_form',
		'type'	=> 'title'
	),
	
	array(
		'label'	=> __('Amounts display', 'ewd'),
		'desc'	=> __('Choose to display predefined amounts list, custom amount field or both', 'ewd'),
		'id'	=> $prefix.'amount_display',
		'type'	=> 'select',
		'options' => array(
					'one' => array (
						'label' => __('Predefined amounts list', 'ewd'),
						'value'	=> 'list'
					),
					'two' => array (
						'label' => __('Custom Amount Field', 'ewd'),
						'value'	=> 'custom'
					),
					'three' => array (
						'label' => __('Both', 'ewd'),
						'value'	=> 'both'
					)
		)
	),
	
	array(
		'label'	=> __('Available Donation Amounts', 'ewd'),
		'desc'	=> __('Give a list of amounts that the potential donor can select (ex: 50). Disabled if Enable Custom Donation Amount is checked.', 'ewd'),
		'id'	=> $prefix.'repeatable',
		'type'	=> 'repeatable'
	),
	array(
		'label'	=> __('Send donation button text', 'ewd'),
		'desc'	=> __("Enter submit donation button text", 'ewd'),
		'id'	=> $prefix.'donation_submit_button',
		'type'	=> 'text'
	),
	array(
		'label'	=> __('Confirmation Message', 'ewd'),
		'desc'	=> __("This is the message donors will see before donation confirmation", 'ewd'),
		'id'	=> $prefix.'donation_confirm_message',
		'type'	=> 'textarea'
	),
	array(
		'label'	=> __('Progress Bar Color', 'ewd'),
		'desc'	=> __("Please choose the progress bar color", 'ewd'),
		'id'	=> $prefix.'donation_progressbar_color',
		'type'	=> 'colorpicker'
	),
	array(
		'label'	=> __('Enable progress bar stripes', 'ewd'),
		'desc'	=> __("Choose to enable or disable stripes", 'ewd'),
		'id'	=> $prefix.'donation_progressbar_stripes',
		'type'	=> 'checkbox'
	),
	array(
		'label'	=> __('Enable stripes animation', 'ewd'),
		'desc'	=> __("Choose to enable or disable stripes animation", 'ewd'),
		'id'	=> $prefix.'donation_progressbar_stripes_animation',
		'type'	=> 'checkbox'
	),
	
	array(
		'type'	=> 'tab_end'
	),
	/* -- /TAB 2 -- */
	
	/* -- TAB 3 -- */
	array(
		'id'	=> $prefix.'list', // Use data in $ewd_custom_tabs
		'type'	=> 'tab_start'
	),
	
	array(
		'label'=> __('Please configure the donations list options below', 'ewd'),
		'id'	=> $prefix.'title_list',
		'type'	=> 'title'
	),
	array(
		'label'	=> __('Donations per page', 'ewd'),
		'desc'	=> __('Choose how many donations to display per page in the donations list page.', 'ewd'),
		'id'	=> $prefix.'donations_per_page',
		'type'	=> 'text'
	),
	array(
		'label'	=> __('Enable Donor Picture', 'ewd'),
		'desc'	=> __('Check if you want to enable donor picture (only uploaded from admin)', 'ewd'),
		'id'	=> $prefix.'donation_enable_donor_picture',
		'type'	=> 'checkbox'
	),
	array(
		'label'	=> __('Display Donation Percentage of Target', 'ewd'),
		'desc'	=> __('Check to display the donation part of total target', 'ewd'),
		'id'	=> $prefix.'donation_enable_part_of_target',
		'type'	=> 'checkbox'
	),
	
	array(
		'type'	=> 'tab_end'
	),
	/* -- /TAB 3 -- */
	
	/* -- TAB 4 -- */
	array(
		'id'	=> $prefix.'paypal', // Use data in $ewd_custom_tabs
		'type'	=> 'tab_start'
	),
	
	array(
		'label'=> __('Please configure the PayPal options below', 'ewd'),
		'id'	=> $prefix.'title_paypal',
		'type'	=> 'title'
	),
	array(
		'label'	=> __('Logo', 'ewd'),
		'desc'	=> __("Choose a logo to be displayed on PayPal page", 'ewd'),
		'id'	=> $prefix.'paypal_header_logo',
		'type'	=> 'image'
	),
	array(
		'label'	=> __('PayPal Account', 'ewd'),
		'desc'	=> __("Please enter your PayPal account", 'ewd'),
		'id'	=> $prefix.'paypal_account',
		'type'	=> 'text'
	),
	array(
		'label'	=> __('Enable PayPal Sandbox', 'ewd'),
		'desc'	=> __('Check to activate PayPal Sandbox (test mode only)', 'ewd'),
		'id'	=> $prefix.'donation_enable_paypal_sandbox',
		'type'	=> 'checkbox'
	),
	array(
		'label'	=> __('Enable PayPal IPN Notifier', 'ewd'),
		'desc'	=> __('Check to receive IPN data by email (test mode)', 'ewd'),
		'id'	=> $prefix.'donation_enable_ipn_verifier',
		'type'	=> 'checkbox'
	),
	array(
		'label'	=> __('PayPal Return URL', 'ewd'),
		'desc'	=> __('Choose the page where donors are redirected after payment', 'ewd'),
		'id'	=> $prefix.'return_url',
		'type'	=> 'post_list',
		'post_type' => 'page'
	),
	array(
		'label'	=> __('PayPal Cancel URL', 'ewd'),
		'desc'	=> __('Choose the page where donors are redirected when they cancel transaction', 'ewd'),
		'id'	=> $prefix.'cancel_url',
		'type'	=> 'post_list',
		'post_type' => 'page'
	),
	array(
		'label'	=> __('PayPal Language', 'ewd'),
		'desc'	=> __('Please specify the language to use on PayPal', 'ewd'),
		'id'	=> $prefix.'paypal_language',
		'type'	=> 'select',
		'options' => array (
			'one' => array (
				'label' => 'US',
				'value'	=> 'US'
			),
			'two' => array (
				'label' => 'GB',
				'value'	=> 'GB'
			),
			'three' => array (
				'label' => 'NL',
				'value'	=> 'NL'
			),
			'four' => array (
				'label' => 'DE',
				'value'	=> 'DE'
			),
			'five' => array (
				'label' => 'IT',
				'value'	=> 'IT'
			),
			'six' => array (
				'label' => 'FR',
				'value'	=> 'FR'
			),
			'seven' => array (
				'label' => 'ES',
				'value'	=> 'ES'
			)
		)
	),
	array(
		'label'	=> __('Donation Currency', 'ewd'),
		'desc'	=> __("It's the currency symbol used for donation", 'ewd'),
		'id'	=> $prefix.'donation_currency_symbol',
		'type'	=> 'text'
	),
	array(
		'label'	=> __('Donation Currency code', 'ewd'),
		'desc'	=> __("It's the currency code used for donation (USD, EUR or other)", 'ewd'),
		'id'	=> $prefix.'donation_currency_code',
		'type'	=> 'text'
	),
	array(
		'label'	=> __('Currency Symbol Position', 'ewd'),
		'desc'	=> __('Please specify where you place currency symbol', 'ewd'),
		'id'	=> $prefix.'currency_position',
		'type'	=> 'select',
		'options' => array (
			'one' => array (
				'label' => __('Before currency', 'ewd'),
				'value'	=> 'before'
			),
			'two' => array (
				'label' => __('After currency', 'ewd'),
				'value'	=> 'after'
			),
		),
	),
	
	array(
		'type'	=> 'tab_end'
	),
	/* -- /TAB 4 -- */

	/* -- TAB 5 -- */
	array(
		'id'	=> $prefix.'emails', // Use data in $ewd_custom_tabs
		'type'	=> 'tab_start'
	),

	array(
		'label'=> __('Please configure global emails parameters', 'ewd'),
		'id'	=> $prefix.'title_emails',
		'type'	=> 'title'
	),
	
	array(
		'label'	=> __('Email From', 'ewd'),
		'desc'	=> __('Insert here the email used in emails sendings as sender', 'ewd'),
		'id'	=> $prefix.'donation_email_sender',
		'type'	=> 'text'
	),
	
	array(
		'label'=> __('Please configure the email sent to the donor', 'ewd'),
		'id'	=> $prefix.'title_emails',
		'type'	=> 'title'
	),
	
	array(
		'label'	=> __('Enable thank you email?', 'ewd'),
		'desc'	=> __('Check if you want to send an email once a donation is completed.', 'ewd'),
		'id'	=> $prefix.'donation_enable_thanks_email',
		'type'	=> 'checkbox'
	),
	array(
		'label'	=> __('Thank you email content', 'ewd'),
		'desc'	=> __('Please insert here the thank you email content that is sent when donation is completed.<br />Use this variables: <b>%FIRSTNAME%</b>, <b>%LASTNAME%</b>, <b>%DATE%</b> and <b>%AMOUNT%</b>', 'ewd'),
		'id'	=> $prefix.'donation_thanks_email',
		'type'	=> 'textarea'
	),
	
	
	array(
		'label'=> __('Please configure the email sent to you when a new donation is done', 'ewd'),
		'id'	=> $prefix.'title_emails',
		'type'	=> 'title'
	),
	array(
		'label'	=> __('Enable admin notification email?', 'ewd'),
		'desc'	=> __('Check if you want to receive an email once a donation is completed.', 'ewd'),
		'id'	=> $prefix.'donation_enable_admin_email',
		'type'	=> 'checkbox'
	),
	array(
		'label'	=> __('Admin notification email', 'ewd'),
		'desc'	=> __('Please insert here the admin notification email content that is sent to you when donation is completed.<br />Use this variables: <b>%FIRSTNAME%</b>, <b>%LASTNAME%</b>, <b>%DATE%</b> and <b>%AMOUNT%</b> (These are donor & donation data)', 'ewd'),
		'id'	=> $prefix.'donation_admin_email',
		'type'	=> 'textarea'
	),

	array(
		'type'	=> 'tab_end'
	)
	/* -- /TAB 5 -- */
	


);

?>