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
 	$tam_scrollable = '100%';
}
else {
	$tam_scrollable = $opsbt["width_sidebar"]."px";
}
?>
ul.sb_fwithout_icons, ul.sb_fwith_icons {
	right: 0;
}

ul.sb_fwithout_icons a { 
<?php
	if ($opsbt['fs'] != '') { ?>
	font-size: <?php echo $opsbt['fs']; ?>px;
<?php } else { ?>	
	font-size:11px;
<?php } 	
	if ($opsbt['fw'] == 'bold') { ?>
	font-weight: bold;
<?php } 	
	if ($opsbt['ff'] != '') { ?>
	font-family: <?php echo stripslashes($opsbt['ff']) ?>;
<?php  } ?>	
	height: <?php echo $opsbt["height_tabs"]; ?>px  !important;	
	line-height: <?php echo $opsbt["height_tabs"]; ?>px !important;
	border: 1px solid <?php echo $opsbt["line"]; ?>;
}
	
/* selected tab */
ul.sb_fwithout_icons a.current {
	background-color:<?php echo $opsbt["active_bg"]; ?> !important;
	color:<?php echo $opsbt["active_font"]; ?> !important;	 
}

/* 
    root element for the scrollable. 
    when scrolling occurs this element stays still. 
*/ 
div.scrollable_fwithout_icons { 
    height:<?php echo $opsbt["height_tabs"]; ?>px !important; 
	line-height: <?php echo $opsbt["height_tabs"]; ?>px !important;
    width: <?php echo $tam_scrollable; ?>; 
} 

div.sb_container_fwithout_icons a.prev {
<?php if (!$opsbt['align_left']) { ?> 
	width:10px;
<?php } else { ?>
	width: 0;
<?php } ?>		
	height:10px;
	float:left;
	font-size:1px;
}
div.sb_container_fwithout_icons a.prev {
<?php if (!$opsbt['align_left']) { ?> 
	margin-right: 3px;
<?php } else { ?>
    margin_right: 0;
<?php } ?>		
}

/* link inside the tab. uses a background image */
ul.sb_fwith_icons a { 
	height: <?php echo $opsbt["height_icons"]; ?>px  !important;	
	line-height: <?php echo $opsbt["height_icons"]; ?>px !important;
}
	
div.scrollable_fwith_icons { 
    height:<?php echo $opsbt["height_icons"]; ?>px !important; 
	line-height: <?php echo $opsbt["height_icons"]; ?>px !important;
    width: <?php echo $tam_scrollable; ?>; 
} 
 
div.sb_container_fwith_icons a.prev {
	width: 0;
    margin_right: 0;
}
