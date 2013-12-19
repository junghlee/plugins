<?php 

function help_icon($hash){
	return " <a href=\"http://shailan.com/wordpress/plugins/dropdown-menu/help//#".$hash."\" class=\"help-icon\">(?)</a>";
}

// Define themes
$default_themes = array(
	'None' => '*none*',
	'From URL' => '*url*',
	'Custom CSS' => '*custom*',
	'Color Scheme' => 'color-scheme',
	'Web 2.0' => plugins_url('/themes/web20.css', __FILE__),
	'Simple White' => plugins_url('/themes/simple.css', __FILE__),
	'Wordpress Default' => plugins_url('/themes/wpdefault.css', __FILE__),
	'Grayscale' => plugins_url('/themes/grayscale.css', __FILE__),
	'Aqua' => plugins_url('/themes/aqua.css', __FILE__),
	'Blue gradient' => plugins_url('/themes/simple-blue.css', __FILE__),
	'Shiny Black' => plugins_url('/themes/shiny-black.css', __FILE__),
	'Flickr theme' =>  plugins_url('/themes/flickr.com/default.ultimate.css', __FILE__),
	'Nvidia theme' =>  plugins_url('/themes/nvidia.com/default.advanced.css', __FILE__),
	'Adobe theme' => plugins_url('/themes/adobe.com/default.advanced.css', __FILE__),
	'MTV theme' =>  plugins_url('/themes/mtv.com/default.ultimate.css', __FILE__),
	'Hulu theme' =>  plugins_url('/themes/hulu/hulu.css', __FILE__),
	'Rounded Corners by Shailan' => plugins_url('/themes/rounded-corners.css', __FILE__),
	'Rounded Corners Light by Shailan' => plugins_url('/themes/rounded-corners-light.css', __FILE__),
	'Pills by Shailan' => plugins_url('/themes/pills.css', __FILE__)
);

$available_themes = array();

// Check for theme style file
if( file_exists( trailingslashit( get_stylesheet_directory() ) . 'dropdown.css') ){
	$available_themes['Dropdown.css (theme)'] = get_stylesheet_directory_uri() . '/dropdown.css';
}

if( file_exists( trailingslashit( get_template_directory() ) . 'dropdown.css') ){
	$available_themes['Dropdown.css (template)'] = get_template_directory_uri() . '/dropdown.css';
}

$available_themes = array_merge( $available_themes, $default_themes );

// Swap array for options page
$themes = array();
while(list($Key,$Val) = each($available_themes))
	$themes[$Val] = $Key;

$overlays = array(
	'none'=>'none',
	'glassy'=>'glassy',
	'flat'=>'flat',
	'shadow'=>'shadow',
	'soft' =>'soft'
);

$alignment = array( 'left'=>'left', 'center' => 'center', 'right'=> 'right' );
$types = array( 'pages'=>'Pages', 'categories'=>'Categories' );
$effects = array('fade'=>'Fade In/Out', 'slide'=>'Slide Up/Down');
$speed = array( '400'=>'Normal', 'fast'=>'Fast', 'slow'=>'Slow' );
$delay = array('100'=>'100', '200'=>'200', '300'=>'300', '400'=>'400', '500'=>'500', '600'=>'600','700'=>'700');

if( function_exists('wp_nav_menu') ){
	// Get available menus
	$menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
	$navmenus = array();
	
	if($menus){
		foreach( $menus as $menu ){
			$navmenus[ 'navmenu_' . $menu->term_id ] = $menu->name;
		}
	}
	
	// Merge type with menu array
	$types = array_merge($types, $navmenus);
}

$this->menu_types = $types; // Back it up

// Registered menu locations
global $_wp_registered_nav_menus;

// Define plugin options	
$options = array(
	
array(
	"name" => "General",
	"label" => __("General"),
	"type" => "section"
),

	array(  "name" => "Dropdown Menu Theme",
	"desc" => "Skin for the menu".help_icon("menu-theme"),
	"id" => "shailan_dm_active_theme",
	"std" => plugins_url("/dropdown-menu-widget/themes/web20.css"),
	"options" => $themes,
	"type" => "select"),
	
	array(  "name" => "Theme URL",
	"desc" => "If <strong>From URL</strong> is selected you can specify theme URL here. ".help_icon("theme-url"),
	"id" => "shailan_dm_theme_url",
	"std" => "http://",
	"type" => "text"),
	
	array(  "name" => "Use Theme Location",
	"desc" => "This option enables use of theme location.". help_icon("theme-location"),
	"id" => "shailan_dm_location_enabled",
	"type" => "checkbox",
	"std" => "off" ),
	
	array(  "name" => "Theme Location",
	"desc" => "This option will place dropdown menu automatically to the theme location.". help_icon("theme-location"),
	"id" => "shailan_dm_location",
	"type" => "select",
	"options" => $_wp_registered_nav_menus ),
	
	array(  "name" => "Rename Homepage",
	"desc" => "You can change your homepage link text here " . help_icon("rename-homepage"),
	"id" => "shailan_dm_home_tag",
	"std" => __("Home"),
	"type" => "text"),
	
	array(  "name" => "Show parent indicators",
	"desc" => "This option will enable arrows next to parent items.". help_icon("parent-indicators"),
	"id" => "shailan_dm_arrows",
	"type" => "checkbox",
	"std" => "on" ),
	
	array(  "name" => "Indicator color",
	"desc" => "Change indicator color (eg. #000000).". help_icon("indicator-color"),
	"id" => "shailan_dm_arrow_color",
	"std" => "rgba(0,0,0,0.5)",
	"type" => "text"),
	
array( "type" => "close" ),

array(
		"name" => "Template Tag",
		"label" => __("Template Tag"),
		"type" => "section"
	),
	
	array(
		"desc" => "Settings here only effect menus inserted with <strong>template tag</strong> : <code>&lt;?php shailan_dropdown_menu(); ?&gt;</code>. Widget settings are NOT affected by these settings. ".help_icon("template-tag"),
		"type" => "paragraph"
	),
	
	array(  "name" => "Menu Type",
	"desc" => "Dropdown Menu Type".help_icon("menu-type"),
	"id" => "shailan_dm_type",
	"std" => "pages",
	"options" => $types,
	"type" => "select"),
	
	array(  "name" => "Home link",
	"desc" => "If checked dropdown menu displays home link".help_icon("home-link"),
	"id" => "shailan_dm_home",
	"std" => 'on',
	"type" => "checkbox"),
	
	array(  "name" => "Login",
	"desc" => "If checked dropdown menu displays login link".help_icon("login"),
	"id" => "shailan_dm_login",
	"std" => 'on',
	"type" => "checkbox"),
	
	array(  "name" => "Register / Site Admin",
	"desc" => "If checked dropdown menu displays register/site admin link.".help_icon("register-site-admin"),
	"id" => "shailan_dm_login",
	"std" => 'on',
	"type" => "checkbox"),
	
	array(  "name" => "Vertical menu",
	"desc" => "If checked dropdown menu is displayed vertical.".help_icon("vertical-menu"),
	"id" => "shailan_dm_vertical",
	"std" => 'off',
	"type" => "checkbox"),
	
	array(  "name" => "Exclude Pages",
	"desc" => "Excluded page IDs.".help_icon("exclude-pages"),
	"id" => "shailan_dm_exclude",
	"std" => "",
	"type" => "text"),
	
	array(  "name" => "Alignment",
	"desc" => "Menu alignment.".help_icon("alignment"),
	"id" => "shailan_dm_align",
	"std" => "left",
	"options" => $alignment,
	"type" => "select"),
	
	array( "type" => "close" ),
	
array(
	"name" => "Effects",
	"label" => __("Effects"),
	"type" => "section"
),
	
	array(  "name" => "Enable dropdown effects",
	"desc" => "If checked sub menus will use effects below". help_icon("enable-dropdown-effects"),
	"std" => "on",
	"id" => "shailan_dm_effects",
	"type" => "checkbox"),
	
	array(  "name" => "Effect",
	"desc" => "Select effect you want to use".help_icon("effect"),
	"id" => "shailan_dm_effect",
	"type" => "select",
	"options" => $effects ),
	
	array(  "name" => "Effect Speed",
	"desc" => "Select effect speed".help_icon("effect-speed"),
	"id" => "shailan_dm_effect_speed",
	"type" => "select",
	"std" => "fast",
	"options" => $speed ),
	
	array(  "name" => "Effect delay",
	"desc" => "Select effect delay (uses hoverIntent)".help_icon("effect-delay"),
	"id" => "shailan_dm_effect_delay",
	"type" => "select",
	"options" => $delay ),
	
array( "type" => "close" ),

array(
	"name" => "custom-colors",
	"label" => __("Custom colors"),
	"type" => "section"
),

	array(
		"desc" => "Using options below you can customize certain elements of current theme. If you select <strong>Color Scheme</strong> as your theme, you will have full control over colors.",
		"type" => "paragraph"
	),
	
	array(  "name" => "Use custom colors",
	"desc" => "If not checked custom colors won't work.".help_icon("use-custom-colors"),
	"id" => "shailan_dm_custom_colors",
	"std" => 'off',
	"type" => "checkbox"),
	
	array("type"=>"picker"),
	
	array(  "name" => "Menu Background Color",
	"desc" => "Background color of the dropdown menu".help_icon("menu-background-color"),
	"id" => "shailan_dm_color_menubg",
	"std" => '#000000',
	"type" => "text"),
	
	array(  "name" => "Hover Background Color",
	"desc" => "Background color of list item link.".help_icon("hover-background-color"),
	"id" => "shailan_dm_color_lihover",
	"std" => '#333333',
	"type" => "text"),
	
	array(  "name" => "Link Text Color",
	"desc" => "Default link color".help_icon("link-text-color"),
	"id" => "shailan_dm_color_link",
	"std" => '#FFFFFF',
	"type" => "text"),
	
	array(  "name" => "Link Text Color on mouse over",
	"desc" => "Secondary link color".help_icon("link-text-color-on-mouse-over"),
	"id" => "shailan_dm_color_hoverlink",
	"std" => '#FFFFFF',
	"type" => "text"),
	
	array(  "name" => "Overlay",
	"desc" => "Menu overlay (Works on browsers that support png transparency only.)".help_icon("overlay"),
	"id" => "shailan_dm_overlay",
	"std" => "glass",
	"type" => "select",
	"options" => $overlays ),
	
	array( "type" => "close" ),
	
	array(
		"name" => "Advanced",
		"label" => __("Advanced"),
		"type" => "section"
	),
	
	array(  "name" => "Dropdown Menu Font",
	"desc" => "Font family for the menu<br />Please leave blank to use your wordpress theme font.".help_icon("dropdown-menu-font"),
	"id" => "shailan_dm_font",
	"std" => '',
	"type" => "text"),
	
	array(  "name" => "Dropdown Menu Font Size",
	"desc" => "Font size of the menu items (Eg: 12px OR 1em) <br />Please leave blank to use your wordpress theme font-size.".help_icon("dropdown-menu-font-size"),
	"id" => "shailan_dm_fontsize",
	"std" => '',
	"type" => "text"),
	
	array(  
		"name" => "Custom CSS",
		"desc" => "You can paste your own customization file here.".help_icon("custom-css"),
		"id" => "shailan_dm_custom_css",
		"std" => '',
		"type" => "textarea"
	),
	
	array(  "name" => "Show Empty Categories",
	"desc" => "If checked categories with no posts will be shown.".help_icon("show-empty-categories"),
	"id" => "shailan_dm_show_empty",
	"std" => 'on',
	"type" => "checkbox"),
	
		array(  
		"name" => "Wrap long menu items",
		"desc" => "If checked long menu items will wrap". help_icon("wrap-long-menu-items"),
		"id" => "shailan_dm_allowmultiline",
		"type" => "checkbox",
		"std" => "off"
	),
	
	array(  
		"name" => "Remove title attributes",
		"desc" => "This will remove 'View all posts under..' title attributes from menu links". help_icon("remove-title-attributes"),
		"id" => "shailan_dm_remove_title_attributes",
		"type" => "checkbox",
		"std" => "off"
	),
	
	array(  
		"name" => "Remove links from top levels",
		"desc" => "This will remove links from top level pages/categories. So user can only click to sub-level menu.". help_icon("remove-links-from-top-levels"),
		"id" => "shailan_dm_remove_top_level_links",
		"type" => "checkbox",
		"std" => "off"
	),
	
	array( "type" => "close" )
	
); 
