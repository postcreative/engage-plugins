<?php

/* ------------------------------------------------------------------*/
/* CREATE THE HELP TAB */
/* ------------------------------------------------------------------*/

function ewd_help_center() {
	
	ob_start(); 
	?>
	
<div class="tab_content" id="help">

<?php /* <p>
	<a href="#settings"><?php _e('Settings', 'ewd'); ?></a> | 
	<a href="#shortcodes"><?php _e('Shortcodes', 'ewd'); ?></a>
	</p>

	<h3 id="settings"><?php _e('Settings', 'ewd'); ?></h3>
	<p><?php _e('To work properly the plugin needs to be configured. Please go to the settings page and fill in the form.', 'ewd'); ?></p>

	<hr />
	<a href="#icon-options-general"><?php _e('top', 'ewd'); ?></a> */ ?>

	<h3 id="shortcodes"><?php _e('Shortcodes', 'ewd'); ?></h3>
	
	<p><?php _e('To display the donation form use this shortcode. You can place it in any page or post.', 'ewd'); ?></p>
<pre><code>[donation_form]</code></pre>
	<p><?php _e('To display the donations list use this shortcode. You can place it in any page or post.', 'ewd'); ?></p>
<pre><code>[donations_list]</code></pre>
	<p><?php _e('To display the donation targeted amount, use this shortcode.', 'ewd'); ?> <?php echo __( 'The format attribute is optional, do not use it to display a numeric value. Use it to display the money formatted value.', 'ewd' ); ?></p>
<pre><code>[donations_target format="money"]</code></pre>
	<p><?php _e('To display already collected funds, use this shortcode.', 'ewd'); ?> <?php echo __( 'The format attribute is optional, do not use it to display a numeric value. Use it to display the money formatted value.', 'ewd' ); ?></p>
<pre><code>[donations_collected_funds format="money"]</code></pre>
	<p><?php _e('To display the collected funds / target progress bar, use this shortcode.', 'ewd'); ?> <?php echo __( 'Configure color, animation and stripes under Settings > Donation form tab.', 'ewd' ); ?></p>
<pre><code>[donations_progress_bar]</code></pre>
	<p><?php _e('You do not have to set anything else!', 'ewd'); ?></p>

	<hr />
	<a href="#icon-options-general"><?php _e('top', 'ewd'); ?></a>

</div>

	<?php
	echo ob_get_clean();
}

?>