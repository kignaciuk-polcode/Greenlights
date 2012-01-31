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
}
?>

	#sidebarTabs_admin_preview{
		height:50px;
		border: 2px solid grey;
		background-color: <?php echo $opsbt['active_bg']; ?>;
		margin-top:20px;
		padding:10px;
	}

	#sidebarTabs_admin
		{
		margin: 0 1en 1em 0;
		padding: 0em 20px 0.2em 1em;
		border-bottom: 1px solid <?php echo $opsbt['line']; ?>;
		font-size: 11px;
		list-style-type: none;
		}

	#sidebarTabs_admin li
		{	
		display: inline;
		font-size: 11px;
		line-height:normal;
		}
		
	#sidebarTabs_admin_inactive 
		{
		text-decoration: none;
		background: <?php echo $opsbt['inactive_bg']; ?>;
		border: 1px solid <?php echo $opsbt['line']; ?>;
		padding: 0.2em 0.4em;
		color: <?php echo $opsbt["inactive_font"]; ?> ;
		outline:none;		
		}
		
	#sidebarTabs_admin_active {
		
		background: <?php echo $opsbt["active_bg"]; ?>;
		color: <?php echo $opsbt["active_font"]; ?> ;
		border: 1px solid <?php echo $opsbt["line"]; ?>;
		border-bottom: 1px solid <?php echo $opsbt["active_bg"]; ?>;
		text-decoration: none;
		padding: 0.2em 0.4em;

		}

	#sidebarTabs_admin_over 
		{
		color: <?php echo $opsbt["over_font"]; ?> ;
		background: <?php echo $opsbt["over_bg"]; ?>;
		border: 1px solid <?php echo $opsbt["line"]; ?>;
		text-decoration: none;
		padding: 0.2em 0.4em;

		}
	#sidebarTabsWidgets table { border-collapse: collapse; margin-top: 5px; } 	
	#sidebarTabsWidgets table tr td { border-bottom: 1px solid #ccc; }	
	#sidebarTabsWidgets table tr td { padding: 3px 20px; }
	#sidebarTabsWidgets table tr th { background-color: #e6e7e8; }
	.action_buttons {
		vertical-align: middle !important;
		margin-left: 20px;
	}
	p label {
	    width: 110px;
		vertical-align: middle;
		float: right;
	}	
	
#colorpicker301 { margin: 800px 500px 0 0 !important; }

.sidebarTabs_divs {
	font-size: 11px;
}
.sidebarTabs_divs ul {
	margin: 0;
	padding: 0;
	list-style: none;
}
.sidebarTabs_divs ul li {
	padding-right: 10px;
}
.sidebarTabs_divs ul li a {
	text-decoration: none;
	color:#2583ad;
}
.title_sidebarTabs_admin {
	line-height: 30px !important;
	background-color: #464646;
	margin: 0 0 10px 0 !important;
	padding: 0 15px 0 0 !important;
	color: #fff !important;
	font-size: 1.2em !important;
	text-align: right;
}
.sbtw {
	background-color:<?php echo $opsbt["bg_panes"]; ?> !important;
	border: none !important;
	padding: 0 !important;
}

.sb_icon {
 	vertical-align: middle; 
	border:1px solid #e6e7e8; 
	padding:5px; 
	margin: 0 1px 5px 1px;
}
.sb_preview {
    padding:0 0 15px 0;
	float:left; 
	width: 40%;
} 
.sb_form {
    float:right; 
	width: 58%; 
	padding-bottom: 15px;
} 	
#extra {
    position:absolute;
	width: 100%;
	height: 100%;
	left: 0;
	top: 0;
	background-color: transparent;
	text-align: center;
	z-index:1;
}
#loading {
	width: 240px;
	margin: 0 auto;
	margin-top: 30%;
	background-color:#f1f1f1;
	border: 1px solid #444;
	padding: 20px;
}
div.corner  { float: right; width: 50px; height: 10px; padding: 10px; margin: 0 0 0.7em 0.7em; background: #464646; }
div.bvtp { float: right; width: 100px; height: 30px; margin: 0 0 0.7em 0.7em; background: url(images/vertical_bg.png) repeat-x; }
div.bvtd { float: right; width: 100px; height: 30px; margin: 0 0 0.7em 0.7em; background: <?php echo $opsbt["active_bg"]; ?>  url(images/h30.png) repeat-x; color: <?php echo $opsbt["active_font"]; ?> ;}
div.bvtn { float: right; width: 100px; height: 30px; margin: 0 0 0.7em 0.7em; background-color: <?php echo $opsbt["active_bg"]; ?>; color: <?php echo $opsbt["active_font"]; ?>;}
.icon-sidebarTabs {
	background: transparent url(images/sidebarTabs.png) no-repeat;
}