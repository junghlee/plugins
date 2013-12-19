<?php 
/*
Plugin Name: Dropdown Menu Widget
Plugin URI: http://shailan.com/wordpress/plugins/dropdown-menu
Description: A multi widget to generate drop-down menus from your pages, categories & navigation menus. You can find more widgets, plugins and themes at <a href="http://shailan.com">shailan.com</a>.
Tags: dropdown, menu, css, css-dropdown, navigation, widget, dropdown-menu, customization, theme, jquery, template, multi-color, theme
Version: 1.9.2
Author: Matt Say
Author URI: http://shailan.com
Text Domain: shailan-dropdown-menu
*/

define('VERSION', '1.9.2');

class shailan_DropdownWidget extends WP_Widget {

	function shailan_DropdownWidget(){
		$widget_ops = array(
			'classname' => 'shailan-dropdown-menu-widget', 
			'description' => __( 'Dropdown page/category/custom menu', 'shailan-dropdown-menu' ) 
		);
		
		$this->WP_Widget('dropdown-menu', __('Dropdown Menu', 'shailan-dropdown-menu'), $widget_ops);
		$this->alt_option_name = 'widget_dropdown_menu';
		
		$this->pluginname = "Dropdown Menu";
		$this->shortname = "shailan_dm";
		
		$this->help_url = "http://shailan.com/wordpress/plugins/dropdown-menu/help/";
		
		$this->version = VERSION; 
		$this->settings_key = "shailan_dropdown_menu";
		$this->options_page = "dropdown-menu";
		
		// Hook up styles
		add_action( 'wp_head', array(&$this, 'header') );
		// add_action( 'wp_footer', array(&$this, 'footer'), 10, 1 );			
		
		// Hook up scripts 
		/*if(!is_admin()){ */
			wp_enqueue_script( 'jquery' ); 
			wp_enqueue_script( 'hoverIntent', plugins_url( '/scripts/hoverIntent.js' , __FILE__ ) , array('jquery') );
			wp_enqueue_script( 'dropdown-ie-support', plugins_url( '/scripts/include.js' , __FILE__ ) , array('jquery') ); 
		/*}*/
		
		// Include options array
		require_once("shailan-dropdown-menu-options.php");
		$this->options = $options;
		$this->settings = $this->get_plugin_settings();
		
		$this->widget_defaults = array(
			'title' => '',
			'type' => 'pages',
			'exclude' => '',
			'home' => false,
			'login' => false,
			'admin' => false,
			'vertical' => false,
			'align' => 'left',
			'theme' => 'none',
			'show_title' => false,
			'width' => 'empty'
		);
		
		add_action('admin_menu', array( &$this, 'admin_header') );
	}
	
function admin_header(){
	
	if ( @$_GET['page'] == $this->options_page ) {
	
		// Options page styles
		wp_enqueue_style( 'farbtastic' ); 
		wp_enqueue_style( "google-droid-sans", "http://fonts.googleapis.com/css?family=Droid+Sans:regular,bold&v1", false, "1.0", "all");
		wp_enqueue_style( "dropdown-admin", plugins_url( '/css/dropdown-admin.css' , __FILE__ ) , false, "1.0", "all");	
		
		// Options page scripts
		wp_enqueue_script( "jquery" );
		wp_enqueue_script( 'farbtastic' ); 
		wp_enqueue_script( 'dropdown-colorpick', plugins_url( '/scripts/admin.js' , __FILE__ ) , array('jquery') );
		
		if ( @$_REQUEST['action'] && 'save' == $_REQUEST['action'] ) {
		
			// Save settings
			$settings = $this->get_settings();
			
			// Set updated values
			foreach($this->options as $option){					
				if( $option['type'] == 'checkbox' && empty( $_REQUEST[ $option['id'] ] ) ) {
					$settings[ $option['id'] ] = 'off';
				} else {
					$settings[ $option['id'] ] = $_REQUEST[ $option['id'] ]; 
				}
			}
			
			// Save the settings
			update_option( $this->settings_key, $settings );
			header("Location: admin.php?page=" . $this->options_page . "&saved=true&message=1");
			die;
		} else if( @$_REQUEST['action'] && 'reset' == $_REQUEST['action'] ) {
			
			// Start a new settings array
			$settings = array();
			delete_option( $this->settings_key );
			
			header("Location: admin.php?page=" . $this->options_page . "&reset=true&message=2");
			die;
		}
		
	}
 
	$page = add_options_page( 
		__('Settings for Dropdown Menu', 'shailan-dropdown-menu'),
		__('Dropdown Menu', 'shailan-dropdown-menu'), 
		'edit_themes',
		$this->options_page,
		array( &$this, 'options_page') 
	);
	
	add_action( 'admin_print_styles-' . $page, array( &$this, 'header' ) );
}
	
function get_plugin_settings(){
	$settings = get_option( $this->settings_key );		
	
	if(FALSE === $settings){ 
		// Options doesn't exist, install standard settings
		return $this->install_default_settings();
	} else { // Options exist, update if necessary
		if( !empty( $settings['version'] ) ){ $ver = $settings['version']; } 
		else { $ver = ''; }
		
		if($ver != $this->version){ 
			// Update settings
			return $this->update_plugin_settings( $settings ); 
		} else { 
			// Plugin is up to date, let's return
			return $settings;
		} 
	}		
}
	
/* Updates a single option key */
function update_plugin_setting( $key, $value ){
	$settings = $this->get_plugin_settings();
	$settings[$key] = $value;
	update_option( $this->settings_key, $settings );
}

/* Retrieves a single option */
function get_plugin_setting( $key, $default = '' ) {
	$settings = $this->get_plugin_settings();
	if( array_key_exists($key, $settings) ){
		return $settings[$key];
	} else {
		return $default;
	}
	
	return FALSE;
}

function install_default_settings(){
	// Create settings array
	$settings = array();
	
	// Set default values
	foreach($this->options as $option){
		if( array_key_exists( 'id', $option ) && array_key_exists( 'std', $option ) )
			$settings[ $option['id'] ] = $option['std'];
	}
	
	$settings['version'] = $this->version;
	// Save the settings
	update_option( $this->settings_key, $settings );
	return $settings;
}

function update_plugin_settings( $current_settings ){
	//Add missing keys
	foreach($this->options as $option){
		if( array_key_exists ( 'id' , $option ) && !array_key_exists ( $option['id'] ,$current_settings ) ){
			$current_settings[ $option['id'] ] = $option['std'];
		}
	}
	
	update_option( $this->settings_key, $current_settings );
	return $current_settings;
}
	
function options_page(){
	global $options, $current;

	$title = "Dropdown Menu Widget Options";
	
	$options = $this->options;	
	$current = $this->get_plugin_settings();
	
	$messages = array( 
		"1" => __("Dropdown Menu Widget settings saved.", "shailan-dropdown-menu"),
		"2" => __("Dropdown Menu Widget settings reset.", "shailan-dropdown-menu")
	);
	
	$navigation = '<div id="stf_nav"><a href="http://shailan.com/wordpress/plugins/dropdown-menu/">Plugin page</a> | <a href="http://shailan.com/wordpress/plugins/dropdown-menu/help/">Usage</a> | <a href="http://shailan.com/donate/">Donate</a> | <a href="http://shailan.com/wordpress/plugins/">Get more widgets..</a></div>
	
<div class="stf_share">
	<div class="share-label">
		Like this plugin? 
	</div>
	<div class="share-button tweet">
		<a href="http://twitter.com/share" class="twitter-share-button" data-url="http://shailan.com/wordpress/plugins/dropdown-menu/" data-text="I am using #dropdownmenu #widget by shailan on my #wordpress blog, Check this out!" data-count="horizontal" data-via="shailancom">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
	</div>
	<div class="share-button facebook">
		<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
		<fb:like href="http://shailan.com/wordpress/plugins/dropdown-menu/" ref="plugin_options" show_faces="false" width="300" font="segoe ui"></fb:like>
	</div>
</div>
	
	';
	
	$footer_text = '<em><a href="http://shailan.com/wordpress/plugins/dropdown-menu/">Dropdown Menu Widget</a> by <a href="http://shailan.com/">SHAILAN</a></em>';
	
	include_once( "stf-page-options.php" );

}
	
/******************************************************************************
*  the WIDGET 
******************************************************************************/
    function widget($args, $instance) {		
        extract( $args );
		$widget_options = wp_parse_args( $instance, $this->widget_defaults );
		extract( $widget_options, EXTR_SKIP );
		
		// On and off
		$show_title = (bool) ( 'on' == $show_title );		
		$orientation = ( (bool) ( 'on' == $vertical) ? 'dropdown-vertical' : 'dropdown-horizontal');
		$custom_walkers = false; // (bool) get_option('shailan_dm_customwalkers'); disabled
		$show_empty = (bool) ( 'on' == $this->get_plugin_setting('shailan_dm_show_empty') );
		
		$width = (int) $width;
		
		$width_attr = '';
		if( $width > 0 )
			$width_attr = 'style="width:' . $width . 'px;"';
			
		if( !isset($align) || $align == '') {
			$align = 'left';
		}
		
		if( !isset($type) || $type == '') {
			$type = 'pages';
		}
		
        echo $args['before_widget']; 
		
		// Show title if option checked
		if ( $title && $show_title ){ echo $before_title . $title . $after_title; }
		
		$nl = "\n"; $indent = "\n\t"; $indent2 = "\n\t\t";
		
		echo $nl . "<!-- Dropdown Menu Widget by Shailan.com (http://shailan.com)  v". VERSION ." on wp".get_bloginfo( 'version' )." -->";
		echo $nl . "<!-- Menu Type : " . $type . " -->";
		echo $nl . "<div class=\"shailan-dropdown-menu\" >";
			
			$dropdown_wrapper_open = $nl . '<div id="shailan-dropdown-wrapper-' . $this->number . '" >';
					
			$dropdown_open = $indent . '<div align="'.$align.'" class="'.$orientation.'-container dm-align-'.$align.' clearfix"><table cellpadding="0" cellspacing="0"><tr><td>';
			$list_open = $indent2 . '<ul id="dropdown-'. $this->number .'" class="dropdown dropdown-'. $this->number .' '. $orientation . ' dropdown-align-'.$align.'"  '. $width_attr .' >' . $nl . "<!-- Menu elements start -->\n";
			
			if($home && ($type == 'pages' || $type == 'categories')){ 
			
						$home_item = $nl . '<li class="page_item cat-item blogtab '. (is_front_page() && !is_paged() ? 'current_page_item current-cat' : '' ) . '">
							<a href="'.get_option('home').'">';

						$home_tag = get_option('shailan_dm_home_tag'); 
						if(empty($home_tag)){ $home_tag = __('Home'); }
						
						$home_item .= $home_tag;
						$home_item .= '</a></li>';
						
						$list_open .= $home_item;
			}
					
			$list_close = ($admin ? wp_register('<li class="admintab">','</li>', false) : '') . ($login ? '<li class="page_item">'. wp_loginout('', false) . '</li>' : '')  . '
					</ul>';
			$dropdown_close = '</td>
				  </tr></table> 
				</div>';
					
			$dropdown_wrapper_close = '</div> ';
								
			$menu_defaults = array(
				'ID' => $this->number,
				'sort_column' => 'menu_order, post_title',
				'order_by' => 'name',
				'depth' => '4',
				'title_li' => '',
				'exclude' => $exclude
			);
			
			$menu_defaults = apply_filters( 'dropdown_menu_defaults', $menu_defaults );
			
			switch ( $type ) {

				/** Pages menu */
				case "pages": 
				
				if($custom_walkers){
					$page_walker = new shailan_PageWalker();
					$menu_defaults = wp_parse_args( array('walker'=>$page_walker) , $menu_defaults ); }
					
					echo $dropdown_wrapper_open;
					do_action('dropdown_before');
					echo $dropdown_open;
					echo $list_open;
					  do_action('dropdown_list_before');
					  wp_list_pages($menu_defaults);
					  do_action('dropdown_list_after');
					echo $list_close;
					echo $dropdown_close;
					do_action('dropdown_after');
					echo $dropdown_wrapper_close;
				
				break; 
				
				/** Categories menu */
				case "categories": 
				
				if($custom_walkers){ 
					$cat_walker = new shailan_CategoryWalker();
					$menu_defaults = wp_parse_args( array('walker'=>$cat_walker) , $menu_defaults ); }
					
					if($show_empty){$menu_defaults = wp_parse_args( array('hide_empty'=>'0') , $menu_defaults ); }
				
					echo $dropdown_wrapper_open;
					do_action('dropdown_before');
					echo $dropdown_open;
					echo $list_open;
					  do_action('dropdown_list_before');
					  wp_list_categories($menu_defaults); 
					  do_action('dropdown_list_after');
					echo $list_close;
					echo $dropdown_close;
					do_action('dropdown_after');
					echo $dropdown_wrapper_close;

				break;
				
				/** WP3 Nav menu */
				default:
					
					$location = '';
					$menu = '';
				
					// Replace navmenu_
					if( FALSE !== strpos( $type, 'navmenu_' ) ){
						$type = str_replace( 'navmenu_', '', $type );
					}
					
					$menu_id = $type;
					
					// Check if a menu exists with this id
					$menu = wp_get_nav_menu_object( $menu_id );
					if( $menu ){ $menu = $menu_id; }
					
					// Is that a location?
					if ( ! $menu && ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_id ] ) ){
						$location = $menu_id;
						$menu = '';
					}
						
					$menu_args = array(
					  'menu'            => $menu, 
					  'container'       => false, 
					  'container_class' => '', 
					  'container_id'    => '', 
					  'menu_class'      => 'dropdown '. $orientation . ' dropdown-align-'.$align, 
					  'menu_id'         => '',
					  'echo'            => true,
					  'theme_location'  => $location,
					  'is_shailan_dropdown_callback' => 'yes'
					  );
					  
					if($custom_walkers){
						$page_walker = new shailan_PageWalker();
						$menu_args = wp_parse_args( array('walker'=>$page_walker) , $menu_args ); 
					}
					
					echo $dropdown_wrapper_open;
					do_action('dropdown_before');
					echo $dropdown_open;
					  wp_nav_menu($menu_args);
					echo $dropdown_close;
					do_action('dropdown_after');
					echo $dropdown_wrapper_close;
					
				} // switch ($type)

			echo $nl . "</div>";
			echo "\n\n<!--/ Dropdown Menu Widget -->";		?>
			
              <?php echo $after_widget; $this->footer(); ?>
        <?php
    }
	
    function update($new_instance, $old_instance) {	
        return $new_instance;
    }
	
/******************************************************************************
*  WIDGET FORM
******************************************************************************/
    function form($instance) {	
		$widget_options = wp_parse_args( $instance, $this->widget_defaults );
		extract( $widget_options, EXTR_SKIP );
		
		$width = (int) $width;
		$show_title = (bool) $show_title;
		$home = (bool) $home;
		$login = (bool) $login;
		$admin = (bool) $admin;
		$vertical = (bool) $vertical;
		
        ?>		
		
		<style type="text/css">.help-link{ padding:2px 5px; text-decoration:none; }</style>
		
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title :', 'shailan-dropdown-menu'); ?> <?php $this->help_link('widget-title'); ?><input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label> </p>
		
		<p>
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('show_title'); ?>" name="<?php echo $this->get_field_name('show_title'); ?>"<?php checked( $show_title ); ?> />
		<label for="<?php echo $this->get_field_id('show_title'); ?>"><?php _e( 'Show widget title' , 'shailan-dropdown-menu' ); ?></label><?php $this->help_link('show-title'); ?> <br />
			
		<p><label for="<?php echo $this->get_field_id('type'); ?>"><?php _e('Menu Type:'); ?>
		<select name="<?php echo $this->get_field_name('type'); ?>" id="<?php echo $this->get_field_id('type'); ?>">
		<?php foreach ($this->menu_types as $key=>$option) { ?>
				<option <?php if ($type == $key) { echo 'selected="selected"'; } ?> value="<?php echo $key; ?>"><?php echo $option; ?></option><?php } ?>
		</select>
		</label><?php $this->help_link('widget-menu-type'); ?> </p>
			
		<p><label for="<?php echo $this->get_field_id('exclude'); ?>"><?php _e('Exclude:', 'shailan-dropdown-menu'); ?> <?php $this->help_link('exclude'); ?> <input class="widefat" id="<?php echo $this->get_field_id('exclude'); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" type="text" value="<?php echo $exclude; ?>" /></label><br /> 
		<small>Page IDs, separated by commas.</small></p>
		
		<p><label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Menu Width:', 'shailan-dropdown-menu'); ?> <?php $this->help_link('widget-width'); ?><input class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $width; ?>" /></label><br /> 
		<small>Menu width, leave blank for default.</small></p>
			
		<p>
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('home'); ?>" name="<?php echo $this->get_field_name('home'); ?>"<?php checked( $home ); ?> />
		<label for="<?php echo $this->get_field_id('home'); ?>"><?php _e( 'Add homepage link' , 'shailan-dropdown-menu' ); ?></label><?php $this->help_link('homepage-link'); ?><br />
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('login'); ?>" name="<?php echo $this->get_field_name('login'); ?>"<?php checked( $login ); ?> />
		<label for="<?php echo $this->get_field_id('login'); ?>"><?php _e( 'Add login/logout' , 'shailan-dropdown-menu' ); ?></label><?php $this->help_link('login-logout-link'); ?><br />
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('admin'); ?>" name="<?php echo $this->get_field_name('admin'); ?>"<?php checked( $admin ); ?> />
		<label for="<?php echo $this->get_field_id('admin'); ?>"><?php _e( 'Add Register/Site Admin' , 'shailan-dropdown-menu' ); ?></label><?php $this->help_link('site-admin-link'); ?><br />
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('vertical'); ?>" name="<?php echo $this->get_field_name('vertical'); ?>"<?php checked( $vertical ); ?> />
		<label for="<?php echo $this->get_field_id('vertical'); ?>"><?php _e( 'Vertical menu' , 'shailan-dropdown-menu' ); ?><?php $this->help_link('vertical-menu'); ?></label>
		</p>
		
		<p><?php _e('Align:', 'shailan-dropdown-menu'); ?> <?php $this->help_link('alignment'); ?><label for="left"><input type="radio" id="left" name="<?php echo $this->get_field_name('align'); ?>" value="left" <?php if($align=='left'){ echo 'checked="checked"'; } ?> /> <?php _e('Left', 'shailan-dropdown-menu'); ?></label> <label for="center"><input type="radio" id="center" name="<?php echo $this->get_field_name('align'); ?>" value="center" <?php if($align=='center'){ echo 'checked="checked"'; } ?>/> <?php _e('Center', 'shailan-dropdown-menu'); ?></label> <label for="right"><input type="radio" id="right" name="<?php echo $this->get_field_name('align'); ?>" value="right" <?php if($align=='right'){ echo 'checked="checked"'; } ?>/> <?php _e('Right', 'shailan-dropdown-menu'); ?></label></p>
		
		<p><?php _e('Current Theme:', 'shailan-dropdown-menu') ?><br />
			<?php $theme = $this->get_plugin_setting('shailan_dm_active_theme');
				
				$available_themes = array(
					'None' => '*none*',
					'From URL' => '*url*',
					'Custom CSS' => '*custom*',
					'Color Scheme' => 'color-scheme',
					'Web 2.0 by Shailan' => plugins_url('/themes/web20.css', __FILE__),
					'Simple White' => plugins_url('/themes/simple.css', __FILE__),
					'Wordpress Default' => plugins_url('/themes/wpdefault.css', __FILE__),
					'Grayscale' => plugins_url('/themes/grayscale.css', __FILE__),
					'Aqua' => plugins_url('/themes/aqua.css', __FILE__),
					'Blue gradient' => plugins_url('/themes/simple-blue.css', __FILE__),
					'Shiny Black' => plugins_url('/themes/shiny-black.css', __FILE__),
					'Flickr theme' =>  plugins_url('/themes/flickr.com/default.ultimate.css', __FILE__),
					'Nvidia theme' =>  plugins_url('/themes/nvidia.com/default.advanced.css', __FILE__),
					'Adobe theme' => plugins_url('/themes/adobe.com/default.advanced.css', __FILE__),
					'MTV theme' => plugins_url('/themes/mtv.com/default.ultimate.css', __FILE__),
					'Hulu theme' => plugins_url('/themes/hulu/hulu.css', __FILE__),
					'Rounded Corners by Shailan' => plugins_url('/themes/rounded-corners.css', __FILE__),
					'Rounded Corners Light by Shailan' => plugins_url('/themes/rounded-corners-light.css', __FILE__),
					'Pills by Shailan' => plugins_url('/themes/pills.css', __FILE__)
				);
				
				$theme_names = array_flip($available_themes);				
				$theme_name = $theme_names[$theme];				
				echo "<strong>" . $theme_name . "</strong>";
			?>
			<a href="options-general.php?page=dropdown-menu" style="text-decoration:none;"><?php esc_attr_e('Change theme &rarr;', 'shailan-dropdown-menu'); ?></a>
		</p>
			
		<div class="widget-control-actions">
			<p><small>Powered by <a href="http://shailan.com/wordpress/plugins/dropdown-menu/" title="Wordpress Tips and tricks, Freelancing, Web Design">Shailan.com</a> | <a href="http://shailan.com/wordpress/" title="Get more wordpress widgets and themes" target="_blank" >Get more..</a></small></p>
		</div>
		<br class="clear" />
			
        <?php 
	}
	
/******************************************************************************
*  HEADER
******************************************************************************/
	function header($instance){
	
		/*if(!is_admin()){*/
		
			//$default_headers = array( 'Version' => 'Version' );
			//$dropdown = get_file_data( __FILE__, $default_headers, 'plugin' );
		
			$theme = $this->get_plugin_setting('shailan_dm_active_theme');
			if($theme == '*url*'){ $theme = $this->get_plugin_setting('shailan_dm_theme_url'); }
			if($theme == ''){ $theme = "web20"; }
			
			$allow_multiline = (bool) ( 'on' == $this->get_plugin_setting('shailan_dm_allowmultiline') );
			// Colors
			$custom_colors = (bool) ( 'on' == $this->get_plugin_setting('shailan_dm_custom_colors') );
			$shailan_dm_color_menubg = $this->get_plugin_setting('shailan_dm_color_menubg');
			$shailan_dm_color_lihover = $this->get_plugin_setting('shailan_dm_color_lihover');
			$shailan_dm_color_link = $this->get_plugin_setting('shailan_dm_color_link');
			$shailan_dm_color_hoverlink = $this->get_plugin_setting('shailan_dm_color_hoverlink');
			$is_fx_active = (bool) ( 'on' == $this->get_plugin_setting('shailan_dm_effects') );
			
			echo "\n\n<!-- Dropdown Menu Widget Styles by shailan (http://shailan.com) v" . VERSION . " on wp" . get_bloginfo( 'version' ) . " -->"; // For debug
			echo "\n<link rel=\"stylesheet\" href=\"". plugins_url( '/css/shailan-dropdown.min.css' , __FILE__ ) . "\" type=\"text/css\" />";
			
			if( $theme!='*none*' && $theme != '*custom*' ){
				if( false === strpos($theme, 'http') ){
					// Default
					echo "\n<link rel=\"stylesheet\" href=\"". plugins_url( '/themes/' . $theme . '.css', __FILE__ ) ."\" type=\"text/css\" />";
				} else {
					// URL include
					echo "\n<link rel=\"stylesheet\" href=\"".$theme."\" type=\"text/css\" />";
				}
			}
			
			echo "\n<style type=\"text/css\" media=\"all\">";
			
			$indent = "\n\t";
			
			// Font family and font size
			$font_family = stripslashes( $this->get_plugin_setting('shailan_dm_font') );
			
			if(!empty($font_family)){ echo $indent. "ul.dropdown li a { font-family:$font_family; } "; }
			
			$font_size = $this->get_plugin_setting('shailan_dm_fontsize'); //'12px';			
			
			if(!empty($font_size)){ echo $indent. "ul.dropdown li a { font-size:$font_size; }"; }
			
			if(!$allow_multiline){
				echo $indent. "ul.dropdown { white-space: nowrap; }";
			}
			
			if( $custom_colors ){
			// Custom color scheme is active
			
			// Overlay support 
			$overlay = $this->get_plugin_setting('shailan_dm_overlay');
			echo $indent . "/* Selected overlay: ". $overlay . " */";
			
			if($overlay!='none' && $theme=='color-scheme' ){
				$posvert = 0;
				switch ( $overlay ) {
					case "glass": 
						$posvert = 0;
					break; 
					case "flat": 
						$posvert = -100;
					break; 
					case "shadow": 
						$posvert = -200;
					break; 
					case "soft": 
						$posvert = -300;
					break; 
				}
				
				$apos = $posvert - 2;
			
			?>
			
	.shailan-dropdown-menu .dropdown-horizontal-container, 
	ul.dropdown li, ul.dropdown li.hover, ul.dropdown li:hover{ background-position:0px <?php echo $posvert; ?>px; }
	ul.dropdown li.hover a, ul.dropdown li:hover a{ background-position:0px <?php echo $apos; ?>px; }

	<?php } elseif($overlay == 'none') { ?>
	/* Clear background images */
	.shailan-dropdown-menu .dropdown-horizontal-container, ul.dropdown li, ul.dropdown li.hover, ul.dropdown li:hover, ul.dropdown li.hover a, ul.dropdown li:hover a { background-image:none; }		
	<?php } else {/* unidentified overlay ? */} ?>
	
	.shailan-dropdown-menu .dropdown-horizontal-container, ul.dropdown li{ background-color:<?php echo $shailan_dm_color_menubg; ?>; }
	ul.dropdown a,
	ul.dropdown a:link,
	ul.dropdown a:visited,
	ul.dropdown li { color: <?php echo $shailan_dm_color_link; ?>; }
	ul.dropdown a:hover,
	ul.dropdown li:hover { color: <?php echo $shailan_dm_color_hoverlink; ?>; }
	ul.dropdown a:active	{ color: <?php echo $shailan_dm_color_hoverlink; ?>; }
			
	ul.dropdown li.hover a, ul.dropdown li:hover a{ background-color: <?php echo $shailan_dm_color_lihover; ?>; }
	ul.dropdown li.hover ul li, ul.dropdown li:hover ul li{ background-color: <?php echo $shailan_dm_color_menubg; ?>;
		color: <?php echo $shailan_dm_color_link; ?>; }
			
	ul.dropdown li.hover ul li.hover, ul.dropdown li:hover ul li:hover { background-image: none; }
	ul.dropdown li.hover a:hover, ul.dropdown li:hover a:hover { background-color: <?php echo $shailan_dm_color_lihover; ?>; }
	
	ul.dropdown ul{ background-image:none; background-color:<?php echo $shailan_dm_color_menubg; ?>; border:1px solid <?php echo $shailan_dm_color_menubg; ?>; }
	ul.dropdown-vertical li { border-bottom:1px solid <?php echo $shailan_dm_color_lihover; ?>; }
	<?php
			
			} // if($custom_colors)
			
			// If effects not active, embed CSS display:
			if(! $is_fx_active){ ?>
	
	/** Show submenus */
	ul.dropdown li:hover > ul, ul.dropdown li.hover ul{ display: block; }
	
	/** Show current submenu */
	ul.dropdown li.hover ul, ul.dropdown ul li.hover ul, ul.dropdown ul ul li.hover ul, ul.dropdown ul ul ul li.hover ul, ul.dropdown ul ul ul ul li.hover ul , ul.dropdown li:hover ul, ul.dropdown ul li:hover ul, ul.dropdown ul ul li:hover ul, ul.dropdown ul ul ul li:hover ul, ul.dropdown ul ul ul ul li:hover ul { display: block; } 
				
			<?php }
			
// Arrow pointers

if( 'on' == $this->get_plugin_setting('shailan_dm_arrows') ){
	$clr = $this->get_plugin_setting('shailan_dm_arrow_color');

?>

ul.dropdown li.parent>a{
	padding-right:25px;
}
ul.dropdown li.parent>a:after{ 
	content:""; position:absolute; top: 45%; right:6px;width:0;height:0; 
	border-top:4px solid <?php echo $clr; ?>;border-right:4px solid transparent;border-left:4px solid transparent }
ul.dropdown li.parent:hover>a:after{
	content:"";position:absolute; top: 45%; right:6px; width:0; height:0;
	border-top:4px solid <?php echo $clr; ?>;border-right:4px solid transparent;border-left:4px solid transparent }
ul.dropdown li li.parent>a:after{
	content:"";position:absolute;top: 40%; right:5px;width:0;height:0;
	border-left:4px solid <?php echo $clr; ?>;border-top:4px solid transparent;border-bottom:4px solid transparent }
ul.dropdown li li.parent:hover>a:after{
	content:"";position:absolute;top: 40%; right:5px;width:0;height:0;
	border-left:4px solid <?php echo $clr; ?>;border-top:4px solid transparent;border-bottom:4px solid transparent }

<?php } 
			
			// Insert Custom CSS last
			$custom_css = stripslashes( $this->get_plugin_setting('shailan_dm_custom_css') );
			if(!empty($custom_css)){ echo $custom_css; }
			echo "\n</style>";
			echo "\n<!-- /Dropdown Menu Widget Styles -->";
			echo "\n\n ";
		
		/*}*/
	} // -- End Header
	
/******************************************************************************
*  HEADER
******************************************************************************/
	function footer( $instance = null ){
		$indent = "\n\t";
		
		$remove_title_attributes = (bool) ( 'on' == $this->get_plugin_setting('shailan_dm_remove_title_attributes') );
		$remove_top_level_links = (bool) ( 'on' == $this->get_plugin_setting('shailan_dm_remove_top_level_links') );
		$is_fx_active = (bool) ( 'on' == $this->get_plugin_setting('shailan_dm_effects') );
		$speed = $this->get_plugin_setting('shailan_dm_effect_speed', '400');
		$effect = $this->get_plugin_setting('shailan_dm_effect', 'fade');
		$delay = $this->get_plugin_setting('shailan_dm_effect_delay', '100');
		
		if( $is_fx_active || $remove_title_attributes || $remove_top_level_links ){
		
		echo "\n\n<!-- Dropdown Menu Widget Effects by shailan (http://shailan.com) v". VERSION ." on wp".get_bloginfo( 'version' )." -->"; // For debug
		echo "\n<script type=\"text/javascript\">/* <![CDATA[ */";
		echo "\njQuery(document).ready(function($) { \n";
		
// Remove title attributes from links

		if($remove_title_attributes){
		?>
  $('ul.dropdown li a').removeAttr('title');
		<?php
		}
		
// Remove links from top-level elements

		if($remove_top_level_links){
		?>
  $('ul.dropdown>li>ul.children').parent().find('a:first').removeAttr('href');
		<?php
		}
		
// Dropdown FX

		if( 'fade' == $effect ){
		?>
 
  var config = {
	over : function(){ $(this).find("ul:first").fadeIn('<?php echo $speed; ?>'); },  
	out : function(){ $(this).find("ul:first").fadeOut('<?php echo $speed; ?>'); },
	timeout : <?php echo $delay; ?>
  }
 
  $(".dropdown li").hoverIntent( config );
		<?php
		} elseif( 'slide' == $effect ) { ?>

  var config = {
	over : function(){	$(this).find("ul:first").slideDown('<?php echo $speed; ?>'); },  
	out : function(){	$(this).find("ul:first").slideUp('<?php echo $speed; ?>'); },
	timeout : <?php echo $delay; ?>
  }
 
  $(".dropdown li").hoverIntent( config ); 
		<?php 
		} elseif( 'fade2' == $effect ) { ?>
	
  $(".dropdown li").hoverIntent(
	function(){	h = $(this).height() + 'px'; $(this).find("ul:first").animate( {opacity:'show', top:h}, '<?php echo $speed; ?>'); },
	function(){	h = $(this).height() + 5 + 'px'; $(this).find("ul:first").animate( {opacity:'hide', top:h}, '<?php echo $speed; ?>'); }
  ); 
  
	<?php }
		
		echo "\n});";
		echo "\n/* ]]> */</script>";
		echo "\n<!-- /Dropdown Menu Widget Effects -->";
		echo "\n\n ";

		} // fx active
		
	}
	
	function help_link($key, $text = '(?)'){
		echo '<a href="'.$this->help_url.'#' . $key. '" target="_blank" class="help-link">' . $text . '</a>';
	}

} 
// *** END OF CLASS ***

function get_dropdown_setting( $key, $default = '' ) {
	$settings = get_option('shailan_dropdown_menu');
	
	if( array_key_exists($key, $settings) ){
		return $settings[ $key ];
	} else {
		return $default;
	}
	
	return FALSE;
}

// Register widget
add_action('widgets_init', create_function('', 'return register_widget("shailan_DropdownWidget");'));

// Load translations
$plugin_dir = basename(dirname(__FILE__));
load_plugin_textdomain( 'shailan-dropdown-menu', false, $plugin_dir . '/lang');

/* Includes */
include('shailan-page-walker.php'); // Load custom page walker
include('shailan-category-walker.php'); // Load custom category walker

/* Custom widget */	
include('shailan-multi-dropdown.php'); // Load multi-dropdown widget

// Settings link
function shailan_dropdown_add_settings_link($links) {
    $settings_link = '<a href="options-general.php?page=dropdown-menu">Settings</a>';
    array_push( $links, $settings_link );
    return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter( "plugin_action_links_$plugin", 'shailan_dropdown_add_settings_link' );

// After activation redirect
register_activation_hook(__FILE__, 'shailan_dropdown_activate');
add_action('admin_init', 'shailan_dropdown_redirect');

function shailan_dropdown_activate() {
    add_option('shailan_dropdown_do_activation_redirect', true);
}

// Redirects to options page on activate
function shailan_dropdown_redirect() {
    if ( get_option( 'shailan_dropdown_do_activation_redirect', false ) ) {
        delete_option('shailan_dropdown_do_activation_redirect');
		$url = admin_url( 'options-general.php?page=dropdown-menu' );
        wp_redirect($url);
    }
}

// Template tag support
function shailan_dropdown_menu( $args = array() ){

	$type = get_dropdown_setting('shailan_dm_type');
	$exclude = get_dropdown_setting('shailan_dm_exclude');
	$inline_style = get_dropdown_setting('shailan_dm_style');
	$login = (bool) ( 'on' == get_dropdown_setting('shailan_dm_login') );
	$admin = (bool) ( 'on' == get_dropdown_setting('shailan_dm_admin') );
	$vertical = (bool) ( 'on' == get_dropdown_setting('shailan_dm_vertical') );
	$home = (bool) ( 'on' == get_dropdown_setting('shailan_dm_home') );
	$align = get_dropdown_setting('shailan_dm_align');
	$width = get_dropdown_setting('shailan_dm_width');
	
	$opts = array(
		'type' => $type,
		'exclude' => $exclude,
		'style' => $inline_style,
		'login' => $login,
		'admin' => $admin,
		'vertical' => $vertical,
		'home' => $home,
		'align' => $align,
		'width' => $width
	);
	
	$options = wp_parse_args( $args, $opts );
	
	if(!empty( $args['menu'] )){ $options['type'] = $args['menu']; }

	the_widget( 'shailan_DropdownWidget', $options );
	
}

/**
* Replaces nav menu callback function to use dropdown menu.
*
* @since 1.9.2
*/
function shailan_nav_menu_args_filter( $args ){

	$replace_enabled = get_dropdown_setting( 'shailan_dm_location_enabled' );
	
	if( 'on' == $replace_enabled ){
		$theme_location = get_dropdown_setting( 'shailan_dm_location' );
		if( $args['theme_location'] == $theme_location ){
			$args['fallback_cb'] = 'shailan_dropdown_menu';
		}
	}
	
	return $args;
} add_filter('wp_nav_menu_args', 'shailan_nav_menu_args_filter', 90, 1);

/*
function shailan_nav_menu_output_filter( $nav_menu, $args ){
	return $nav_menu;
} add_filter( 'wp_nav_menu', 'shailan_nav_menu_output_filter', 10, 2);
*/