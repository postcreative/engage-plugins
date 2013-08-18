<?php

/**
Plugin Name: Easy Contextual Help for WordPress
Plugin URI: http://plugins.righthere.com/easy-contextual-help/
Description: Easy Contextual Help (ECH) lets you easily add Contexual Help to each menu in the built-in Administration Panel in WordPress. The plugin automatically recognizes all installed plugins and makes it easy for you to add help. Improve support for your clients by providing contextual help for your menus. Also supports Contextual Help for WordPress Multisite!
Version: 1.5.1 rev29694
Author: Alberto Lau (RightHere LLC)
Author URI: http://plugins.righthere.com
 **/
 
define('ECHELP_PATH', plugin_dir_path(__FILE__) ); 
define("ECHELP_URL", plugin_dir_url(__FILE__) );

load_plugin_textdomain('echelp', null, dirname( plugin_basename( __FILE__ ) ).'/languages' );

if(!function_exists('property_exists')):
function property_exists($o,$p){
	return is_object($o) && 'NULL'!==gettype($o->$p);
}
endif;

class plugin_echelp {
	var $id;
	var $plugin_page;
	function plugin_echelp(){
		$this->id = "echelp";
	
		add_action('init',array(&$this,'init'),9);	
		add_action('plugins_loaded',array(&$this,'plugins_loaded'));
		add_action('admin_head-post-new.php',array(&$this,'pre_admin_head') );
		add_action('admin_head-post.php',array(&$this,'pre_admin_head') );
	}
	
	function init(){
		if(is_admin()){
			wp_register_style($this->id.'-toggle',ECHELP_URL.'css/toggle.css',array(),'1.0.0');
		}	
	}
	
	function pre_admin_head(){
		add_action('admin_head',array(&$this,'admin_head'),9);
	}
	
	function admin_head(){
		global $post;
		if(!in_array($post->post_type,array('echelp','echelpms')))return;
		wp_print_styles($this->id.'-toggle');
?>
<script>
 jQuery(document).ready(function($){ 
 	$("#echelp-options-cont .option-title").click(function(){
		$(this).toggleClass('open').next()
			.find('.description').slideToggle().end()
			.slideToggle();
	});	
	$("#toggle-settings .option-title").click();
  });
</script>
<?php	
	}
	
	function plugins_loaded(){
		global $wp_version;
		
		if(0==intval(get_site_option('ech_allow_subsite')) && current_user_can('easy_contextual_help') ){
			require_once ECHELP_PATH.'includes/class.echelp.php';
			new echelp();			
		}
		
		if(function_exists('is_multisite')&&is_multisite()){
			if(function_exists('is_super_admin')&&is_super_admin()){
				require_once ECHELP_PATH.'includes/class.echelpms.php';
				new echelpms();			
			}		
		}	
	
		if($wp_version<3.3){
			require_once ECHELP_PATH.'includes/class.prewp33_ech_frontend.php';	
		}else{
			require_once ECHELP_PATH.'includes/class.ech_frontend.php';
		}
		new ech_frontend();		
		//add_action( 'network_admin_menu', 'dm_network_pages' ); 
	}
}  

new plugin_echelp();

//-- Installation script:---------------------------------
function echelp_install(){
	$WP_Roles = new WP_Roles();	
	foreach(array(
		'easy_contextual_help'
		) as $cap){
		$WP_Roles->add_cap( 'administrator', $cap );
	}
}
register_activation_hook(__FILE__, 'echelp_install');
//-------------------------------------------------------- 
function echelp_uninstall(){
	$WP_Roles = new WP_Roles();
	foreach(array(
		'easy_contextual_help'
		) as $cap){
		$WP_Roles->remove_cap( 'administrator', $cap );
	}
	//-----
}
register_deactivation_hook( __FILE__, 'echelp_uninstall' );
//--------------------------------------------------------
?>