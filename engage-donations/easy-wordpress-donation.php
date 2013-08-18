<?php
/*
Plugin Name: Engage Donations (EWD)
Plugin URL: http://remicorson.com
Description: This plugin allows you to easily collect funds on your WordPress install
Version: 1.8.4
Author: Rémi Corson - adpated by Post Creative
Author URI: http://remicorson.com
Contributors: Rémi Corson, corsonr
*/

/*
|--------------------------------------------------------------------------
| ERRORS DISPLAY
|--------------------------------------------------------------------------
*/

//@ini_set('display_errors', 'on');

/*
|--------------------------------------------------------------------------
| CONSTANTS
|--------------------------------------------------------------------------
*/

if( !defined( 'EWD_BASE_FILE' ) )		define( 'EWD_BASE_FILE', __FILE__ );
if( !defined( 'EWD_BASE_DIR' ) ) 		define( 'EWD_BASE_DIR', dirname( EWD_BASE_FILE ) );
if( !defined( 'EWD_PLUGIN_URL' ) ) 		define( 'EWD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
if( !defined( 'EWD_PLUGIN_VERSION' ) ) 	define( 'EWD_PLUGIN_VERSION', '1.8.4' );

/*
|--------------------------------------------------------------------------
| GLOBALS
|--------------------------------------------------------------------------
*/

global $ewd_prefix;
$ewd_prefix = 'ewd_';

/*
|--------------------------------------------------------------------------
| INTERNATIONALIZATION
|--------------------------------------------------------------------------
*/

function ewd_textdomain() {
	load_plugin_textdomain( 'ewd', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action('init', 'ewd_textdomain');

/*
|--------------------------------------------------------------------------
| INCLUDES
|--------------------------------------------------------------------------
*/

$ewd_options = get_option('ewd_settings');
include( EWD_BASE_DIR . '/includes/styles.php');
include( EWD_BASE_DIR . '/includes/scripts.php');
include( EWD_BASE_DIR . '/includes/functions/ewd_functions.php');
include( EWD_BASE_DIR . '/includes/shortcodes.php');
include( EWD_BASE_DIR . '/includes/post-types.php');
include( EWD_BASE_DIR . '/includes/post-types-columns.php');
include( EWD_BASE_DIR . '/includes/taxonomies.php');
include( EWD_BASE_DIR . '/includes/taxonomies-fields.php');
include( EWD_BASE_DIR . '/includes/taxonomies-columns.php');
include( EWD_BASE_DIR . '/includes/functions/donations_functions.php');
include( EWD_BASE_DIR . '/includes/functions/metaboxes/donation_metaboxes.php');

if( isset($_GET['page']) AND $_GET['page'] == 'ewd-settings' ) {
	include( EWD_BASE_DIR . '/includes/options.php');
	include( EWD_BASE_DIR . '/includes/help.php');
}

/*
|--------------------------------------------------------------------------
| MENUS
|--------------------------------------------------------------------------
*/

function ewd_settings_menu() {
	// Export
	add_submenu_page(
					'edit.php?post_type=donation',
					__('Export to CSV', 'ewd'), 
					__('Export to CSV', 'ewd'),
					'manage_options', 
					'ewd-export', 
					'ewd_export_page');
	// add settings page
	add_submenu_page(
					'edit.php?post_type=donation',
					__('Donation Settings', 'ewd'), 
					__('Settings', 'ewd'),
					'manage_options', 
					'ewd-settings', 
					'ewd_settings_page');
}
add_action('admin_menu', 'ewd_settings_menu', 100);


/*
|--------------------------------------------------------------------------
| REGISTER PLUGIN SETTINGS
|--------------------------------------------------------------------------
*/

function ewd_register_settings() {

	// create whitelist of options
	register_setting( 'ewd_settings_group', 'ewd_settings' );
}
//call register settings function
add_action( 'admin_init', 'ewd_register_settings', 100 );

/*
|--------------------------------------------------------------------------
| SETTINGS PAGE LAYOUT
|--------------------------------------------------------------------------
*/

function ewd_settings_page() {
	
	global $ewd_options;
		
	?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br /></div>
		<h2><?php _e('Donation Settings', 'ewd'); ?></h2>
		<?php

		if ( ! isset( $_REQUEST['is-updated'] ) )
			$_REQUEST['settings-updated'] = false;

		?>
		<?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
		<div class="updated fade"><p><strong><?php _e( 'Options saved', 'ewd' ); ?></strong></p></div>

		<?php endif; ?>
		<form method="post" action="options.php" class="ewd_options_form">

			<?php settings_fields( addslashes('ewd_settings_group') ); ?>
			
			<?php ewd_show_custom_tabs(); ?>

			<?php ewd_show_custom_fields(); ?>
			
			<!-- save the options -->
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Options', 'ewd' ); ?>" />
			</p>
			
		</form>
	</div><!--end .wrap-->
	<?php
}

/*
|--------------------------------------------------------------------------
| EXPORT PAGE LAYOUT
|--------------------------------------------------------------------------
*/

function ewd_export_page() {
	
	global $ewd_options;
	
	?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br /></div>
		<h2><?php _e('Export Donations to CSV', 'ewd'); ?></h2>
		
			<p>
			<?php _e('You can download the donation list by clicking the button below. It will create a .CSV file with all informations.', 'ewd'); ?>
			</p>
			
			<!-- export button -->
			<p class="submit">
				<a href="<?php echo EWD_PLUGIN_URL; ?>/includes/functions/ewd_export.php" class="button-primary"><?php _e( 'Click to export', 'ewd' ); ?></a>
			</p>

	</div><!--end .wrap-->
	<?php
	
}

/*
|--------------------------------------------------------------------------
| UNINSTALL
|--------------------------------------------------------------------------
*/

function ewd_uninstall () 
{
    // Uncomment the line above to delete all data at plugin uninstall
    /* delete_option('ewd_settings'); */
}

register_deactivation_hook( __FILE__, 'ewd_uninstall' );