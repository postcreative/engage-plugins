<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/

class echelp {
	function echelp(){
		add_action('init',array(&$this,'init'));
		add_action('admin_init',array(&$this,'admin_init'));
		add_action('admin_head', array(&$this, 'post_meta_box') );
		add_action('save_post', array(&$this,'save_post') );	
	}
	
	function init(){
		$labels = array(
			'name' 				=> __('Contextual Help','echelp'),
			'singular_name' 	=> __('Contextual Help','echelp'),
			'add_new' 			=> __('Add Contextual Help','echelp'),
			'edit_item' 		=> __('Edit Contextual Help','echelp'),
			'new_item' 			=> __('New Contextual Help','echelp'),
			'view_item'			=> __('View Contextual Help','echelp'),
			'search_items'		=> __('Search Contextual Help','echelp'),
			'not_found'			=> __('No Contextual Help found','echelp'),
			'not_found_in_trash'=> __('No Contextual Help found in trash','echelp')
		);
		
		$pp = register_post_type('echelp', array(
			'label' => __('Contextual Help','echelp'),
			'labels' => $labels,
			'public' => false,
			'show_ui' => true,
			'capability_type' => 'page',
			'hierarchical' => true,
			'rewrite' => false,
			'query_var' => false,
			'supports' => array('title','editor','revisions','page-attributes'),
			'exclude_from_search' => true,
			'menu_position' => 5,
			'show_in_nav_menus' => false
		));
	}	
	
	function admin_init(){
		add_filter( 'manage_edit-echelp_columns', array(&$this,'admin_columns')  );
		add_action('manage_pages_custom_column', array(&$this,'custom_column'),10,2);		
	}
	
	function admin_columns($defaults){
		$new = array();
		foreach($defaults as $key => $title){

			$new[$key]=$title;
			if($key=='title'){
				$new['echelp_location']=__("Location",'echelp');
			}
		}
	
		return $new;
	}
	
	function custom_column($field, $post_id=null){
		global $post;
		if($post->post_type=='echelp'){
			$post_id = $post_id==null?$post->ID:$post_id;
			if($field=='echelp_location'){
				$location = get_post_meta($post_id,'echelp_location',true);
				$tmp=array();
				if(!empty($location)&&is_array($location)){
					foreach($location as $l){
						$tmp[]=$this->_get_submenu_label($l);
					}
				}
				echo implode(',',$tmp);
			}		
		}
	}		
	
	function _get_menu_label($slug){
		$slug=str_replace('&','&amp;',$slug);
		global $menu;
		foreach($menu as $m){
			if($m[2]==$slug)
				return $m[0];
		}
		return '';
	}
	
	function _get_submenu_label($slug){
		$slug=str_replace('&','&amp;',$slug);
		global $submenu;
		foreach($submenu as $group){
			foreach($group as $m){
				if($m[2]==$slug)
					return $m[0];			
			}
		}
		return $this->_get_menu_label($slug);
	}	
	
	function post_meta_box(){
		add_meta_box( 'wp-echelp', __('Contextual Help Location','echelp'),	array( &$this, 'form_template' ), 'echelp', 'normal', 'high');
	}	
	
	function form_template($post){
		echo '<input type="hidden" name="echelp-nonce" id="echelp-nonce" value="' . wp_create_nonce( 'echelp-nonce' ) . '" />';
		global $menu;
		global $submenu;	
		global $admin_page_hooks;
		global $_registered_pages;
		global $wp_post_types;
		global $_parent_pages;
		global $wp_version;
		
		$misc = array(
			array(
				'label'	=> 'Edit existing user',
				'value'	=> 'user-edit.php'
			)/*,
			array(
				'label'	=> '',
				'value'	=> ''
			)*/
		);
		
		//$hookname = get_plugin_page_hookname( $menu_slug, '' );
		if(!is_array($submenu)||count($submenu)==0){
			_e('Plugin is not compatible with the installed WordPress version.','echelp');
			return;
		}
		
		$echelp_location = get_post_meta($post->ID,'echelp_location',true);
		$echelp_location = $echelp_location==''?array():$echelp_location;
//error_log(print_r($echelp_location,true),3,'eclog.log');
//echo "<PRE>";
//print_r($echelp_location);
//echo "</PRE>";
?>
<div id="echelp-options-cont" class="echelp-options-cont <?php echo do_action('pop-options-cont-class')?>">

<div id="toggle-settings" class="toggle-option">
<h3 class="option-title sidebar-name">
	<span class="pop-option-title-icon"></span>
	<span class="pop-option-title"><?php _e('Advanced Settings','echelp')?></span>
	<span class="pop-right">&nbsp;</span>
</h3>	
<div class="option-content widget">
	<?php if($wp_version<3.3): ?>	
	<div class="toggle-row">
		<div class="description-holder">
			<div class="description">By default the of the help text is not displayed.  Check this option to display it.</div>
			<div class="description-bg">By default the of the help text is not displayed.  Check this option to display it.</div>
		</div>
		<input type="checkbox" name="show_headline" <?php echo 1==get_post_meta($post->ID,'show_headline',true)?'checked="checked"':''?> value="1" />&nbsp;<?php _e('Display the title','echelp')?>
	</div>
	<?php else:?>
	<div class="toggle-row">
		<div class="description-holder">
			<div class="description"><p>Tabs are ordered using the order field.</p><p>There can only be one echelp post assigned to the contextual help sidebar.  If more than one is assigned, only the last is displayed.</p></div>
			<div class="description-bg"><p>Tabs are ordered using the order field.</p><p>There can only be one echelp post assigned to the contextual help sidebar.  If more than one is assigned, only the last is displayed.</p></div>
		</div>
		<label>Help Type (Tab or Sidebar):</label>
		<select name="help_type">
			<option <?php echo in_array(get_post_meta($post->ID,'help_type',true),array('','tab'))?'selected="selected"':''?> value="tab">Tab</option>
			<option <?php echo 'sidebar'==get_post_meta($post->ID,'help_type',true)?'selected="selected"':''?> value="sidebar">Sidebar</option>
		</select>
	</div>
	<?php endif; ?>
	<div class="toggle-row">
		<div class="description-holder">
			<div class="description">Check if you want this Context Help to replace an existing help text.  If you set more than one Context Help to the same location and check them to replace, then only the last one will be displayed.</div>
			<div class="description-bg">Check if you want this Context Help to replace an existing help text.  If you set more than one Context Help to the same location and check them to replace, then only the last one will be displayed.</div>
		</div>
		<input type="checkbox" name="replace_existing_help" <?php echo 1==get_post_meta($post->ID,'replace_existing_help',true)?'checked="checked"':''?> value="1" />&nbsp;<?php _e('Replace existing help text','echelp')?>
	</div>
	<?php if($wp_version<3.3): ?>	
	<div class="toggle-row">
		<div class="description-holder">
			<div class="description">By default the help text is added to the bottom of existing content.  Check this option if you want to display this help text before existing help content.</div>
			<div class="description-bg">By default the help text is added to the bottom of existing content.  Check this option if you want to display this help text before existing help content.</div>
		</div>
		<input type="checkbox" name="help_prepend" <?php echo 1==get_post_meta($post->ID,'help_prepend',true)?'checked="checked"':''?> value="1" />&nbsp;<?php _e('Display this help text before existing help content.','echelp')?>
	</div>
	<?php endif; ?>
	<div class="clearer">&nbsp;</div>
</div>
</div>
<?php		
		$done_parent_slugs = array('separator1','separator2','separator-last');
		foreach($submenu as $parent_slug => $submenu_items){
			$done_parent_slugs[]=$parent_slug;
			if(count($submenu_items)==0)
				continue;
?>
<div id="toggle-<?php echo str_replace('.','_',$parent_slug)?>" class="toggle-option">

<h3 class="option-title sidebar-name">
	<span class="pop-option-title-icon"></span>
	<span class="pop-option-title"><?php echo $this->_get_menu_label($parent_slug);?></span>
	<span class="pop-right">&nbsp;</span>
</h3>
<div class="option-content widget">
<?php		foreach($submenu_items as $si => $item):$hookname = trim($item[2]);/*error_log($echelp_location[0]."\r\n".$hookname."<--\n\r\n\r",3,'eclog.log');*/?>
<div class="toggle-row">
<input type="checkbox" name="f_echelp_location[]" <?php echo in_array(str_replace('&amp;','&',$hookname),$echelp_location)?'checked="checked"':''?> class="chk-echelp-map" value="<?php echo trim($hookname)?>" />&nbsp;<?php echo /*$hookname." : ".*/ $item[0] ?><br />
</div>
<?php		endforeach; ?>
<div class="clearer">&nbsp;</div>
</div>
</div>
<?php	
		}	
?>

<div id="toggle-misc" class="toggle-option">
<h3 class="option-title sidebar-name">
	<span class="pop-option-title-icon"></span>
	<span class="pop-option-title"><?php echo _e('Miscellaneous','ech');?></span>
	<span class="pop-right">&nbsp;</span>
</h3>
<div class="option-content widget">
<?php		foreach($menu as $si => $item):if(in_array($item[2],$done_parent_slugs))continue;$hookname = trim($item[2]);?>
<div class="toggle-row">
<input type="checkbox" name="f_echelp_location[]" <?php echo in_array(str_replace('&amp;','&',$hookname),$echelp_location)?'checked="checked"':''?> class="chk-echelp-map" value="<?php echo trim($hookname)?>" />&nbsp;<?php echo /*$hookname." : ".*/ $item[0] ?><br />
</div>
<?php		endforeach; ?>
<?php 		foreach($misc as $m):?>
<div class="toggle-row">
<input type="checkbox" name="f_echelp_location[]" <?php echo in_array($m['value'],$echelp_location)?'checked="checked"':''?> class="chk-echelp-map" value="<?php echo $m['value']?>" />&nbsp;<?php echo $m['label']?><br />
</div>

<?php 		endforeach; ?>


<div class="clearer">&nbsp;</div>
</div>
</div>

</div>
<div class="clearer">&nbsp;</div>
<?php			
	}
	
	function save_post($post_id){
		if ( !wp_verify_nonce( @$_POST['echelp-nonce'], 'echelp-nonce' )) {
			return $post_id;
		}
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
			return $post_id;
		
		if ( 'echelp' == $_POST['post_type'] ) {
		  if ( !current_user_can( 'edit_page', $post_id ) )
		    return $post_id;
		} else {
		  return $post_id;
		}		
		$arr = isset($_POST['f_echelp_location'])&&is_array($_POST['f_echelp_location'])?$_POST['f_echelp_location']:array();
		//$str = serialize($arr);
		update_post_meta($post_id,'echelp_location',$arr);
	
		$replace_existing_help = isset($_POST['replace_existing_help'])&&$_POST['replace_existing_help']==1?1:'';
		update_post_meta($post_id,'replace_existing_help',$replace_existing_help);
				
		$help_prepend = isset($_POST['help_prepend'])&&$_POST['help_prepend']==1?1:'';
		update_post_meta($post_id,'help_prepend',$help_prepend);
		
		$show_headline = isset($_POST['show_headline'])&&$_POST['show_headline']==1?1:'';
		update_post_meta($post_id,'show_headline',$show_headline);
		
		$help_type = isset($_POST['help_type'])?$_POST['help_type']:'tab';
		update_post_meta($post_id,'help_type',$help_type);
		
		return $post_id;
	}			
}
?>