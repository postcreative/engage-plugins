<?php
/*
Plugin Name: Custom Dahsboard Message
Plugin URL: http://postcreative.co.uk/
Description: A little plugin to modify default dashboard welcome message
Version: 0.1
Author: Nova Stevenson
Author URI: http://remicorson.com
Contributors: corsonr
*/


/**
 * Hide default welcome dashboard message and and create a custom one
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function engage_welcome_panel() {

	?>
<script type="text/javascript">
/* Hide default welcome message */
jQuery(document).ready( function($) 
{
	$('div.welcome-panel-content').hide();
});
</script>

	<div class="custom-welcome-panel-content">
	<h3><?php _e( 'Welcome to your Engage website' ); ?></h3>
	<p class="about-description"><?php _e( 'It\'s super easy to get going with your new site just follow the links below. ' ); ?></p>
	<div class="welcome-panel-column-container">
	<div class="welcome-panel-column">
			<h4><?php _e( 'Let\'s get started' ); ?></h4>
				<ul>
		
			<li><?php printf( '<a href="%s" class="welcome-icon welcome-edit-page">' . __( 'Edit your home page' ) . '</a>', get_edit_post_link( get_option( 'page_on_front' ) ) ); ?></li>

		<li><?php printf( '<a href="%s" class="welcome-icon welcome-edit-page">' . __( 'Edit your about page' ) . '</a>', admin_url( 'post.php?post=2211&action=edit' ) ); ?></li>
		<li><?php printf( '<a href="%s" class="welcome-icon welcome-edit-page">' . __( 'Edit your contact page' ) . '</a>', get_edit_post_link( get_option( 'post.php?post=2212&action=edit' ) ) ); ?></li>
		
			
		</ul>	
	</div>
	<div class="welcome-panel-column">
		<h4><?php _e( 'Next Steps' ); ?></h4>
				<ul>
				<li><?php printf( '<a href="%s" class="welcome-icon welcome-add-page">' . __( 'Add additional pages' ) . '</a>', admin_url( 'post-new.php?post_type=page' ) ); ?></li>
				<li><?php printf( '<a href="%s" class="welcome-icon welcome-add-page">' . __( 'Add additional users' ) . '</a>', admin_url( 'user-new.php?post_type=page' ) ); ?></li>
			<li><?php printf( '<div class="welcome-icon welcome-widgets-menus">' . __( 'Manage <a href="%1$s">widgets</a> or <a href="%2$s">menus</a>' ) . '</div>', admin_url( 'widgets.php' ), admin_url( 'nav-menus.php' ) ); ?></li>
		
			<li><?php printf( '<a href="%s" class="welcome-icon welcome-view-site">' . __( 'View your site' ) . '</a>', home_url( '/' ) ); ?></li>
			
			
		</ul>
		

	</div>
	<div class="welcome-panel-column welcome-panel-last">
	
	<h4><?php _e( "Need some help?" ); ?></h4>
	<p>Check out the help instructions by clicking the <strong style="color:#ff42c4";>Help Button</strong> in the top right-hand corner of the screen.  You will find this on every admin page.</p>
		<p><a class="button button-primary button-hero load-customize hide-if-no-customize" href="http://your-website.com"><?php _e( 'Email support' ); ?></a></p>
		
		
		<p><?php printf( '<a href="%s" class="welcome-icon welcome-learn-more">' . __( 'Learn more about getting started' ) . '</a>', __( 'http://engage-online.com/first_steps_with_engage' ) ); ?></p>

	</div>
	</div>
	</div>	

<?php
}

add_action( 'welcome_panel', 'engage_welcome_panel' );