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
	$tam_scrollable = '78%';
}
else {
	$tam_scrollable = ($opsbt["width_sidebar"] - 42)."px";
}
?>

ul.sb_swithout_icons a { 
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
<?php } ?>	
	height: <?php echo $opsbt["height_tabs"]; ?>px  !important;	
	line-height: <?php echo $opsbt["height_tabs"]; ?>px !important;
	border: 1px solid <?php echo $opsbt["line"]; ?>;
}

ul.sb_swithout_icons a.current {
	background-color:<?php echo $opsbt["active_bg"]; ?> !important;
	color:<?php echo $opsbt["active_font"]; ?> !important;	 
}

 
div.scrollable_swithout_icons { 
    height:<?php echo $opsbt["height_tabs"]; ?>px !important; 
	line-height: <?php echo $opsbt["height_tabs"]; ?>px !important;
    width: <?php echo $tam_scrollable; ?>; 
} 
 
/* 
    a single item. must be floated on horizontal scrolling 
    typically this element is the one that *you* will style 
    the most. 
*/ 
div.sb_container_swithout_icons a.prev, div.sb_container_swithout_icons a.next {
	margin-top:<?php echo ((int) ($opsbt["height_tabs"]-18)/2); ?>px;	
}
/* prev, next */
div.sb_container_swithout_icons a.prev, div.sb_container_swithout_icons a.next {
	width:18px;
	height:18px;
	float:left;
	cursor:pointer;
	font-size:1px;
}
div.sb_container_swithout_icons a.prev {
	margin-right: 3px;
}
div.sb_container_swithout_icons a.next {
	margin-left: 3px;
}	
div.sb_container_swithout_icons a.prev {
	background:url(images/left.png) 0 -18px no-repeat;
}
div.sb_container_swithout_icons a.next {
	float: right;
	background:url(images/right.png) 0 -18px no-repeat !important;
	clear:right;	
} 
div.sb_container_swithout_icons a.prev:hover {
	background-position:0 -18px;		
}
div.sb_container_swithout_icons a.next:hover {
	background-position: 0 -18px;		
}
/* disabled navigational button */
div.sb_container_swithout_icons a.prev.disabled {
	background:url(images/left.png) no-repeat;
	cursor: default;
}
div.sb_container_swithout_icons a.next.disabled {
	background:url(images/right.png) 0 0 no-repeat !important;
	cursor: default;
}

ul.sb_swith_icons a { 
	height: <?php echo $opsbt["height_icons"]; ?>px  !important;	
	line-height: <?php echo $opsbt["height_icons"]; ?>px !important;
}
 
div.scrollable_swith_icons { 
    width: <?php echo $tam_scrollable; ?>; 
    height:<?php echo $opsbt["height_icons"]; ?>px !important; 
	line-height: <?php echo $opsbt["height_icons"]; ?>px !important;
}
 
/* 
    a single item. must be floated on horizontal scrolling 
    typically this element is the one that *you* will style 
    the most. 
*/ 
div.sb_container_swith_icons a.prev, div.sb_container_swith_icons a.next {
	margin-top:<?php echo ((int) ($opsbt["height_tabs"]-18)/2); ?>px;	
}
/* prev, next */
div.sb_container_swith_icons a.prev, div.sb_container_swith_icons a.next {
	width:18px;
	height:18px;
	float:left;
	cursor:pointer;
	font-size:1px;
}
div.sb_container_swith_icons a.prev {
	margin-right: 3px;
}
div.sb_container_swith_icons a.next {
	margin-left: 3px;
}	
div.sb_container_swith_icons a.prev {
	background:url(images/left.png) 0 -18px no-repeat;
}
div.sb_container_swith_icons a.next {
	float: right;
	background:url(images/right.png) 0 -18px no-repeat !important;
	clear:right;	
} 
div.sb_container_swith_icons a.prev:hover {
	background-position:0 -18px;		
}
div.sb_container_swith_icons a.next:hover {
	background-position: 0 -18px;		
}
/* disabled navigational button */
div.sb_container_swith_icons a.prev.disabled {
	background:url(images/left.png) no-repeat;
	cursor: default;
}
div.sb_container_swith_icons a.next.disabled {
	background:url(images/right.png) 0 0 no-repeat !important;
	cursor: default;
}
