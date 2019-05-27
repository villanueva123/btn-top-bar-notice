<?php

  /**
   * Front Enqueue
   * @since    1.0.0
   */

	 add_action( 'wp_enqueue_scripts', 'front_end_style' );
		function front_end_style(){
			wp_enqueue_style('front-end-css', plugin_dir_url( __FILE__ ) . 'css/ctb_frontend.css', array(), 'all' );
		}



	 //Display using prepend javascript
	 add_action('wp_footer', 'display_alert_box_func_content');
 	function display_alert_box_func_content(){
 				$topbars = get_option('btn-top-bar-notice');
				$container = get_option('container_class');
 				$html .='' ;
				$html1 .='' ;
				$html .= "<script>
				  var container, htmlContent = document.createElement('div');";
					 $html .="container = document.querySelector('".$container."');";
			 				foreach ($topbars as $s => $setting) {
			 						$tag_id = $setting['tag_id'];
			 						$message = $setting['content'];
			 						$content_color = $setting['cotent-color'];
			 						$bg = $setting['color-picker'];
			 						$current_date = strtotime( date("Y-m-d") );
			 						$end_date = strtotime($setting['date-picker']);
			 						$start_date = strtotime($setting['start-date-picker']);

			 						if($setting['active'] == 1){
			 							$condition = do_shortcode('[memb_has_any_tag tagid='.$tag_id.']');
				 						if($condition == 'Yes'){
				 							 if($setting['date-picker'] =='' || $current_date >= $start_date && $current_date <= $end_date){

												 $html1 .= '<div class="alert-box-top-bar" id="alert-box'.$tag_id .'" style="background-color:'.$bg.';"><div class="alert-container">'.$message.'</div></div>';
				 								 $html .='(function($) {$("#alert-box'.$tag_id.' p,#alert-box'.$tag_id.' a,#alert-box'.$tag_id.'").css("color","'.$content_color.'");})( jQuery );';
				 							 }
				 						 }
			 						}
			 				}
				$html .="htmlContent.innerHTML = '".$html1."';
										container.insertBefore(htmlContent, container.firstChild);

						</script>";
 			 	echo $html;
 	 }
