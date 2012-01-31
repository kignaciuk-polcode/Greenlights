<?php
//require_once(ABSPATH . 'wp-admin/includes/widgets.php');

global $wp_registered_widgets, $sidebars_widgets, $text_direction;

$stab = get_option("sidebartabs_widget");
$sargs = get_option("sidebartabs_args");
$total_tabs = count($stab);
if (!is_array($stab)) $total_tabs = 0;
$complete_message =  '<div id="message" class="updated fade"><p>';
$success_message = '<strong>'.__('Save successful.','sidebartabs').'</strong>'; 
$error_message = '<strong>'.__('Error!','sidebartabs').'</strong><br>';
$delete_message = __('sidebarTab successfully deleted.','sidebartabs');
$update_message = __('sidebarTab successfully updated.','sidebartabs');
$save_options = '<strong>'.__('sidebarTabs Options successfully Updated!','sidebartabs').'</strong>';
$save_args = '<strong>'.__('args successfully Updated!','sidebartabs').'</strong>';
$path = admin_url() . 'options-general.php';

if (!empty($_POST['action'])) $_GET['action'] = $_POST['action'];

if(!empty($_GET['action'])) {
	// Decide What To Do
	switch($_GET['action']) {
		case 'args':
			if ($_POST['submit_args']) {
				$options["before_widget"] = str_replace('"',"'",$_POST['beforeWidget']);
				$options["after_widget"]  = str_replace('"',"'",$_POST['afterWidget']);
				$options["before_title"]  = str_replace('"',"'",$_POST['beforeTitle']);
				$options["after_title"]   = str_replace('"',"'",$_POST['afterTitle']);
				update_option("sidebartabs_args", $options);
				$message_args = $save_args;	
				echo '<div id="message" class="updated fade"><p>'.$message_args.'</p></div>';		
			}
			$default_args = get_option('sidebartabs_args');
	?>
			<div class="wrap">
				<div class="icon32 icon-sidebarTabs">
				<br/>
				</div>
				<h2><?php echo _e('Default args','sidebartabs'); ?></h2>
				<div class="tablenav">
					<div class="alignleft">
						<a class="button-highlighted action_buttons" href="<?php echo $path ?>?page=sidebarTabs.php">&laquo; <?php _e('Back to Manage sidebarTabs','sidebartabs') ?></a>
						<br class="clear" />
					</div>
				</div>
				<form name="blogform" method="post" action="<?php echo $path ?>?page=sidebarTabs.php&amp;action=args"> 
				    <div class="stuffbox metabox-holder" style="padding-top:0;">
						<h3>Edit</h3>
						<table class="form-table">
							<tr valign="top">
								<th scope="row"><?php _e('Before Widget','sidebartabs'); ?>:</th>
								<td>
										<input type="text" name="beforeWidget" value="<?php echo stripslashes($default_args['before_widget']) ?>" size="50" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php _e('After Widget','sidebartabs'); ?>:</th>
								<td>
										<input type="text" name="afterWidget" value="<?php echo stripslashes($default_args['after_widget']) ?>" size="50" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php _e('Before Title','sidebartabs'); ?>:</th>
								<td>
										<input type="text" name="beforeTitle" value="<?php echo stripslashes($default_args['before_title']) ?>" size="50" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php _e('After Title','sidebartabs'); ?>:</th>
								<td>
										<input type="text" name="afterTitle" value="<?php echo stripslashes($default_args['after_title']) ?>" size="50" />
								</td>
							</tr>
						</table>
						<br />
                    </div>		
					<span class="submit"><input type="submit" name="submit_args" value="<?php echo _e('Update','sidebartabs'); ?> &raquo;" /></span>
				    <input type="hidden" name="action" value="args" />
				</form>	
			</div>	
<?php
		break;
		
		case 'delete':
			$message = true;
			$complete_message .= $delete_message;
			if (isset($_POST) AND is_array($_POST)) {
				foreach ($_POST as $n => $v) {
					if (substr($n,0,4) == "del_" AND $v == 'on') {
						unset($stab[substr($n,4)]);
					}
				}
			}
			$temp = array_values($stab);
			$stab = NULL;
			foreach ($temp as $i => $v) {
				$stab[$i+1] = $v;
			}
			update_option('sidebartabs_widget',$stab);
			if (!is_array($stab)) $total_tabs = 0;
			else $total_tabs = $i+1;
            break;
			
		case 'edit':
			if ($_GET["new"] == false) {
			    $stab = get_option('sidebartabs_widget');
				$stab_widget = $stab[$_GET['tab']];
				$before_widget = $stab_widget['before_widget'];
				$after_widget = $stab_widget['after_widget'];
				$before_title = $stab_widget['before_title'];
				$after_title = $stab_widget['after_title'];
				$title = __("SidebarTab","sidebartabs");
				$subtitle = __("Edit","sidebartabs");
				$sidebarTabs_status = is_active_widget( $wp_registered_widgets[$stab_widget['widget']]['callback'], $wp_registered_widgets[$stab_widget['widget']]['id']) ? 'active' : 'inactive';
			}
			else {
				$before_widget = $sargs['before_widget'];
				$after_widget = $sargs['after_widget'];
				$before_title = $sargs['before_title'];
				$after_title = $sargs['after_title'];
				$title = __("SidebarTab","sidebartabs");
				$subtitle = __("New","sidebartabs");
			}
?>
			<div class="wrap">
				<div class="icon32 icon-sidebarTabs">
				<br/>
				</div>
				<h2><?php echo $title ?></h2>
				<div class="tablenav">
					<div class="alignleft">
						<a class="button-highlighted action_buttons" href="<?php echo $path ?>?page=sidebarTabs.php">&laquo; <?php _e('Back to Manage sidebarTabs','sidebartabs') ?></a>
						<br class="clear" />
					</div>
				</div>
				<form name="blogform" method="post" action="<?php echo $path ?>?page=sidebarTabs.php&amp;action=save&amp;otab=<?php echo $_GET['tab'] ?>"> 
				    <div class="stuffbox metabox-holder" style="padding-top:0;">
						<h3><?php echo $subtitle; ?></h3>
						<table class="form-table">
							<tr valign="top">
								<th scope="row"><?php echo _e('Tab Number','sidebartabs'); ?></th>
								<td><input name="number" type="text" value="<?php echo $_GET["tab"] ?>" size="3" /> <?php echo _e("The order in which the sidebartab will appear in your tab bar","sidebartabs"); ?></td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php echo _e('Widget:','sidebartabs'); ?></th>
								<td>
									<select name="widget">
								<?php 
								ksort($wp_registered_widgets);
								foreach ( $wp_registered_widgets as $widget ) { 
	//								if (!$widget['callback'] || is_array($widget['callback'])) continue;
									$name = $widget['name']; 
									if ($name == 'sidebarTabs') continue;
									$iname = explode('-',$widget['id']);
									$options = get_option("widget_".$iname[0]);									
									$title = $options[$iname[1]]['title']; 
							?>
										<option value="<?php echo $widget['id']; ?>" <?php  if (!$_GET['new'] && $widget['id'] == $stab_widget['widget']) echo 'selected="selected"' ?>><?php echo $name." (".($title ? $title : $widget['id']).")"; ?></option>
							<?php
								}
							?>
									</select>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php _e('Tab name','sidebartabs'); ?>:</th>
								<td>
										<input type="text" name="sidebarName" value="<?php echo stripslashes($stab_widget['sidebartabname']) ?>" size="50" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php _e('Unregister Widget','sidebartabs'); ?>:</th>
								<td>
										<input type="checkbox" name="unregister" value="1" <?php if ("1" == $stab_widget["unregister"]) echo "checked=\"checked\""; ?> /> <?php if (!$_GET['new']) echo ' Widget is '. $sidebarTabs_status; ?>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php _e('Before Widget','sidebartabs'); ?>:</th>
								<td>
										<input type="text" name="beforeWidget" value="<?php echo stripslashes($before_widget) ?>" size="50" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php _e('After Widget','sidebartabs'); ?>:</th>
								<td>
										<input type="text" name="afterWidget" value="<?php echo stripslashes($after_widget) ?>" size="50" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php _e('Before Title','sidebartabs'); ?>:</th>
								<td>
										<input type="text" name="beforeTitle" value="<?php echo stripslashes($before_title) ?>" size="50" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php _e('After Title','sidebartabs'); ?>:</th>
								<td>
										<input type="text" name="afterTitle" value="<?php echo stripslashes($after_title) ?>" size="50" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php _e('Icon','sidebartabs'); ?>:</th>
								<td>
									<input type="radio" name="icon" value="0" <?php if ("0" == $stab_widget["icon"]) echo "checked=\"checked\""; ?> /> <span class="sb_icon" style="padding-right: 10px;"><?php _e('No','sidebartabs'); ?></span>
								<?php
								$listImages = get_list_images(ABSPATH."/wp-content/plugins/".dirname( plugin_basename(__FILE__) ) . '/icons');
								foreach ($listImages as $icon) { ?>
									<input type="radio" name="icon" value="<?php echo $icon; ?>" <?php if ($icon == $stab_widget["icon"]) echo "checked=\"checked\""; ?> /> <img src="<?php echo plugins_url('sidebartabs/icons/').$icon; ?>" class="sb_icon" alt="" />
								<?php
								}
								?>
								</td>
							</tr>
						</table>
						<br />
					</div>	
					<?php
					if ($_GET["new"] == true) { ?>
						<span class="submit"><input type="submit" name="submit" value="<?php echo _e('Save','sidebartabs') ?> &raquo;" /></span>
					<?php } else { ?>
						<span class="submit"><input type="submit" name="submit" value="<?php echo _e('Update','sidebartabs'); ?> &raquo;" /></span>
						<input type="hidden" name="update" value="1" />
					<?php } ?>
				<?php if (isset($_POST['submit_sidebarTab'])) print("<script>sidebarTabs_preview();</script>"); ?>
				</form>
			</div>
			<?php	
		    break;
		
		case 'options':		
			if (isset($_POST['submit_sidebarTab'])) {	
				$options["active_font"] = $_POST['active_font'];
				$options["active_bg"] = $_POST['active_bg'];
				$options["inactive_font"] = $_POST['inactive_font'];
				$options["inactive_bg"] = $_POST['inactive_bg'];
				$options["over_font"] = $_POST['over_font'];
				$options["over_bg"] = $_POST['over_bg'];
				$options["line"] = $_POST['line'];
				$options["height_tabs"] = $_POST['height_tabs'];
				$options["height_icons"] = $_POST['height_icons'];
				$options["width_icons"] = $_POST['width_icons'];
				$options["width_sidebar"] = $_POST['width_sidebar'];
				$options["unit"] = $_POST['unit'];
				$options["bg_panes"] = $_POST['bg_panes'];
				$options["color_links"] = $_POST['color_links'];
				$options["color_hover_links"] = $_POST['color_hover_links'];
				$options["text_color"] = $_POST['text_color'];
				$options["layout"] = $_POST["layout"];
				$options["width_corner"] = $_POST['width_corner'];
				$options["typeCorner"] = $_POST['typeCorner'];
				$options["fw"] = $_POST['fw'];
				$options["fs"] = $_POST['fs'];
				$options["ff"] = str_replace('"',"'",$_POST['ff']);
				$options["bvt"] = $_POST['bvt'];
				$options["bht"] = $_POST['bht'];
				$options["margin_c"] = $_POST['margin_c'];
				$options["args_theme"] = $_POST['args_theme'];
				$options["align_left"] = $_POST['align_left'];
				$options["jqueryui"] = $_POST['jqueryui'];
				$options["cookies"] = ($_POST['cookies']=="1") ? "1" : "0";
				$options["expires"] = $_POST['expires'];
				update_option("sidebarTabs", $options);
				$message_options = $save_options;	
				echo '<div id="message" class="updated fade"><p>'.$message_options.'</p></div>';		
			}
			$options=get_option("sidebarTabs");
			if (!$options["margin_c"] && !isset($options["margin_c"])) $margin_c = 'margin: 10px 0 10px 0';
			else $margin_c = $options["margin_c"];
			write_css_icons();
?>		

<script type="text/javascript">
function sidebarTabs_preview(){
	tabs = new Array("active","inactive","over");
	
	document.getElementById("sidebarTabs_admin").style.borderBottom="1px solid "+(document.sidebarTabsOptions.line.value == 'none') ? "" : document.sidebarTabsOptions.line.value;	
	document.getElementById("sidebarTabs_admin_active").style.border="1px solid "+(document.sidebarTabsOptions.line.value == 'none') ? "" : document.sidebarTabsOptions.line.value;	
	document.getElementById("sidebarTabs_admin_inactive").style.border="1px solid "+(document.sidebarTabsOptions.line.value == 'none') ? "" : document.sidebarTabsOptions.line.value;	
	document.getElementById("sidebarTabs_admin_over").style.border="1px solid "+(document.sidebarTabsOptions.line.value == 'none') ? "" : document.sidebarTabsOptions.line.value;

	for(y=0; y<tabs.length; y++){
		document.getElementById("sidebarTabs_admin_"+tabs[y]).style.backgroundColor=document.sidebarTabsOptions.elements[tabs[y]+"_bg"].value;
	}
	for(y=0;y<tabs.length;y++){
		document.getElementById("sidebarTabs_admin_"+tabs[y]).style.color=document.sidebarTabsOptions.elements[tabs[y]+"_font"].value;
	}
	
	document.getElementById("sidebarTabs_admin_preview").style.backgroundColor=document.sidebarTabsOptions.active_bg.value;	
	document.getElementById("sidebarTabs_admin_active").style.borderBottom="1px solid "+document.sidebarTabsOptions.active_bg.value;		
}

function sidebarTabs_preview_align(dir){

	document.getElementById("sidebarTabs_admin").style.textAlign=dir;
	if(dir=="center") document.getElementById("sidebarTabs_admin").style.paddingLeft="0px";
	else document.getElementById("sidebarTabs_admin").style.paddingLeft="20px";

}
function editTab(val,name) {
   if (val==1) document.getElementById("sidebarTabs_admin_active").innerHTML = name;
   if (val==2) document.getElementById("sidebarTabs_admin_inactive").innerHTML = name;
   if (val==3) document.getElementById("sidebarTabs_admin_over").innerHTML = name;
}
function toggle_check(c) {
	c=c.checked;
	t = document.getElementById('manage_tabs_form').getElementsByTagName('input');
	for(var i=0;i<t.length;i++) {
		if (t[i].name.substring(0,4)=='del_') {
			t[i].checked=c;
		}
	}
}
</script>

	<div class="wrap">
		<div class="icon32 icon-sidebarTabs">
		<br/>
		</div>
		<h2><?php echo _e('Options','sidebartabs'); ?></h2>
		<div class="tablenav">
			<div class="alignleft">
				<a class="button-highlighted action_buttons" href="<?php echo $path ?>?page=sidebarTabs.php">&laquo; <?php _e('Back to Manage sidebarTabs','sidebartabs') ?></a>
				<br class="clear" />
			</div>
		</div>
			
		<form name="sidebarTabsOptions" method="post" action="">
			<p style="color:#990000"><?php echo _e("Attention: D'ont forget to Save the changes after you are done. Always save after upgrade of plugin version.",'sidebartabs') ?></p>
			<div class="stuffbox metabox-holder" id="layout" style="padding-top:0;">
				<h3><?php _e('Layout of Tabs (Only for configuration and compatibility with previous versions. Select layout in each instance of the plugin.)','sidebartabs'); ?></h3>
				<br class="clear" />
				<table class="form-table">	
					<tr valign="top">
						<th scope="row"><?php _e('Select','sidebartabs') ?></th>
						<td>
							<label><input type="radio" value="1" name="layout" class="optionLayout" <?php if ("1" == $options["layout"]) echo "checked=\"checked\""; ?> /> <?php _e('Fixed without icons','sidebartabs') ?> &nbsp;&nbsp;</label>
							<label><input type="radio" value="2" name="layout" class="optionLayout" <?php if ("2" == $options["layout"]) echo "checked=\"checked\""; ?> /> <?php _e('Fixed with icons','sidebartabs') ?>  &nbsp;&nbsp;</label>
							<label><input type="radio" value="3" name="layout" class="optionLayout" <?php if ("3" == $options["layout"]) echo "checked=\"checked\""; ?> /> <?php _e('Scrollable without icons','sidebartabs') ?> &nbsp;&nbsp;</label>
							<label><input type="radio" value="4" name="layout" class="optionLayout" <?php if ("4" == $options["layout"]) echo "checked=\"checked\""; ?> /> <?php _e('Scrollable with icons','sidebartabs') ?></label>
						</td>
					</tr>	
				</table>
				<br />
			</div>	

           <div class="stuffbox metabox-holder" style="padding-top:0;">
				<h3><?php _e('Check \'Yes\' if you uses jQuery UI in the blog (Avoid conflict with the jQuery Tools library since both have the tabs function).','sidebartabs'); ?></h3>
				<br class="clear" />
				<table class="form-table">	
					<tr valign="top">
						<td>
							<label><input type="radio" value="0" name="jqueryui" class="optionArgs" <?php if (!$options["jqueryui"]) echo "checked=\"checked\""; ?> /> <?php _e('No','sidebartabs') ?> &nbsp;&nbsp;</label>
							<label><input type="radio" value="1" name="jqueryui" class="optionArgs" <?php if ($options["jqueryui"]) echo "checked=\"checked\""; ?> /> <?php _e('Yes','sidebartabs') ?>  &nbsp;&nbsp;</label>
						</td>
					</tr>	
				</table>
				<br />
			</div>	

           <div class="stuffbox metabox-holder" style="padding-top:0;">
				<h3><?php _e('Default arguments before and after widget of your theme','sidebartabs'); ?></h3>
				<br class="clear" />
				<table class="form-table">	
					<tr>
						<td colspan="2"><?php echo _e("<strong>Note</strong>: Some themes, without the use of arguments, break the sidebar layout. Others don't work correctly with them. So I decided to create this option and leave the choice up to each one. An excellent explanation this issue is the comment (#69) made by Clifton in page of plugin. If the plugin works correctly without these arguments in your theme select 'No', otherwise select 'Yes'.",'sidebartabs') ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Use?','sidebartabs') ?></th>
						<td>
							<label><input type="radio" value="0" name="args_theme" class="optionArgs" <?php if (!$options["args_theme"]) echo "checked=\"checked\""; ?> /> <?php _e('No','sidebartabs') ?> &nbsp;&nbsp;</label>
							<label><input type="radio" value="1" name="args_theme" class="optionArgs" <?php if ($options["args_theme"]) echo "checked=\"checked\""; ?> /> <?php _e('Yes','sidebartabs') ?>  &nbsp;&nbsp;</label>
						</td>
					</tr>	
				</table>
				<br />
			</div>	
			
			<div id="resposta">
				<?php if ($options["layout"] == 1 || $options["layout"] == 3 || !isset($options["layout"])) require_once("sidebarTabs_fixed.php"); 
					  else  require_once("sidebarTabs_scrollable.php"); 
				?>
			</div>
		   <div id="extra" style="display:none;"><div id="loading"><img src="<?php echo plugins_url('sidebartabs/images/ajax-loader.gif'); ?>" alt="" style="vertical-align:middle;" /> &nbsp;&nbsp;<?php _e('Loading. Wait...','sidebartabs'); ?></div></div>      
					
		</form>	
		<div class="tablenav">
			<div class="alignleft">
				<a class="button-highlighted action_buttons" href="<?php echo $path ?>?page=sidebarTabs.php">&laquo; <?php _e('Back to Manage sidebarTabs','sidebartabs') ?></a>
				<br class="clear" />
			</div>
		</div>
	 </div>
<?php	
			break;

		case 'reset': {
			sidebarTabs_init(true);
			$stab = NULL;
			update_option('sidebartabs_widget',$stab);
			$total_tabs = 0;
			break;
		}
				
		case 'save':
			$message = true;
			$number = trim($_POST['number']);
		    $sidebarTabs_name = $_POST['widget'];
		    $sidebarTabs_tabname = str_replace('"',"'",$_POST['sidebarName']);
			$before_widget = str_replace('"',"'",$_POST['beforeWidget']);
			$after_widget  = str_replace('"',"'",$_POST['afterWidget']);
			$before_title  = str_replace('"',"'",$_POST['beforeTitle']);
			$after_title   = str_replace('"',"'",$_POST['afterTitle']);
		    $sidebarTabs_description = $wp_registered_widgets[$_POST['widget']]['description'];
		    $sidebarTabs_callback = $wp_registered_widgets[$_POST['widget']]['callback'];		
			$sidebarTabs_unregister = ($_POST['unregister'] == 1) ? 1 : 0;
			$sidebarTabs_icon = $_POST['icon'];
			if (!empty($_POST['update'])) {
				$success = true;
				$success_message = $update_message;
				$stab[$number] = array('widget' => $sidebarTabs_name, 'sidebartabname' => $sidebarTabs_tabname, 'description' => $sidebarTabs_description, 'callback' => $sidebarTabs_callback, 'unregister' => $sidebarTabs_unregister, 'before_widget' => $before_widget, 'after_widget' => $after_widget, 'before_title' => $before_title, 'after_title' => $after_title, 'icon' => $sidebarTabs_icon);
				update_option('sidebartabs_widget', $stab);
				$total_tabs = count($stab);
				write_css_icons();
				break;		   
			}
			if (!is_numeric($number) && empty($number)) {
				$number = $total_tabs+1;
				$success_message .= '
					<li>'.sprintf(__('The tab number was set to %s.','sidebartabs'),$number).'</li>'; 
			
			} 
			
			if (!is_numeric($number)) {
				$number = ($total_tabs+1);				
			}
								
			if ($number != $_POST['otab']) {			
				if ($number < 1) {
					$success_message .= '
						<li>'.__('The tab number should be at least 1 and so was changed to 1.','sidebartabs').'</li>';
					$number = 1;
				} elseif ($number > ($total_tabs+1)) {
					$success_message .= '
						<li>'.__('The tab number was changed to be only one higher than the previous total number of tabs.','sidebartabs').'</li>';
					$number = ($total_tabs+1);
				}
				if ($number < $_GET["otab"]) {
					$temp = $stab;
					foreach ($temp as $i => $v) {
						if (($i >= $number) && ($i < $_GET["otab"])) {
							$stab[$i+1] = $v;
						}
					}
				} elseif ($number > $_GET["otab"]) {
					$temp = $stab;
					foreach ($temp as $i => $v) {
						if (($i <= $number) && ($i > $_GET["otab"])) {
							$stab[$i-1] = $v;
						}
					}
				}
			}
			
			$success = true;
			$stab[$number] = array('widget' => $sidebarTabs_name, 'sidebartabname' => $sidebarTabs_tabname, 'description' => $sidebarTabs_description, 'callback' => $sidebarTabs_callback, 'unregister' => $sidebarTabs_unregister, 'before_widget' => $before_widget, 'after_widget' => $after_widget, 'before_title' => $before_title, 'after_title' => $after_title, 'icon' => $sidebarTabs_icon);
			update_option('sidebartabs_widget', $stab);
			$total_tabs = count($stab);
			write_css_icons();
		break;	
	}
	if ( $message == true ) {
		if  ( $error == true ) {
			$complete_message .= $error_message;
		} 
		if ( $success == true ) {
			$complete_message .= $success_message;
		}
		$complete_message .= '</p></div>';
		
		echo $complete_message;
	}
}	

if (!isset($_GET['action']) || $_GET['action'] != 'edit' && $_GET['action'] != 'options' && $_GET['action'] != 'args') {
	$sbto = get_option('sidebarTabs');	
	if ($sbto['layout'] == 1) $layout_used = __('Fixed without icons','sidebartabs');
	if ($sbto['layout'] == 2) $layout_used = __('Fixed with icons','sidebartabs');
	if ($sbto['layout'] == 3) $layout_used = __('Scrollable without icons','sidebartabs');
	if ($sbto['layout'] == 4) $layout_used = __('Scrollable with icons','sidebartabs');
?>

	<div class="wrap">	
		<div class="icon32 icon-sidebarTabs">
		<br/>
		</div>
		<h2><?php echo _e('Manage sidebarTabs','sidebartabs'); ?></h2>
		<br class="clear" />
		<form id="manage_tabs_form" method="post" action="<?php echo $path ?>?page=sidebarTabs.php&amp;action=delete"> 
			<div class="tablenav">
				<div class="alignleft">
					<input type="submit" value="<?php echo _e('Delete','sidebartabs'); ?>" name="delete" class="button-secondary delete action_buttons" />
					<a class="button-highlighted action_buttons" href="<?php echo $path ?>?page=sidebarTabs.php&amp;action=edit&amp;new=1&amp;tab=<?php echo ($total_tabs+1) ?>"><?php echo _e('Create New sidebarTab','sidebartabs'); ?></a>&brvbar;&nbsp;&nbsp;&nbsp;
					<a class="button-highlighted action_buttons" href="<?php echo $path ?>?page=sidebarTabs.php&amp;action=options"><?php _e('sidebarTabs Options','sidebartabs'); ?></a>
					<a class="button-highlighted action_buttons" href="<?php echo $path ?>?page=sidebarTabs.php&amp;action=args"><?php echo _e('Default args','sidebartabs'); ?></a>&brvbar;&nbsp;&nbsp;&nbsp;
					<a class="button-highlighted action_buttons" href="<?php echo $path ?>?page=sidebarTabs.php&amp;action=reset" onclick="javascript:check=confirm('<?php echo _e('Reset ?','sidebartabs'); ?>');if(check==false) return false;"><?php echo _e('Reset','sidebartabs'); ?></a>
				</div>
				<br class="clear" />
			</div>
			<br class="clear" />
			<table class="widefat"> 
				<thead>
					<tr> 
						<th scope="col" class="check-column"><input type="checkbox" onclick="toggle_check(this)" /></th> 
						<th scope="col"><?php echo _e('Order','sidebartabs'); ?></th>
						<th scope="col"><?php echo _e('Widget','sidebartabs'); ?></th>
						<th scope="col"><?php echo _e('Tab Name','sidebartabs'); ?></th>
						<th scope="col"><?php echo _e('Description','sidebartabs'); ?></th>
						<th scope="col"><?php echo _e('Icon','sidebartabs'); ?></th>
						<th scope="col"><?php echo _e('Unregister','sidebartabs'); ?></th>
						<th scope="col"><?php echo _e('Action','sidebartabs'); ?></th>
					</tr>	
				</thead>	
				<?php
				if (is_array($stab)) { 
					ksort($stab);
					foreach ($stab as $i => $widget ) {
					    if ($widget['unregister'] == 1) $unregister = __('yes','sidebartabs');
						else $unregister = __('no','sidebartabs');
						if ($i%2) { 
							echo '<tr class="alternate">';
						} else {
							echo '<tr>';
						}
						$iname = explode('-',$widget['widget']);
						$options = get_option("widget_".$iname[0]);
						$title = $widget['widget']." (".$options[$iname[1]]['title'].")"; 
						if ($options[$iname[1]]['title'] == '') $title = $widget['widget'];
						echo '
							  <th scope="row" class="check-column"><input type="checkbox" name="del_'.$i.'" /></th>
							  <td>'.$i.'</td>
							  <td>'.$title.'</td>
							  <td>'.stripslashes($widget['sidebartabname']).'</td>
							  <td>'.$widget['description'].'</td>
							  <td style="background: url('.plugins_url('sidebartabs/icons/').$widget['icon'].') 0 -'.$sbto['height_icons'].'px no-repeat;"></td>
							  <td>'.$unregister.'</td>
							  <td><a href="'.$path.'?page=sidebarTabs.php&amp;action=edit&amp;new=0&amp;tab='.$i.'">'.__('Edit','sidebartabs').'</a></td>
						  </tr>';  
					}
				}
				?>
			</table>	
		</form>
		<br class="clear" />
		<h2><?php echo _e('Preview (without style) - Layout: ','sidebartabs').$layout_used; ?></h2>
		<br class="clear" />
		<?php 
			sidebarTabs_addHeader(); 
			ksort($wp_registered_widgets);
			$widget_options = get_option("widget_sidebarTabs");
			$count_widgets = 0;
			foreach ( $wp_registered_widgets as $widget ) { 
				$name = $widget['name']; 
				if ($name == 'sidebarTabs')  {
					$id_sb = $widget['params'][0]['number'];
					$args = wp_parse_args( $args, array('widget_id' => 'sidebartabs-'.$id_sb));
					$abas = $widget_options[$id_sb]['orderSelect'];
					$sidebarTabs_status = is_active_widget( $widget['callback'], $widget['id']) ? '1' : '0';
					if ($sidebarTabs_status) {
						get_sidebarTabs($args,'1');
						$count_widgets++;
					}
					$args='';
				}	
			}	
			if (!$count_widgets) echo '<p>'.__('No instance created. Go to Appearence > Widget after create the sidebarTabs.','sidebartabs').'</p>';							
		?>
	</div>
	<?php } 
	
function get_list_images($dirname) {
	$files = array(); 
	if($handle = opendir($dirname)) { 
	   while(false !== ($file = readdir($handle))) { 
		   if (!preg_match("/\.(gif|png|jpg|jpeg){1}$/i", $file)) continue;
		   if (!@getimagesize($dirname . '/' . $file)) continue;
		   $files[] = utf8_encode($file); 
	   }	   
	   closedir($handle); 
	} 
	sort($files);
	return ($files); 
}	

function write_css_icons() {
	$stab = get_option("sidebartabs_widget");
	$sbto = get_option("sidebarTabs");

//	if ($sbto["layout"] == 1 || $sbto["layout"] == 3) {
//		update_option("sidebartabs_css_icons", '');		
//		return;
//	}
	
	$css_icon = '';
	
	if (is_array($stab)) { 
		ksort($stab);
		foreach ($stab as $i => $widget ) {	
			if ($widget['icon']) {
				$css_icon .= '
ul.sidebarTabs a.sbicon'.$i.' {
	text-indent: -9999px;
	background: url(icons/'.$widget['icon'].') 0 -'.$sbto['height_icons'].'px no-repeat;
	width:' .$sbto['width_icons'].'px;
	padding: 0 !important;
}	
ul.sidebarTabs a.sbicon'.$i.':hover, ul.sidebarTabs a#sbicon'.$i.'.current {
	background: url(icons/'.$widget['icon'].') no-repeat !important;
}
';
			}	
		}
	}	
	update_option("sidebartabs_css_icons", $css_icon);		
}
	?>
	
<script type="text/javascript">
//<![CDATA[
	var valor_ant = <?php if ($options["layout"]) { echo $options["layout"]; } else { echo '1'; } ?>;
	jQuery(document).ready(function() {
		jQuery('#layout:radio').wrap('<a href="#"></a>');
		jQuery('.optionLayout').click(function(e){
		    var valor = jQuery(this).val();
			if (valor == valor_ant) return;
			if ((valor_ant == 1 || valor_ant == 3) && (valor == 1 || valor == 3)) return;
			if ((valor_ant == 2 || valor_ant == 4) && (valor == 2 || valor == 4)) return;
			valor_ant = valor;
			jQuery(this).attr('checked');
			if (valor == 1 || valor == 3) {
				var url = '<?php echo plugins_url("/sidebartabs/sidebarTabs_fixed.php"); ?>';
			}
			else {
				var url = '<?php echo plugins_url("/sidebartabs/sidebarTabs_scrollable.php"); ?>';
			}	
			jQuery.ajax({
				url: url,
				dataType: 'html',
				type: 'GET',
				beforeSend: function() {
					jQuery('#extra').show();
				},
				complete: function() {
					jQuery('#extra').hide();
				},
				success: function(data, textStatus) {
					jQuery('#resposta').empty();
					jQuery('#resposta').html(data);
				},
				error: function(xhr,er) {
					alert('Error '+xhr.status+' - '+xhr.statusText+', Type Error: '+er+' >> url: '+url);
				}
			});	 
		});

	});
   // ]]>

</script>