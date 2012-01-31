<?php
if (!function_exists('add_action')) {
	$wp_root = '../../..';
	if (file_exists($wp_root.'/wp-load.php')) {
		require_once($wp_root.'/wp-load.php');
	} else {
		require_once($wp_root.'/wp-config.php');
	}
}
$options=get_option("sidebarTabs");
if (!$options["margin_c"] && !isset($options["margin_c"])) $margin_c = 'margin: 10px 0 10px 0';
else $margin_c = $options["margin_c"];
?>
			<div id="with_icons">	
				<div class="stuffbox metabox-holder" style="padding-top:0;">
					<h3><?php echo _e('Colors','sidebartabs') ?></h3>
					<div id="colorpicker301" class="colorpicker301" onclick="setTimeout('sidebarTabs_preview()',100)"></div>				
					<table class="form-table">
						<tr>
							<td colspan="2"><?php echo _e('Enter the color in the fields bellow, or use the buttons to pick it.','sidebartabs') ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php echo _e('Line color of the panes','sidebartabs'); ?></th>
							<td>
								<input type="button" onclick="showColorGrid3('line','none');" value="..." />&nbsp;
								<input type="text" id="line" name="line" size="10" value="<?php echo $options["line"] ?>" onkeyup="sidebarTabs_preview()" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php echo _e('Background color of the panes','sidebartabs'); ?></th>
							<td scope="row">
								<input type="button" onclick="showColorGrid3('bg_panes','none');" value="..." />&nbsp;
								<input type="text" id="bg_panes" name="bg_panes" size="10" value="<?php echo $options["bg_panes"] ?>" />
							</td>
						</tr>	
						<tr valign="top">
							<th scope="row"><?php echo _e('Link color in the panes','sidebartabs'); ?></th>
							<td scope="row">
								<input type="button" onclick="showColorGrid3('color_links','none');" value="..." />&nbsp;
								<input type="text" id="color_links" name="color_links" size="10" value="<?php echo $options["color_links"] ?>" /> <small>(<?php echo _e('blank assumes the theme color','sidebartabs'); ?>)</small>
							</td>
						</tr>	
						<tr valign="top">
							<th scope="row"><?php echo _e('Hover link color in the panes','sidebartabs'); ?></th>
							<td scope="row">
								<input type="button" onclick="showColorGrid3('color_hover_links','none');" value="..." />&nbsp;
								<input type="text" id="color_hover_links" name="color_hover_links" size="10" value="<?php echo $options["color_hover_links"] ?>" /> <small>(<?php echo _e('blank assumes the theme color','sidebartabs'); ?>)</small>
							</td>
						</tr>	
						<tr valign="top">
							<th scope="row"><?php echo _e('Text color in the panes','sidebartabs'); ?></th>
							<td scope="row">
								<input type="button" onclick="showColorGrid3('text_color','none');" value="..." />&nbsp;
								<input type="text" id="text_color" name="text_color" size="10" value="<?php echo $options["text_color"] ?>" /> <small>(<?php echo _e('blank assumes the theme color','sidebartabs'); ?>)</small>
							</td>
						</tr>	
					</table>
					<br />
				</div>	
				<div class="stuffbox metabox-holder" style="padding-top:0;">
					<h3><?php _e('Dimensions / CSS Margin Instance / Others','sidebartabs'); ?></h3>
					<br class="clear" />
					<table class="form-table">	
						<tr>
							<td colspan="2">
								<?php _e('<strong>Note:</strong> Set the height (32) and width (39) if you want to use the icons provided with the plugin','sidebartabs'); ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Height Icons','sidebartabs'); ?></th>
							<td scope="row">
								<input type="text" id="height_icons" name="height_icons" size="6" value="<?php echo $options["height_icons"] ?>" /> <small>px</small>
							</td>
						</tr>	
						<tr valign="top">
							<th scope="row"><?php echo _e('Width Icons','sidebartabs'); ?></th>
							<td scope="row">
								<input type="text" id="width_icons" name="width_icons" size="6" value="<?php echo $options["width_icons"] ?>" /> <small>px</small>
							</td>
						</tr>	
						<tr valign="top">
							<th scope="row"><?php echo _e('Width sidebar of your theme','sidebartabs'); ?></th>
							<td scope="row">
								<input type="text" name="width_sidebar" size="6" value="<?php echo $options["width_sidebar"] ?>" />
								<input type="radio" value="px" name="unit" <?php if ("px" == $options["unit"]) echo "checked=\"checked\""; ?> /> px <small><?php _e('(fixed sidebar)','sidebartabs'); ?></small>&nbsp;&nbsp;
								<input type="radio" value="p" name="unit" <?php if ("p" == $options["unit"]) echo "checked=\"checked\""; ?> /> % <small><?php _e('(fluid sidebar)','sidebartabs'); ?></small>
							</td>
						</tr>	
						<tr valign="top">
							<th scope="row"><?php _e('CSS Margin Instance','sidebartabs'); ?></th>
							<td scope="row">
								<input type="text" id="margin_c" name="margin_c" size="40" value="<?php echo $margin_c ?>" /><br /> <small>(<?php echo _e('blank assumes the default margin of plugin','sidebartabs'); ?>)</small>		
							</td>
						</tr>	
						<tr valign="top">
							<th scope="row"><?php _e('Align horizontal tabs on the left?','sidebartabs') ?></th>
							<td>
								<label><input type="radio" value="0" name="align_left" class="optionAlign" <?php if (!$options["align_left"]) echo "checked=\"checked\""; ?> /> <?php _e('No','sidebartabs') ?> &nbsp;&nbsp;</label>
								<label><input type="radio" value="1" name="align_left" class="optionAlign" <?php if ($options["align_left"]) echo "checked=\"checked\""; ?> /> <?php _e('Yes','sidebartabs') ?>  &nbsp;&nbsp;</label>
							</td>
						</tr>	
						<tr valign="top">
							<th scope="row"><?php _e('Remember last opened tab','sidebartabs'); ?></th>
							<td>	
								<input type="checkbox" name="cookies" value="1" <?php if ("1" == $options["cookies"]) echo "checked"; ?>> <small>Makes the browser remember in wich tab the user was when the page is reloaded. Requires cookies to be enable.</small>
							</td>
						</tr>		
						<tr valign="top">
							<th scope="row"><?php _e('Cookies expires','sidebartabs'); ?></th>
							<td scope="row">
								<input type="text" id="expires" name="expires" size="10" value="<?php echo $options['expires'] ?>" /><br /><small>(<?php echo _e('Time in days. Examples: 20 = 20 days = 480 hours, 0.5 = 0.5 days = 12 hours','sidebartabs'); ?>)</small>	
							</td>
						</tr>	
					</table>
					<br />
					<input type="hidden" name="active_font" value="<?php echo $options["active_font"] ?>" />
					<input type="hidden" name="active_bg" value="<?php echo $options["active_bg"] ?>" />
					<input type="hidden" name="over_font" value="<?php echo $options["over_font"] ?>" />
					<input type="hidden" name="over_bg" value="<?php echo $options["over_bg"] ?>" />
					<input type="hidden" name="inactive_font" value="<?php echo $options["inactive_font"] ?>" />
					<input type="hidden" name="inactive_bg" value="<?php echo $options["inactive_bg"] ?>" />					
					<input type="hidden" name="height_tabs" value="<?php echo $options["height_tabs"] ?>" />					
					<input type="hidden" name="width_corner" value="<?php echo $options["width_corner"] ?>" />					
					<input type="hidden" name="typeCorner" value="<?php echo $options["typeCorner"] ?>" />					
					<input type="hidden" name="fw" value="<?php echo $options["fw"] ?>" />					
					<input type="hidden" name="fs" value="<?php echo $options["fs"] ?>" />					
					<input type="hidden" name="ff" value="<?php echo stripslashes($options["ff"]) ?>" />					
					<input type="hidden" name="bvt" value="<?php echo $options["bvt"] ?>" />					
					<input type="hidden" name="bht" value="<?php echo $options["bht"] ?>" />					
				</div>	
				<div class="submit" style="clear:both;">
					<input type="submit" name="submit_sidebarTab" value="<?php echo _e('Update Settings', 'sidebartabs') ?> &raquo;" />
					<input type="hidden" name="action" value="options" />
				</div>
			</div>	