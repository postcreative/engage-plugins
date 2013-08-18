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
		add_action('contextual_help', array(&$this,'contextual_help'), 100000, 3);
	}	
	
	function contextual_help($contextual_help, $screen_id, $screen){
		//------------------------------------------------------------------------------------------
		global $wpdb,$post,$plugin_page;
		$tmp_post = $post;	
	
		$this->echelps = array();
		if(function_exists('is_multisite')&&is_multisite()){
			$tablename = sprintf("%sposts",$wpdb->get_blog_prefix(BLOG_ID_CURRENT_SITE));
			if(false!==$wpdb->query("SELECT * FROM $tablename WHERE post_type='echelpms' AND post_status='publish' ORDER BY menu_order ASC") && $wpdb->num_rows>0){
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
			$local_help = get_posts('post_type=echelp&numberposts=-1&order=ASC&orderby=menu_order&post_status=publish');
			if(!empty($local_help)){
				foreach($local_help as $j => $h){
					$i = count($this->echelps);
					$h->ms = false;
					$this->echelps[$i]=$h;
					$this->echelps[$i]->echelp_location = get_post_meta($h->ID,'echelp_location',true);
					$this->echelps[$i]->ms = false;
				}		
			}
		}
		
		$post = $tmp_post;
		wp_reset_postdata();
		//------------------------------------------------------------------------------------------	
		if(!empty($this->echelps)){
			foreach($this->echelps as $h){
				if(!empty($h->echelp_location)){
					foreach($h->echelp_location as $hookname){
						if(property_exists($screen,'post_type')){
							$help_text = $this->_custom_post_type($h,$screen,$hookname);
						}else if(property_exists($screen,'taxonomy')){
							$help_text = $this->_taxonomy($h,$screen,$hookname);
						}else{
							$help_text = $this->_file($h,$screen,$hookname);
						}
				
						if(false!==$help_text){						
							if(1==$this->get_help_meta($h,'replace_existing_help',true)){
								$contextual_help=$help_text;
							}else{
								if(1==$this->get_help_meta($h,'help_prepend',true)){
									$contextual_help = $help_text.$contextual_help;
								}else{
									$contextual_help.=$help_text;
								}
							}						
						}
					}
				}
			}		
		}		
		return $contextual_help;
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
	
	function _help_text($post,$help_text){
		if(1==$this->get_help_meta($post,'show_headline',true)){
			$help_text = '<span id="help-text-title-'.$post->ID.'" class="help-text-title">'.$post->post_title.'</span>'.$help_text;
		}	
		return $help_text;
	}
	
	function _custom_post_type($post,$screen,$hookname){
		global $wp_query;
		$wp_query = is_object($wp_query)?$wp_query:new WP_Query();
		//---
		
		$ur = parse_url($hookname);
		$args=array();
		if(isset($ur['query']))parse_str($ur['query'],$args);
			
		if(isset($args['post_type'])){
			if($args['post_type']==$screen->post_type){
				//prevent a crash on the wp-e-commerce plugin
				remove_filter( "the_content", "wpsc_single_template", 12 );
				if($screen->base==str_replace('-new','',$this->get_filename($hookname))){
					$new_content = apply_filters('the_content',$post->post_content);
					return $this->_help_text($post,$new_content);
				}else if($screen->action=='add' && $this->get_filename($hookname)=='post-new'){
					$new_content = apply_filters('the_content',$post->post_content);
					return $this->_help_text($post,$new_content);
				}
			}
		}else{
			if($screen->id=='edit-post' && $hookname=='edit.php'){
				$new_content = apply_filters('the_content',$post->post_content);
				return $this->_help_text($post,$new_content);
			}else if($screen->id=='post' && $hookname=='post-new.php'){
				$new_content = apply_filters('the_content',$post->post_content);
				return $this->_help_text($post,$new_content);
			}		
		}		
		return false;
	}
	
	function _taxonomy($post,$screen,$hookname){
		$new_content = apply_filters('the_content',$post->post_content);
		$ur = parse_url($hookname);
		parse_str($ur['query'],$args);
		if(isset($args['taxonomy'])){
			if($args['taxonomy']==$screen->taxonomy){
				$ur2 = parse_url($screen->parent_file);
				parse_str($ur2['query'],$screen_parent_file_args);
				if(@$screen_parent_file_args['post_type']==@$args['post_type']){
					return $this->_help_text($post,$new_content);
				}
			}
		}	
		return false;
	}
	
	function _file($post,$screen,$hookname){
		remove_filter('the_content','prepend_attachment');
		$new_content = apply_filters('the_content',$post->post_content);

		if($screen->id=='dashboard' && $screen->parent_file=='index.php' && $hookname=='index.php'){
			return $this->_help_text($post,$new_content);
		}
		
		if($screen->id==$screen->base && $screen->id==$this->get_filename($hookname)){
			return $this->_help_text($post,$new_content);
		}
		
		$hook = get_plugin_page_hook( $hookname , $screen->parent_file );

		if(!empty($hook)&&$hook==$screen->id){
			return $this->_help_text($post,$new_content);
		}
		
		foreach(array('-add','-new') as $repl){
			if($screen->id==str_replace($repl,"",$this->get_filename($hookname))){
				return $this->_help_text($post,$new_content);
			}			
		}

		return false;
	}
	
	function get_filename($item2){
		$p = pathinfo($item2);
		return $p['filename'];
	}
}
?>