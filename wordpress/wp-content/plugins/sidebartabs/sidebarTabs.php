<?php
/*
Plugin Name: SidebarTabs
Plugin URI: http://www.blogviche.com.br/plugin-sidebartabs
Description: sidebarTabs allows you to easily widgets into Tabs
Author: Newton Horta
Version: 3.1
Author URI: http://www.blogviche.com.br

    sidebarTabs is released under the GNU General Public License (GPL)
    http://www.gnu.org/licenses/gpl.txt

	Utiliza parte das instruções do plugin postTabs de autoria de Leo Germani (http://pirex.com.br/wordpress-plugins/post-tabs)
    
*/

function sidebarTabs_init($reset=false){
	if(!get_option("sidebarTabs") || $reset){

		# Load default options
		$options["active_font"] = "#ffffff";
		$options["active_bg"] = "#464646";
		$options["inactive_font"] = "#464646";
		$options["inactive_bg"] = "#969696";
		$options["over_font"] = "#333333";
		$options["over_bg"] = "#e6e7e8";
		$options["line"] = "none";
		$options["height_tabs"] = "26";
		$options["width_icons"] = "39";
		$options["height_icons"] = "32";
		$options["width_sidebar"] = "200";
		$options["unit"] = "px";
		$options["bg_panes"] = "#464646";
		$options["color_links"] = "#c1c1c1";
		$options["color_hover_links"] = "#e18364";
		$options["text_color"] = "#ffffff";
		$options["layout"] = "1";
		$options["width_corner"] = "5";
		$options["typeCorner"] = "0";
		$options["fs"] = "11";
		$options["fw"] = "normal";
		$options["bvt"] = "0";
		$options["bht"] = "0";
		$options["margin_c"] = 'margin: 10px 0 10px 0';
		$options["args_theme"] ="1";
		$options["align_left"] ="0";
		$options["jqueryui"] ="0";
		update_option("sidebarTabs", $options);

		$sidebartabs_args = array(
					'before_widget' => "<div id='%1\$s' class='sbtw %2\$s'>",
					'after_widget' => '</div>',
					'before_title' => "<h2 class='widgettitle widget_title'>",
					'after_title' => "</h2>",
				); 
		update_option('sidebartabs_args', $sidebartabs_args);
	}

}


add_action('init', 'sidebarTabs_textdomain');
function sidebarTabs_textdomain() {
	load_plugin_textdomain('sidebartabs', false, dirname( plugin_basename(__FILE__) ) . '/langs');
}

function getSidebarTabs($atts) {
    extract(shortcode_atts(array(
    "instance" => '',
	"before_title" => '',
	"after_title" => ''
    ), $atts));
    $args = array('widget_id' => 'sidebartabs-'.$instance,'before_title' => $before_title,'after_title' => $after_title);
    return get_sidebarTabs($args,1,1);
}
add_shortcode('sidebartabs', 'getSidebarTabs');

function get_sidebarTabs($args, $dsb=0, $short_code=0) {
	global $wp_registered_widgets, $wp_registered_sidebars, $sidebars_widgets, $i_widget, $text_direction;
	$op = '';
	$text_direction = 'rtl';
	$stab = get_option("sidebartabs_widget");
	if (!is_array($stab)) return;
	$options = get_option("sidebarTabs");
	$widget_options = get_option("widget_sidebarTabs");
	$widget_id = explode('-',$args['widget_id']);
	$widget_id = $widget_id[1];
	if (!$widget_options[$widget_id]['display_sb'] && isset($widget_options[$widget_id]['display_sb']) && !$dsb) return;
	$abas = $widget_options[$widget_id]['orderSelect'];
	$layout_instance = $widget_options[$widget_id]['layout_instance'];
	$layout_horizontal = $widget_options[$widget_id]['layout_horizontal'];
	$effect = $widget_options[$widget_id]['effects'];
	if ($options['args_theme'] && !$short_code && !$dsb) $op .= $args['before_widget'];
	$exclude = $widget_options[$widget_id]['exclude'];
	if (!$exclude && !is_admin()) $op .= $args['before_title'] . $widget_options[$widget_id]['title'] . $args['after_title'];

	if (!$layout_horizontal) $layout_horizontal = $options["layout"];
	if ($layout_horizontal == 1) $class_horizontal = "fwithout_icons";
	if ($layout_horizontal == 2) $class_horizontal = "fwith_icons";
	if ($layout_horizontal == 3) $class_horizontal = "swithout_icons";
	if ($layout_horizontal == 4) $class_horizontal = "swith_icons";

	if (is_admin()) { 
		$class = 'title_sidebarTabs_admin';
		$op .= '<h2 class="'.$class.'">' . $widget_options[$widget_id]['title'] . ' (Id => ' . $widget_id . ')</h2>';
	}	

	if ($abas) $abas = explode(',',$abas);
	else {
		$widget_id = 1;
		ksort($stab);
		foreach ($stab as $i => $widget ) {
			$abas[$i] = $i;
		}
	}

	if ($layout_instance == 1) {	
		$op .= "\n<div class='sb_container sb_container_".$class_horizontal."'>\n";
		$op .= "   <a class='prev'></a>\n";
		$op .= "   <div class='scrollable scrollable".$widget_id." scrollable_".$class_horizontal."'>\n";
		$op .= "      <ul id='sidebarTabs_ul".$widget_id."' class='sidebarTabs sidebarTabs".$widget_id." sb_".$class_horizontal."'>\n";
	} else {
		$op .= "\n<div class='sb_accordion' id='accordion".$widget_id."'>\n";
	}
	$c_vert = 1;
	if ($text_domain == 'rtl') $abas = array_reverse($abas);
	if (is_array($abas)) { 
		foreach ($abas as $i => $aba ) {
			if (is_array($stab[$aba])) {
				$widget = $stab[$aba];
				if ($layout_instance == 1) {	
					$title_icons = '';
					if ($layout_horizontal == 2 || $layout_horizontal == 4)  {
						$title_icons = ' title="'.stripslashes($widget['sidebartabname']).'"';
						$op .= '        <li><a href="#tab'.$aba.'" class="sbicon'.$aba.'" id="sbicon'.$aba.'"'. $title_icons.'>'.stripslashes($widget['sidebartabname'])."</a></li>\n";
					} else {	
						$op .= '        <li><a href="#tab'.$aba.'" class="sb'.$aba.'" id="sb'.$aba.'"'. $title_icons.'>'.stripslashes($widget['sidebartabname'])."</a></li>\n";
					}	
				}
			}	
		}		
	}
	if ($layout_instance == 1) {	
		$op .= "      </ul>\n";
		$op .= "   </div>\n";
		if ($layout_horizontal == 3 || $layout_horizontal == 4) {
			$op .= "   <a class='next'></a>\n";
		}
		$op .= '   <div class="sidebarTabs_divs sidebarTabs_panes sidebarTabs_divs'.$widget_id.'">'."\n";
	} else {
		if ($c_vert == 1) {
			$c_style1 = ' current';
			$c_style2 = 'style="display:block;"';
		}	
	}	
	if (is_array($abas)) { 
		foreach ($abas as $i => $aba ) {
			$widget = $stab[$aba];
			if (is_array($stab[$aba])) {
				if ($layout_instance == 1) {	
					$op .= '      <div class="tb">'."\n";
				} else {	
					$op .= '   <h4 class="accordion_h4"><span>'.$widget['sidebartabname']."</span></h4>\n";
					$op .= '   <div class="pane">'."\n";
				}	
				$bw = stripslashes($widget['before_widget']);
				$aw = stripslashes($widget['after_widget']);
				$bt = stripslashes($widget['before_title']);
				$at = stripslashes($widget['after_title']);
				
				$bw = str_replace('widget','sbtw',$bw);  // fix problem - versions <= 3.0.1
	
				$widget_registered = $wp_registered_widgets[$widget['widget']];
				$params = array_merge(
					array( array_merge( array('widget_id' => $widget['widget'], 'widget_name' => $wp_registered_widgets[$widget['widget']]['name']) ) ),
					(array) $wp_registered_widgets[$widget['widget']]['params']
				);
				// Substitute HTML id and class attributes into before_widget
				$classname_ = '';
				foreach ((array)$widget_registered['classname'] as $cn) {
					if (is_string($cn)) $classname_ .= '_' . $cn;
					elseif (is_object($cn)) $classname_ .= '_' . get_class($cn);
				}
				if ($widget['unregister'] == 1) {
					if (function_exists(unregister_sidebar_widget)) unregister_sidebar_widget($widget['widget']);
				}	
				$classname_ = strtolower(ltrim($classname_, '_'));
				$params[0]['before_widget'] = sprintf($bw, $widget['widget'], $classname_);
				$params[0]['after_widget'] = $aw;
				$params[0]['before_title'] = $bt;
				$params[0]['after_title'] = $at;
				$check_widget = $widget['widget'];
				$check_widget = explode('-', $check_widget);
				if (isset($check_widget[1]) && is_numeric($check_widget[1])) $params[0]['number'] = $check_widget[1];
				if ( is_callable( $widget['callback'] ) ) {
					ob_start();
					call_user_func_array($widget['callback'], $params);
					$content_widget = ob_get_contents();
					ob_end_clean();
				}
				$op .= $content_widget;
				$op .= "      \n</div>\n\n";
				$c_vert++;
				$c_style1 = '';
				$c_style2 = '';
			}
		}
	}	
	if ($layout_instance == 1) {	
		$op .= "   </div>\n</div>\n";
	} else {
		$op .= "   </div>\n";
	}
	if ($effect == '') $effect = 'fade';
	if ($options['args_theme'] && !$short_code && !$dsb) $op .= $args['after_widget'];
	$op .= get_scripts_sidebarTabs($widget_id,$layout_instance,$layout_horizontal,$effect);

	if ($short_code) return $op;
	else echo $op;
}

add_action('wp_enqueue_scripts','sidebarTabs_addHeader');

function sidebarTabs_addHeader(){
	global $text_direction;
//	$text_direction = 'rtl';
	$sbto=get_option("sidebarTabs");
	echo "\n".'<!-- Start Of Script Generated By sidebarTabs 3.0 -->'."\n";
	if('rtl' == $text_direction) {
		$style = "<link rel=\"stylesheet\" href=\"" . get_bloginfo('wpurl') . "/wp-content/plugins/sidebartabs/styleSidebar_common-rtl.php\" type=\"text/css\" media=\"screen\" />\n";
		echo $style;
		$style = "<link rel=\"stylesheet\" href=\"" . get_bloginfo('wpurl') . "/wp-content/plugins/sidebartabs/styleSidebar_fixed-rtl.php\" type=\"text/css\" media=\"screen\" />\n";
		echo $style;
		$style = "<link rel=\"stylesheet\" href=\"" . get_bloginfo('wpurl') . "/wp-content/plugins/sidebartabs/styleSidebar_scrollable-rtl.php\" type=\"text/css\" media=\"screen\" />\n";
		echo $style;
		$style = '<link rel="stylesheet" href="' . get_bloginfo('wpurl').'/wp-content/plugins/sidebartabs/styleSidebar_icons-rtl.php" type="text/css" media="screen" />'."\n";
		echo $style;
	} else {
		$style = "<link rel=\"stylesheet\" href=\"" . get_bloginfo('wpurl') . "/wp-content/plugins/sidebartabs/styleSidebar_common.php\" type=\"text/css\" media=\"screen\" />\n";
		echo $style;
		$style = "<link rel=\"stylesheet\" href=\"" . get_bloginfo('wpurl') . "/wp-content/plugins/sidebartabs/styleSidebar_fixed.php\" type=\"text/css\" media=\"screen\" />\n";
		echo $style;
		$style = "<link rel=\"stylesheet\" href=\"" . get_bloginfo('wpurl') . "/wp-content/plugins/sidebartabs/styleSidebar_scrollable.php\" type=\"text/css\" media=\"screen\" />\n";
		echo $style;
		$style = '<link rel="stylesheet" href="' . get_bloginfo('wpurl').'/wp-content/plugins/sidebartabs/styleSidebar_icons.php" type="text/css" media="screen" />'."\n";
		echo $style;
	}	
	if (is_admin()) { ?>
		<script type="text/javascript" src="<?php bloginfo('wpurl'); ?>/wp-content/plugins/sidebartabs/js/jquery.corner.js"></script>
	<?php		
		if ($sbto['jqueryui']) { ?>
		<script type="text/javascript" src="<?php bloginfo('wpurl'); ?>/wp-content/plugins/sidebartabs/js/jquery.tools.min.m.js"></script>	
	<?php } else { ?> 	
		<script type="text/javascript" src="<?php bloginfo('wpurl'); ?>/wp-content/plugins/sidebartabs/js/jquery.tools.min.js"></script>	
<?php
		  }
	}
	else {
		wp_enqueue_script('jquery');
		if ($sbto['jqueryui']) {
			wp_enqueue_script('toolsui', plugins_url('sidebartabs/js/jquery.tools.min.m.js'), array('jquery'), '1.1.0');
		} else {
			wp_enqueue_script('tools', plugins_url('sidebartabs/js/jquery.tools.min.js'), array('jquery'), '1.1.0');
		}
		wp_enqueue_script('corner', plugins_url('sidebartabs/js/jquery.corner.js'), array('jquery'), '2.01');
		wp_enqueue_script('sidebartabs', plugins_url('sidebartabs/js/sidebartabs.js'), array('jquery'), '2.4');
	}
	echo '<!-- End Of Script Generated By sidebarTabs -->'."\n";
}

add_action('admin_enqueue_scripts', 'sidebarTabs_admin_addHeader');

function sidebarTabs_admin_addHeader($hook_suffix){
	global $text_direction;
//	$text_direction = 'rtl';
	$sidebarTabs_hook_suffix = array('sidebartabs/sidebarTabs_admin.php','sidebartabs/sidebarTabs.php', 'settings_page_sidebarTabs');
	if(in_array($hook_suffix, $sidebarTabs_hook_suffix)) {
		$sb_options=get_option("sidebarTabs");
		if ('rtl' == $text_direction) { 
			$style = "<link rel=\"stylesheet\" href=\"" . get_bloginfo('wpurl') . "/wp-content/plugins/sidebartabs/styleSidebar_admin-rtl.php\" type=\"text/css\" media=\"screen\" />\n";
		echo $style;
		} else {
			$style = "<link rel=\"stylesheet\" href=\"" . get_bloginfo('wpurl') . "/wp-content/plugins/sidebartabs/styleSidebar_admin.php\" type=\"text/css\" media=\"screen\" />\n";
		echo $style;
		}
		wp_enqueue_script('jquery');
		wp_enqueue_script('301a', plugins_url('sidebartabs/js/301a.js'), array('jquery'), '301a');
		wp_enqueue_script('corner', plugins_url('sidebartabs/js/jquery.corner.js'), array('jquery'), '2.01');
		wp_enqueue_script('sidebartabs', plugins_url('sidebartabs/js/sidebartabs.js'), array('jquery'), '2.4');
	}
}

function sidebarTabs_admin() {
	if (function_exists('add_options_page')) {
		add_options_page('sidebarTabs Options', 'sidebarTabs', 8, basename(__FILE__), 'sidebarTabs_admin_page');
	}
}

function sidebarTabs_admin_page() {
	
	require_once("sidebarTabs_admin.php");

}
### Class: sidebarTabs Widget
 class WP_Widget_sidebarTabs extends WP_Widget {
	// Constructor
	function WP_Widget_sidebarTabs() {
		$widget_ops = array('description' => __('Put widgets into tabbed (scrollable) interface in sidebar', 'sidebartabs'));
		$this->WP_Widget('sidebarTabs', __('sidebarTabs', 'sidebartabs'), $widget_ops);
	}

	// Display Widget
	function widget($args, $instance) {
		get_sidebarTabs($args);		
	}

	// When Widget Control Form Is Posted
	function update($new_instance, $old_instance) {
		if (!isset($new_instance['submit'])) {
			return false;
		}
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['exclude'] = $new_instance['exclude'];
		$instance['orderSelect'] = strip_tags($new_instance['orderSelect']);
		$instance['layout_instance'] = strip_tags($new_instance['layout_instance']);
		$instance['layout_horizontal'] = strip_tags($new_instance['layout_horizontal']);
		$instance['effects'] = strip_tags($new_instance['effects']);
		$instance['display_sb'] = strip_tags($new_instance['display_sb']);
		return $instance;
	}

	// DIsplay Widget Control Form
	function form($instance) {
		global $wpdb;
		$instance = wp_parse_args((array) $instance, array('title' => __('Instance 1', 'sidebartabs'), 'exclude' => '', 'orderSelect' => ''));
		$title = esc_attr($instance['title']);
		$exclude = $instance['exclude'] ? 'checked="checked"' : '';
		$orderSelect = esc_attr($instance['orderSelect']);
		$layout_instance = esc_attr($instance['layout_instance']);
		$layout_horizontal = esc_attr($instance['layout_horizontal']);
		$effects = esc_attr($instance['effects']);
		$display_sb = esc_attr($instance['display_sb']);
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'sidebartabs'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('exclude'); ?>"><?php _e('Exclude Title:', 'sidebartabs'); ?> <input id="<?php echo $this->get_field_id('exclude'); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" type="checkbox" <?php echo $exclude; ?>  /></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('orderSelect'); ?>"><?php _e('Orders sidebarTabs:', 'sidebartabs'); ?> <input class="widefat" id="<?php echo $this->get_field_id('orderSelect'); ?>" name="<?php echo $this->get_field_name('orderSelect'); ?>" type="text" value="<?php echo $orderSelect; ?>" /></label><br />
			<small><?php _e('Seperate mutiple sidebarTabs orders with commas. Leave blank for all.', 'sidebartabs'); ?></small>		</p>
		<p>
			<label for="<?php echo $this->get_field_id('layout_instance'); ?>"><?php _e('Layout of Instance:', 'sidebartabs'); ?>
				<select name="<?php echo $this->get_field_name('layout_instance'); ?>" id="<?php echo $this->get_field_id('layout_instance'); ?>" class="widefat">
					<option value="1"<?php selected('1', $layout_instance); ?>><?php _e('Horizontal', 'sidebartabs'); ?></option>
					<option value="2"<?php selected('2', $layout_instance); ?>><?php _e('Vertical', 'sidebartabs'); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('layout_horizontal'); ?>"><?php _e('Layout of Horizontal Tabs:', 'sidebartabs'); ?>
				<select name="<?php echo $this->get_field_name('layout_horizontal'); ?>" id="<?php echo $this->get_field_id('layout_horizontal'); ?>" class="widefat">
					<option value="1"<?php selected('1', $layout_horizontal); ?>><?php _e('Fixed without icons', 'sidebartabs'); ?></option>
					<option value="2"<?php selected('2', $layout_horizontal); ?>><?php _e('Fixed with icons', 'sidebartabs'); ?></option>
					<option value="3"<?php selected('3', $layout_horizontal); ?>><?php _e('Scrollable without icons', 'sidebartabs'); ?></option>
					<option value="4"<?php selected('4', $layout_horizontal); ?>><?php _e('Scrollable with icons', 'sidebartabs'); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('effects'); ?>"><?php _e('Effect (Only vertical instances):', 'sidebartabs'); ?>
				<select name="<?php echo $this->get_field_name('effects'); ?>" id="<?php echo $this->get_field_id('effects'); ?>" class="widefat">
					<option value="default"<?php selected('default', $effects); ?>><?php _e('default', 'sidebartabs'); ?></option>
					<option value="fade"<?php selected('fade', $effects); ?>><?php _e('fade', 'sidebartabs'); ?></option>
					<option value="slide"<?php selected('slide', $effects); ?>><?php _e('slide', 'sidebartabs'); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('display_sb'); ?>"><?php _e('Display in Sidebar:', 'sidebartabs'); ?>
				<select name="<?php echo $this->get_field_name('display_sb'); ?>" id="<?php echo $this->get_field_id('display_sb'); ?>" class="widefat">
					<option value="1"<?php selected('1', $display_sb); ?>><?php _e('Yes', 'sidebartabs'); ?></option>
					<option value="0"<?php selected('0', $display_sb); ?>><?php _e('No', 'sidebartabs'); ?></option>
				</select>
			</label>
		</p>
		<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php
	}
}


### Function: Init sidebarTabs Widget
add_action('widgets_init', 'widget_sidebarTabs_init');
function widget_sidebarTabs_init() {
	register_widget('WP_Widget_sidebarTabs');
}

function get_scripts_sidebarTabs($id_w, $layout_instance='1', $layout_horizontal, $effect) {
	$options_script = get_option("sidebarTabs");
	if (!$options_script['jqueryui']) $sbtabs = '.tabs';
	else $sbtabs = '.sbTabs';
	$cookies = $options_script['cookies'];
	$expires = ($options_script['expires'] ? $options_script['expires'] : 0);
	if ($cookies && $expires == 0) $expires = 365;
	
	$gss = "\n<script type='text/javascript'>"."\n";
	$gss .= '/* <![CDATA[ */'."\n";
	if ($cookies) {
		$gss .= 'var c=sbCookie.get("sidebarTabs'.$id_w.'");';
		$gss .= 'if(c==undefined || c=="")
			ind'.$id_w.'=0;
		else
			ind'.$id_w.'=parseInt(c);'."\n";
		$cookie_parm = '	initialIndex: ind'.$id_w.',
						    onClick: function(tabIndex) {
        						sbCookie.set("sidebarTabs'.$id_w.'",tabIndex,'.$expires.',"/"); 
    						} 
		';
	}
	else {
		$gss .= '  ind'.$id_w.'=0;';
		$cookie_parm = '    initialIndex: 0';
	}	
	$gss .= '   jQuery(document).ready(function(){'."\n";
	if ($layout_instance == 1) {
		if (($layout_horizontal == 1 || $layout_horizontal == 3) && $options_script['typeCorner']) {
	
		$gss .= '      jQuery("ul.sidebarTabs'.$id_w.' li a").corner("'.$options_script['typeCorner'].' '.$options_script['width_corner'].'px");'."\n";

		}
		if ($layout_horizontal == 3 || $layout_horizontal == 4) {
		$gss .= '      var tabInicial'.$id_w.' = jQuery("ul.sidebarTabs'.$id_w.' li a:eq(0)").offset().left;'."\n";
		$gss .= '      jQuery("ul.sidebarTabs'.$id_w.'")'.$sbtabs.'("div.sidebarTabs_divs'.$id_w.' > .tb", {initialIndex: ind'.$id_w.'});'."\n";
		
	// enabling scrollable
		$gss .= '      jQuery("div.scrollable'.$id_w.'").scrollable({
						  onReload: function() { 
							  this.seekTo(ind'.$id_w.');
						  }, 
						  onSeek: function(e) { 
							  var api = jQuery("ul.sidebarTabs'.$id_w.'")'.$sbtabs.'(0) 
							  api.click(e);
							  sbCookie.set("sidebarTabs'.$id_w.'",e,'.$expires.',"/"); 
						  }, 
						  size: 1,
						  items: "#sidebarTabs_ul'.$id_w.'",
						  clickable: true,
						  keyboard: false
					   }); 
					   jQuery("#sidebarTabs_ul'.$id_w.' li a").click(function(e) { 
					      var indice = jQuery("#sidebarTabs_ul'.$id_w.' li a").index(this);
						  var apis = jQuery("div.scrollable'.$id_w.'").scrollable(0); 
						  if (jQuery(this).offset().left>tabInicial'.$id_w.') apis.seekTo(indice); 
		               }); ';
	
		} else {
	
		$gss .= '      jQuery("ul.sidebarTabs'.$id_w.'")'.$sbtabs.'("div.sidebarTabs_divs'.$id_w.' > .tb", {
							'.$cookie_parm.'
					   });';

		}
	} else {

		$gss .= '      jQuery("#accordion'.$id_w.'")'.$sbtabs.'("#accordion'.$id_w.' div.pane", {'.str_replace('.','',$sbtabs).': "h4.accordion_h4", effect: "'.$effect.'", 
							'.$cookie_parm.'
						});';

	}	

$gss .= '		
   });
/* ]]> */
</script>';

return $gss;
}

register_activation_hook( __FILE__, 'sidebarTabs_init' );

//add_action('plugins_loaded', 'registerWidgets');

add_action('admin_menu','sidebarTabs_admin');
?>
