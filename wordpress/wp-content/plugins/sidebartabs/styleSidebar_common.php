<?php
header("Content-type: text/css");
if (!function_exists('add_action')) {
	$wp_root = '../../..';
	if (file_exists($wp_root.'/wp-load.php')) {
		require_once($wp_root.'/wp-load.php');
	} else {
		require_once($wp_root.'/wp-config.php');
	}
	$opsbt = get_option("sidebarTabs");
	$theme_sb = get_template();
}

if ($opsbt['unit'] == 'p') {
	$unit = '%';
}
else {
	$unit = $opsbt['unit'];
}
?>

div.sb_container {
	width: <?php echo $opsbt["width_sidebar"].$unit; ?>;
<?php if ($opsbt['margin_c'] != '') { 
	$margin_c = str_replace(';','',$opsbt['margin_c']);
	echo $margin_c . ' !important;';
}	
?>	
}	
div.sidebarTabs_panes {
<?php if ($theme_sb == "default") { ?>
	float: left;
<?php } else { ?>
	clear: left;
<?php } ?>		
	margin: 0 0 10px 0;
	padding: 0;
<?php if ($opsbt['line'] != '' && $opsbt['line'] != 'none') { ?>	
	border: 1px solid <?php echo $opsbt["line"]; ?>;
<?php } ?>
	width: 100%;
}
	
/* tab pane */
div.sidebarTabs_panes .tb {
	display:none;
	padding:8px;
	background-color:<?php echo $opsbt["bg_panes"]; ?>;
	<?php if ($opsbt["text_color"] != "") { ?>	
	color: <?php echo $opsbt["text_color"]; ?> !important; 
	<?php } ?>
}
	<?php if ($opsbt["color_links"] != "") { ?>
div.sidebarTabs_panes .tb a {
	color: <?php echo $opsbt["color_links"]; ?> !important;
}
	<?php } ?>
	<?php if ($opsbt["color_hover_links"] != "") { ?>
div.sidebarTabs_panes .tb a:hover {
	color: <?php echo $opsbt["color_hover_links"]; ?> !important;
}
	<?php } ?>
ul.sidebarTabs {  
	margin:0 !important; 
	padding:0;
	font-family: tahoma, arial, verdana, sans-serif;
}

/* single tab */
ul.sidebarTabs li {  
	float:left;	 
	padding:0 !important; 
	margin:0 !important;  
	list-style-type:none !important;
	background: none !important;
}

/* link inside the tab. uses a background image */
ul.sidebarTabs a { 
	float:left;
	display:block;
	padding:0 6px !important;
	text-align: center;	
	text-decoration:none;
	color:<?php echo $opsbt["inactive_font"]; ?> !important;
	margin-right: 2px !important;
<?php if ($opsbt["bht"]) { ?>	
	background: <?php echo $opsbt["inactive_bg"]; ?>;
<?php } else { ?>	
	background: <?php echo $opsbt["inactive_bg"]; ?> url('images/h30.png') repeat-x 0 0;
<?php } ?>	
	position:relative;
	outline: none;
}

ul.sidebarTabs a:hover {
	background-color:<?php echo $opsbt["over_bg"]; ?> !important;
	color:<?php echo $opsbt["over_font"]; ?> !important;
}
	
/* selected tab */
ul.sidebarTabs a.current {
	cursor:default;
}

ul.sidebarTabs li:before {
	content: none !important;	
}  
/* 
    root element for the scrollable. 
    when scrolling occurs this element stays still. 
*/ 
div.scrollable { 
 
    /* required settings */ 
    position:relative; 
    overflow:hidden; 
    float: left;
} 
 
/* 
    root element for scrollable items. Must be absolutely positioned 
    and it should have a super large width to accomodate scrollable items. 
    it's enough that you set width and height for the root element and 
    not for this element. 
*/ 
ul.sidebarTabs  { 
    /* this cannot be too large */ 
    width:20000em; 
    position:absolute; 
} 

.widget_title { display: none; }

.title_sidebarTabs {
	line-height: 30px !important;
	background-color: #464646;
	margin: 0 0 10px 0 !important;
	padding: 0 0 0 10px !important;
	color: #fff !important;
	font-size: 1.2em !important;
}

.sb_accordion {
	background:<?php echo $opsbt["bg_panes"]; ?>;
	width: <?php echo $opsbt["width_sidebar"].$unit; ?>;
<?php if ($opsbt['line'] != '' && $opsbt['line'] != 'none') { ?>	
	border:1px solid <?php echo $opsbt["line"]; ?>;	
<?php } ?>
<?php if ($theme_sb == "default") { ?>
	float: left;
<?php } ?>
<?php if ($opsbt['margin_c'] != '') { 
	$margin_c = str_replace(';','',$opsbt['margin_c']);
	echo $margin_c . ' !important;';
} else { ?>
	margin-bottom: 10px;
<?php
}		
?>	
}

/* accordion header */
.sb_accordion h4.accordion_h4 {
<?php if ($opsbt['bvt'] == 1) { ?>	
	background: <?php echo $opsbt["inactive_bg"]; ?> url('images/h30.png') repeat-x 0 0 !important;
<?php } else if ($opsbt['bvt'] == 0) { ?>	
	background: #ddd url('images/vertical_bg.png') repeat-x left top !important;
<?php } else { ?>	
	background: <?php echo $opsbt["inactive_bg"]; ?> !important;
<?php } ?>
<?php if ($opsbt['bvt'] == 0 || $opsbt['bvt'] == 1) { ?>	
	margin:0 !important;
	padding: 0 0 0 10px !important;
	height: 30px !important;
	line-height: 30px !important;
<?php } else { ?>
	margin:0 !important;
	padding: 9px 0px 9px 10px !important;
<?php } ?>
	clear: both;
<?php	
	if ($opsbt['fs'] != '') { ?>
	font-size: <?php echo $opsbt['fs']; ?>px !important;
<?php } else { ?>	
	font-size:14px !important;
<?php } 	
	if ($opsbt['fw'] == 'bold') { ?>
	font-weight: bold !important;
<?php } else { ?>
	font-weight: normal !important;
<?php }  	
	if ($opsbt['ff'] != '') { ?>
	font-family: <?php echo stripslashes($opsbt['ff']) ?> !important;
<?php }  ?>	
	font-style: normal;
	cursor:pointer;		
<?php if ($opsbt['bvt']) { ?>	
	color:<?php echo $opsbt["inactive_font"]; ?> !important;
<?php } ?>	
}
<?php if ($opsbt['bvt']) { ?>	
.sb_accordion .accordion_h4:hover {
	background-color:<?php echo $opsbt["over_bg"]; ?> !important;
	color:<?php echo $opsbt["over_font"]; ?> !important;
}
<?php } ?>
.sb_accordion .accordion_h4 span { 
	display: block;
	background: transparent url('images/accordion-collapsed.png') no-repeat 95% 50%; 
}
/* currently active header */
.sb_accordion .accordion_h4.current {
	cursor:default;
<?php if ($opsbt['bvt']) { ?>	
	background-color: <?php echo $opsbt["active_bg"]; ?> !important;
	color:<?php echo $opsbt["active_font"]; ?> !important;	 
<?php } ?>	
}
.sb_accordion .accordion_h4.current span {
	background: transparent url('images/accordion-active.png') no-repeat 95% 50%;
}
/* accordion pane */
.sb_accordion div.pane {
	display:none;
	padding:8px;
	<?php if ($opsbt["text_color"] != "") { ?>	
	color: <?php echo $opsbt["text_color"]; ?> !important; 
	<?php } ?>
}
	<?php if ($opsbt["color_links"] != "") { ?>
.sb_accordion div.pane a {
	color: <?php echo $opsbt["color_links"]; ?> !important;
}
	<?php } ?>
	<?php if ($opsbt["color_hover_links"] != "") { ?>
.sb_accordion div.pane a:hover {
	color: <?php echo $opsbt["color_hover_links"]; ?> !important;
}
	<?php } ?>

/* a title inside pane */
.sb_accordion div.pane h3 {
	font-weight:normal;
	margin:0 0 -5px 0;
	font-size:16px;
	color:#999;
}
