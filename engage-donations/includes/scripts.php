<?php

/* ----------------------------------------
* load scripts
----------------------------------------- */

function ewd_admin_scripts() {

	global $pagenow;
	
	if( is_admin() && $pagenow == 'edit.php' && isset($_GET['page']) && $_GET['page'] == 'ewd-settings') {
		wp_enqueue_script('jquery-ui-slider');
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('colorpicker');
		wp_enqueue_script('jquery-ui-sortable');
		
		wp_enqueue_script('jscolor', EWD_PLUGIN_URL . 'includes/js/jscolor.js');
		
		wp_enqueue_script('ewd-admin-scripts', EWD_PLUGIN_URL . 'includes/js/admin-scripts.js', array(), EWD_PLUGIN_VERSION);
		wp_enqueue_script('ewd-media-uploader', EWD_PLUGIN_URL . 'includes/js/media-uploader.js');
	}

}

add_action('init', 'ewd_admin_scripts');
