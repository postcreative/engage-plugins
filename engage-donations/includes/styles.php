<?php

/* ----------------------------------------
* load CSS
----------------------------------------- */

function ewd_admin_styles() {

	global $pagenow;
	
	if( is_admin() && $pagenow == 'edit.php' && isset($_GET['page']) && $_GET['page'] == 'ewd-settings') {
		wp_enqueue_style('thickbox');
		wp_enqueue_style('jquery-ui-custom', EWD_PLUGIN_URL.'includes/css/jquery-ui-custom.css');
		wp_enqueue_style('ewd-style', EWD_PLUGIN_URL.'includes/css/admin-styles.css', array(), EWD_PLUGIN_VERSION);
	}

}

add_action('admin_init', 'ewd_admin_styles');

function ewd_styles() {

	if( !is_admin() ) {
		wp_enqueue_style('ewd-progress-bar', EWD_PLUGIN_URL.'includes/css/progress-bar.css.php', array(), EWD_PLUGIN_VERSION);
		wp_enqueue_style('ewd-front-end', EWD_PLUGIN_URL.'includes/css/front-end.css', array(), EWD_PLUGIN_VERSION);
	}

}

add_action('init', 'ewd_styles');
