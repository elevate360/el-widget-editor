<?php
 /**
 * Basic Wysiwyg editor in a widget
 * 
 * Used to provide an editor widget to be used in the sidebar
 * TODO: adjust this more to make the editor better
 */
 
 class el_editor_widget extends WP_Widget{
 	
	public function __construct(){
			
		$args = array(
			'description'	=> 'Creates a simple editor widget to let you add HTML / Content into widget areas'
		);
		
		parent::__construct(
			'el_editor_widget', esc_html__('Basic WYSIWYG Widget', 'ycc'), $args
		);
		
	}

	
	/**
	 * Visual output frontend
	 */
	public function widget($args, $instance){
		
		$title = isset($instance['title']) ? $instance['title'] : '';
		$editor = isset($instance['editor']) ? $instance['editor'] : '';
		
		$html = '';
		
		$html .= $args['before_widget'];
				
			$html .= '<div class="widget-wrap el-col-small-12 small-padding-top-bottom-small">';
			
				//title if supplied
				if(isset($instance['title'])){
					$html .= $args['before_title'];
						$html .= $title;
					$html .= $args['after_title'];	
				}
		
				//main content
				$html .= '<div class="widget-content ">';
					$html .= apply_filters('the_content', $editor);
				$html .= '</div>';
					
			
			$html .= '</div>';
		
		$html .= $args['after_widget'];
		
		
		echo $html;
		
		
	}
	
	/**
	 * Form output on admin
	 * 
	 * TODO: Come back and clean this up, experimental
	 * @link https://codex.wordpress.org/TinyMCE
	 * @link http://wordpress.stackexchange.com/questions/82670/why-cant-wp-editor-be-used-in-a-custom-widget
	 * @link http://wordpress.stackexchange.com/questions/227165/wp-editor-in-widget-breaks-after-save-no-buttons-and-visual-tab-broken
	 */
	public function form($instance){
		
		//enqueue media scripts
		wp_enqueue_media();	

		$title = isset($instance['title']) ? $instance['title'] : '';
		$editor = isset($instance['editor']) ? $instance['editor'] : '';
		
		$html = '';
		
		
		$html .= '<p>';
			$html .= '<label for="' . $this->get_field_id('title') . '">' . __('Title', 'ycc') .'</label>';
			$html .= '<input class="widefat" type="text" name="' . $this->get_field_name('title') . '" id="' . $this->get_field_id('title') . '" value="' . $title .'"/>';
		$html .= '</p>';
		

		//Editor
		$html .= '<div>';
		
			//add a trigger to the TinyMCE element to ensure editor is saved when editing terms
			//http://wordpress.stackexchange.com/questions/39594/wp-editor-textarea-value-not-updating
			
			
			$widget_id = $this->id;
			
			//Name of this widget instance
			$random = rand( 0, 999 );
			$id = $this->get_field_id('editor_' . $random);
			$name = $this->get_field_name('editor_' . $random);
			
			//Handle the re-init process for tiny MCE after save,
			//Handle saving of tiny MCE field when about to save (else the value never gets updated)
			$html .= '<script type="text/javascript">';
			$html .= 'jQuery(document).ready(function($){
							
						options = {
							selector: "textarea[id*=' . $id .']",
							height: 400,
            				theme: "modern",
            				plugins: "tabfocus,paste,media,wordpress,wpeditimage,wpgallery,wplink,wpdialogs",
            				toolbar1: "bold,italic,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,wp_more,spellchecker,wp_fullscreen,wp_adv",
            				toolbar2: "formatselect,underline,alignjustify,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help"
						};
						
						//tinyMCE.init(options);
						
						//console.log("#' . $id . '");
						
						//save when hovering over the submit button
						$("#widget-' . $widget_id . '-savewidget").on("hover", function(){
							tinyMCE.triggerSave();
							console.log("Hover save");
						});
						
						//on update, set the widget area back up again
						$(document).on("widget-updated", function (event, $widget) {
							console.log("Updasted widget");
							tinyMCE.remove();
							tinyMCE.init(options);
						});

					  });';
			$html .= '</script>';
			

		
			$html .= '<label for="' . $id  . '">' . __('Content', 'ycc') . '</label>';
			$html .= '<input type="hidden" id="' . $this->get_field_id('editor_number') .'" name="' . $this->get_field_name('editor_number'). '" value="' . $random .'" />'; 
			ob_start();
			$editor_args = array(
				'textarea_name'		=> $name,
				'textarea_rows'		=> 10,
				'teeny'				=> true
			);
			
			wp_editor($editor, $id, $editor_args);
			$markup = ob_get_clean();
			
			$html .= $markup;
		
		$html .= '</div>';
		
		echo $html;
	}
	
	/**
	 * Save callback
	 */
	public function update($new_instance, $old_instance){
		
		$instance = array();
			
		$instance['title'] = isset($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
		$instance['editor_number'] = isset($new_instance['editor_number']) ? $new_instance['editor_number'] : '';
		$field_name = 'editor_' . $instance['editor_number'];
		$instance['editor'] = isset($new_instance[$field_name]) ? $new_instance[$field_name] : '';
		

		
		return $instance;
		
	}
	
 }




?>