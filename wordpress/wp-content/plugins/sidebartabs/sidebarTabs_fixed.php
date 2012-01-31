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
			<div id="without_icons">	
				<div id="wrap_right" class="stuffbox metabox-holder sb_preview" style="padding-top:0;">
					<h3><?php _e('Preview','sidebartabs'); ?></h3>
					<div id="sidebarTabs_admin_preview">	
						<ul id='sidebarTabs_admin'>
							<li ><a id='sidebarTabs_admin_active' href='#'><?php _e('Active Tab','sidebartabs'); ?></a></li>
							<li ><a id='sidebarTabs_admin_inactive' href='#'><?php _e('Inactive Tab','sidebartabs'); ?></a></li>
							<li ><a id='sidebarTabs_admin_over' href='#'><?php _e('Mouse Over','sidebartabs'); ?></a></li>		
						</ul>
					</div>			
				</div>
				<div id="wrap_left" class="sb_form">
			<div class="stuffbox metabox-holder" style="padding-top:0;">
				<h3><?php _e('Plugin jQuery Corner','sidebartabs'); ?></h3>
				<br class="clear" />
				<table class="form-table">	
					<tr valign="top">
						<th scope="row"><?php _e('Width Corner','sidebartabs'); ?></th>
						<td scope="row">
							<input type="text" id="width_corner" name="width_corner" size="3" value="<?php echo $options["width_corner"] ?>" /> <small>px <?php _e('(recommended value: 5)','sidebartabs'); ?></small>
						</td>
					</tr>	
					<tr valign="top">
						<th scope="row"><?php _e('Select the corner of tabs','sidebartabs') ?></th>
						<td>
						    <div class="corner">
								<input type="radio" name="typeCorner" value="0" <?php if ('0' == $options["typeCorner"]) echo "checked=\"checked\""; ?>  />
							</div>
						    <div class="corner">
								<input type="radio" name="typeCorner" value="top" <?php if ('top' == $options["typeCorner"]) echo "checked=\"checked\""; ?>  /> <p>jQuery(this).corner("top 5px");</p>
							</div>
							<div class="corner">	
								<input type="radio" name="typeCorner" value="dog tl" <?php if ('dog tl' == $options["typeCorner"]) echo "checked=\"checked\""; ?>  /> <p>jQuery(this).corner("dog tl 5px");</p>
							</div>
							<div class="corner">	
								<input type="radio" name="typeCorner" value="bevel top" <?php if ('bevel top' == $options["typeCorner"]) echo "checked=\"checked\""; ?>  /> <p>jQuery(this).corner("bevel top 5px");</p>
							</div>
							<div class="corner">	
								<input type="radio" name="typeCorner" value="notch top" <?php if ('notch top' == $options["typeCorner"]) echo "checked=\"checked\""; ?>  /> <p>jQuery(this).corner("notch top 5px");</p>
							</div>
							<div class="corner">	
								<input type="radio" name="typeCorner" value="bite top" <?php if ('bite top' == $options["typeCorner"]) echo "checked=\"checked\""; ?>  /> <p>jQuery(this).corner("bite top 5px");</p>
							</div>
							<div class="corner">	
								<input type="radio" name="typeCorner" value="cool top" <?php if ('cool top' == $options["typeCorner"]) echo "checked=\"checked\""; ?>  /> <p>jQuery(this).corner("cool top 5px");</p>
							</div>
							<div class="corner">	
								<input type="radio" name="typeCorner" value="sharp top" <?php if ('sharp top' == $options["typeCorner"]) echo "checked=\"checked\""; ?>  /> <p>jQuery(this).corner("sharp top 5px");</p>
							</div>
							<div class="corner">	
								<input type="radio" name="typeCorner" value="slide top" <?php if ('slide top' == $options["typeCorner"]) echo "checked=\"checked\""; ?>  /> <p>jQuery(this).corner("slide top 5px");</p>
							</div>
							<div class="corner">	
								<input type="radio" name="typeCorner" value="jut top" <?php if ('jut top' == $options["typeCorner"]) echo "checked=\"checked\""; ?>  /> <p>jQuery(this).corner("jut top 5px");</p>
							</div>
							<div class="corner">	
								<input type="radio" name="typeCorner" value="curl top" <?php if ('curl top' == $options["typeCorner"]) echo "checked=\"checked\""; ?>  /> <p>jQuery(this).corner("curl top 5px");</p>
							</div>
							<div class="corner">	
								<input type="radio" name="typeCorner" value="tear top" <?php if ('tear top' == $options["typeCorner"]) echo "checked=\"checked\""; ?>  /> <p>jQuery(this).corner("tear top 5px");</p>
							</div>
							<div class="corner">	
								<input type="radio" name="typeCorner" value="fray top" <?php if ('fray top' == $options["typeCorner"]) echo "checked=\"checked\""; ?>  /> <p>jQuery(this).corner("fray tl 5px");</p>
							</div>
							<div class="corner">	
								<input type="radio" name="typeCorner" value="wicked tl" <?php if ('wicked tl' == $options["typeCorner"]) echo "checked=\"checked\""; ?>  /> <p>jQuery(this).corner("wicked tl 5px");</p>
							</div>
							<div class="corner">	
								<input type="radio" name="typeCorner" value="long top" <?php if ('long top' == $options["typeCorner"]) echo "checked=\"checked\""; ?>  /> <p>jQuery(this).corner("long top 5px");</p>
							</div>
							<div class="corner">	
								<input type="radio" name="typeCorner" value="sculpt top" <?php if ('sculpt top' == $options["typeCorner"]) echo "checked=\"checked\""; ?>  /> <p>jQuery(this).corner("sculpt top 5px");</p>
							</div>
							<div class="corner">	
								<input type="radio" name="typeCorner" value="dog2 tl" <?php if ('dog2 tl' == $options["typeCorner"]) echo "checked=\"checked\""; ?>  /> <p>jQuery(this).corner("dog2 tl 5px");</p>
							</div>
							<div class="corner">	
								<input type="radio" name="typeCorner" value="dog3 tl" <?php if ('dog3 tl' == $options["typeCorner"]) echo "checked=\"checked\""; ?>  /> <p>jQuery(this).corner("dog3 tl 5px");</p>
							</div>
						</td>
					</tr>	
				</table>
			</div>		
					<div class="stuffbox metabox-holder" style="padding-top:0;">
						<h3><?php echo _e('Colors','sidebartabs') ?></h3>
						<div id="colorpicker301" class="colorpicker301" onclick="setTimeout('sidebarTabs_preview()',100)"></div>				
						<table class="form-table">
							<tr>
								<td colspan="2"><?php echo _e('Enter the color in the fields bellow, or use the buttons to pick it.','sidebartabs') ?>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php echo _e('Line Color','sidebartabs'); ?></th>
								<td>
									<input type="button" onclick="showColorGrid3('line','none');" value="..." />&nbsp;
									<input type="text" id="line" name="line" size="10" value="<?php echo $options["line"] ?>" onkeyup="sidebarTabs_preview()" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php echo _e('Active Tab','sidebartabs'); ?></th>
								<td>
									<p><label><?php echo _e('Text color:','sidebartabs'); ?></label><input type="button" onclick="showColorGrid3('active_font','none');" value="..." />&nbsp;
									<input type="text" id="active_font" name="active_font" size="10" value="<?php echo $options["active_font"] ?>" onkeyup="sidebarTabs_preview()" /></p>
									<p><label><?php echo _e('Background color:','sidebartabs'); ?></label><input type="button" size="10" onclick="showColorGrid3('active_bg','none');" value="..." />&nbsp;
									<input type="text" id="active_bg" name="active_bg" size="10" value="<?php echo $options["active_bg"] ?>" onkeyup="sidebarTabs_preview()" /></p>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php echo _e('Mouse Over Tab','sidebartabs'); ?></th>
								<td>
									<p><label><?php echo _e('Text color:','sidebartabs'); ?></label><input type="button" onclick="showColorGrid3('over_font','none');" value="..." />&nbsp;
									<input type="text" id="over_font" name="over_font" size="10" value="<?php echo $options["over_font"] ?>" onkeyup="sidebarTabs_preview()" /></p>
									<p><label><?php echo _e('Background color:','sidebartabs'); ?></label><input type="button" onclick="showColorGrid3('over_bg','none');" value="..." />&nbsp;
									<input type="text" id="over_bg" name="over_bg" size="10" value="<?php echo $options["over_bg"] ?>" onkeyup="sidebarTabs_preview()" /></p>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php _e('Inactive Tab','sidebartabs'); ?></th>
								<td>
									<p><label><?php _e('Text color:','sidebartabs'); ?></label><input type="button" onclick="showColorGrid3('inactive_font','none');" value="..." />&nbsp;
									<input type="text" id="inactive_font" name="inactive_font" size="10" value="<?php echo $options["inactive_font"] ?>" onkeyup="sidebarTabs_preview()" /></p>
									<p><label><?php _e('Background color:','sidebartabs'); ?></label><input type="button" onclick="showColorGrid3('inactive_bg','none');" value="..." />&nbsp;
									<input type="text" id="inactive_bg" name="inactive_bg" size="10" value="<?php echo $options["inactive_bg"] ?>" onkeyup="sidebarTabs_preview()" /></p>

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
							<tr valign="top">
								<th scope="row"><?php _e('Background of vertical tabs ','sidebartabs'); ?></th>
								<td scope="row">
									<div class="bvtp">
										<input type="radio" value="0" name="bvt" class="align" <?php if ("0" == $options["bvt"]) echo "checked=\"checked\""; ?> /> <?php _e('standard','sidebartabs'); ?>&nbsp;&nbsp;
									</div>
									<div class="bvtd">
										<input type="radio" value="1" name="bvt" class="align" <?php if ("1" == $options["bvt"]) echo "checked=\"checked\""; ?> /> <?php _e('gradient','sidebartabs'); ?>
									</div>
									<div class="bvtn">
										<input type="radio" value="2" name="bvt" class="align" <?php if ("2" == $options["bvt"]) echo "checked=\"checked\""; ?> /> <?php _e('normal','sidebartabs'); ?>
									</div>
								</td>
							</tr>	
							<tr valign="top">
								<th scope="row"><?php _e('Background of horizontal tabs ','sidebartabs'); ?></th>
								<td scope="row">
									<div class="bvtd">
										<input type="radio" value="0" name="bht" class="align" <?php if ("0" == $options["bht"]) echo "checked=\"checked\""; ?> /> <?php _e('gradient','sidebartabs'); ?>
									</div>
									<div class="bvtn">
										<input type="radio" value="1" name="bht" class="align" <?php if ("1" == $options["bht"]) echo "checked=\"checked\""; ?> /> <?php _e('normal','sidebartabs'); ?>
									</div>
								</td>
							</tr>	
						</table>
						<br />
					</div>	
					<div class="stuffbox metabox-holder" style="padding-top:0;">
						<h3><?php _e('Dimensions / Font of Tabs / CSS Margin Instance / Others','sidebartabs'); ?></h3>
						<br class="clear" />
						<table class="form-table">	
							<tr valign="top">
								<th scope="row"><?php _e('Font Weight of Tabs','sidebartabs'); ?></th>
								<td scope="row">
									<input type="radio" value="bold" name="fw" class="align" <?php if ("bold" == $options["fw"]) echo "checked=\"checked\""; ?> /> <?php _e('bold','sidebartabs') ?> &nbsp;&nbsp;
									<input type="radio" value="normal" name="fw" class="align" <?php if ("normal" == $options["fw"]) echo "checked=\"checked\""; ?> /> <?php _e('normal','sidebartabs') ?> 
								</td>
							</tr>	
							<tr valign="top">
								<th scope="row"><?php _e('Font Size of Tabs','sidebartabs'); ?></th>
								<td scope="row">
									<input type="text" id="fs" name="fs" size="4" value="<?php echo $options["fs"] ?>" /> <small>px</small>
								</td>
							</tr>	
							<tr valign="top">
								<th scope="row"><?php _e('Font Family of Tabs','sidebartabs'); ?></th>
								<td scope="row">
									<input type="text" id="ff" name="ff" size="50" value="<?php echo stripslashes($options["ff"]) ?>" /><br /><small><?php echo _e('Example: verdana, arial, sans-serif. Blank assumes the theme.','sidebartabs'); ?></small>
								</td>
							</tr>	
							<tr valign="top">
								<th scope="row"><?php _e('Height Tabs','sidebartabs'); ?></th>
								<td scope="row">
									<input type="text" id="height_tabs" name="height_tabs" size="6" value="<?php echo $options["height_tabs"] ?>" /> <small>px <?php _e('(recommended value: 26)','sidebartabs'); ?></small>
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
					<input type="hidden" name="height_icons" value="<?php echo $options["height_icons"] ?>" />					
					<input type="hidden" name="width_icons" value="<?php echo $options["width_icons"] ?>" />					
					</div>	
				</div>	
				<div class="submit" style="clear:both;">
					<input type="submit" name="submit_sidebarTab" value="<?php echo _e('Update Settings', 'sidebartabs') ?> &raquo;" />
					<input type="hidden" name="action" value="options" />
				</div>
			</div>	

<script type="text/javascript">
//<![CDATA[ 
	jQuery(document).ready(function() {
        jQuery('div.corner').each(function() {
            var t = jQuery('p', this).text();
            eval(t);
			jQuery('p', this).text('')
        });
	});
   // ]]>

</script>			