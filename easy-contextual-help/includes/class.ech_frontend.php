<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/

class ech_frontend {
	var $echelps;
	function ech_frontend(){
		add_action('in_admin_header',array(&$this,'in_admin_header'));
	}
	
	function in_admin_header(){
		global $hook_suffix;
		$screen = get_current_screen();
		$current_hookname = $this->get_hookname($screen,$hook_suffix);		
		$this->load_echelps($current_hookname);
		$this->add_echelps($screen,$current_hookname);		
	}
	
	function get_hookname($screen,$hook_suffix){
		switch($hook_suffix){
			case 'edit.php':
				return $screen->post_type=='post'?'edit.php':sprintf('edit.php?post_type=%s',$screen->post_type);
			case 'post-new.php':
			case 'post.php':
				return $screen->post_type=='post'?'post-new.php':sprintf('post-new.php?post_type=%s',$screen->post_type);
			case 'edit-tags.php':
				return $screen->post_type=='post' || $screen->taxonomy=='link_category' ? sprintf('edit-tags.php?taxonomy=%s',$screen->taxonomy):sprintf('edit-tags.php?taxonomy=%s&post_type=%s',$screen->taxonomy,$screen->post_type);
			case 'media.php':
				return 'media-new.php';
			case 'link.php':
				return 'link-add.php';
			case 'appearance_page_theme_options':
				return 'theme_options';
			case 'appearance_page_custom-background':
				return 'custom-background';
			case 'appearance_page_custom-header':
				return 'custom-header';
			default:
				global $plugin_page;
				if(trim($plugin_page)!='')return $plugin_page;
				return $hook_suffix;
		}
	}
	
	function add_echelps($screen,$current_hookname){
		if(!empty($this->echelps)){
			foreach($this->echelps as $h){
				if(!empty($h->echelp_location)){			
					if(!in_array($current_hookname,$h->echelp_location))continue;
	
					if( '1'==$this->get_help_meta($h,'replace_existing_help',true) ){
						$screen->remove_help_tabs();
					}
					
					$content = apply_filters('the_content',$h->post_content);
					$content = strip_tags($content)==$content?"<p>$content</p>":$content;
					
					if( 'sidebar'==$this->get_help_meta($h,'help_type',true) ){
						$screen->set_help_sidebar( $content );
					}else{
						$screen->add_help_tab(array(
							'id'		=> 'echelp-'.$h->ID,
							'title'		=> $h->post_title,
							'content'	=> $content
						));					
					}
				}
			}		
		}		
	}
	
	function load_echelps($hookname){
		//------------------------------------------------------------------------------------------
		global $wpdb;
		$this->echelps = array();
		if(function_exists('is_multisite')&&is_multisite()){
			$tablename = sprintf("%sposts",$wpdb->get_blog_prefix(BLOG_ID_CURRENT_SITE));
			$postmeta = sprintf("%spostmeta",$wpdb->get_blog_prefix(BLOG_ID_CURRENT_SITE));
			$subselect = "AND((SELECT M.meta_value FROM $postmeta M WHERE M.post_id=P.ID AND M.meta_key='echelp_location' LIMIT 1) LIKE '%$hookname%')";
			if(false!==$wpdb->query("SELECT P.ID as ID, P.post_title as post_title, P.post_content as post_content FROM $tablename P WHERE P.post_type='echelpms' AND P.post_status='publish' $subselect ORDER BY P.menu_order ASC") && $wpdb->num_rows>0){
				foreach($wpdb->last_result as $h){
					$i = count($this->echelps);
					$h->ms = true;
					$this->echelps[$i]=$h;
					$this->echelps[$i]->echelp_location = $this->get_help_meta($h,'echelp_location');
					$this->echelps[$i]->echelp_location = is_array($this->echelps[$i]->echelp_location)?$this->echelps[$i]->echelp_location:array();
				}
			}
		}
		
		if(0==intval(get_site_option('ech_allow_subsite'))){			
			$tablename = $wpdb->posts;
			$postmeta = $wpdb->postmeta;
			$subselect = "AND((SELECT M.meta_value FROM $postmeta M WHERE M.post_id=P.ID AND M.meta_key='echelp_location' LIMIT 1) LIKE '%$hookname%')";
			if(false!==$wpdb->query("SELECT P.ID as ID, P.post_title as post_title, P.post_content as post_content FROM $tablename P WHERE post_type='echelp' AND post_status='publish' $subselect ORDER BY menu_order ASC") && $wpdb->num_rows>0){
				foreach($wpdb->last_result as $h){
					$i = count($this->echelps);
					$h->ms = false;
					$this->echelps[$i]=$h;
					$this->echelps[$i]->echelp_location = get_post_meta($h->ID,'echelp_location',true);
					$this->echelps[$i]->echelp_location = is_array($this->echelps[$i]->echelp_location)?$this->echelps[$i]->echelp_location:array();
				}
			}			
		}
		//------------------------------------------------------------------------------------------	
	}
	
	function get_help_meta($h,$meta_key){
		if($h->ms){
			global $wpdb;
			$postmeta = sprintf("%spostmeta",$wpdb->get_blog_prefix(BLOG_ID_CURRENT_SITE));
			return maybe_unserialize($wpdb->get_var("SELECT meta_value FROM $postmeta WHERE post_id={$h->ID} AND meta_key='$meta_key' LIMIT 1",0,0));
		}else{
			return get_post_meta($h->ID,$meta_key,true);
		}
	}	
} 

?>