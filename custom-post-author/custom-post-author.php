<?php
/**
 * Plugin Name: Custom Post Author
 * Description: With this plugin you can change the authors name and url within the post. This might be needed, if you have many guest posts.
 * Version:		1.0.1
 * Author:		HerrLlama for Inpsyde GmbH
 * Author URI:	http://inpsyde.com
 * Licence:		GPLv3
 */

// check wp
if ( ! function_exists( 'add_action' ) )
	return;

// constants
define( 'CPA_TEXTDOMAIN', 'custom-post-author-td' );

// kickoff
add_action( 'plugins_loaded', 'cpa_init' );
function cpa_init() {
	// we only need this in admin area
	if ( ! is_admin() )
		return;

	// localization
	load_plugin_textdomain( CPA_TEXTDOMAIN, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	// adds the meta boxes and save stuff
	add_filter( 'add_meta_boxes', 'cpa_add_meta_boxes' );
	add_filter( 'save_post', 'cpa_save_meta_data' );
}

// change the authors name
function cpa_the_author( $display_name ) {

	// Return on admin pages
	if ( is_admin() )
		return;

	// Set default
	$return_name = $display_name;

	// Get current post
	global $post;

	// Check Name
	$author_name = get_post_meta( $post->ID, 'cpa_author', TRUE );
	if ( '' != $author_name )
		$return_name = $author_name;

	// Check URL
	$author_email = get_post_meta( $post->ID, 'cpa_author_url', TRUE );
	if ( '' != $author_email )
		$return_name = 'Email <a href="mailto:' . $author_email . '?Subject=Gracepoint Resources: ' . get_the_title($post->ID) . '">'. $return_name . '</a>';

	// register filter for other plugins
	$return_name = apply_filters( 'cpa_the_author', $return_name, $author_name, $author_email );

	return $return_name;
}

// adds the meta box and the save post stuff
function cpa_add_meta_boxes() {
	add_meta_box( 'custom-post-author', __( 'Custom Post Author', CPA_TEXTDOMAIN ), 'cpa_meta_box' );
}
function cpa_meta_box() {
	global $post;
	?>
	<table class="form-table">
		<tr>
			<th><label for="cpa_author"><?php _e( 'Author Name', CPA_TEXTDOMAIN ); ?></label></th>
			<td><input type="text" class="regular-text" name="cpa_author" id="cpa_author" value="<?php echo get_post_meta( $post->ID, 'cpa_author', TRUE ); ?>" /></td>
		</tr>
		<tr>
			<th><label for="cpa_author_url"><?php _e( 'Author Email', CPA_TEXTDOMAIN ); ?></label></th>
			<td><input type="text" class="regular-text" name="cpa_author_url" id="cpa_author_url" value="<?php echo get_post_meta( $post->ID, 'cpa_author_url', TRUE ); ?>" /></td>
		</tr>
	</table>
	<?php
}
function cpa_save_meta_data( $post_id ) {

	// Preventing Autosave, we don't want that
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;
	if ( !! wp_is_post_revision( $post_id ) )
		return;

	// Add Post Meta if there is one
	if ( ! isset( $_POST[ 'cpa_author' ] ) )
		$_POST[ 'cpa_author' ] = '';
	if ( ! isset( $_POST[ 'cpa_author_url' ] ) )
		$_POST[ 'cpa_author_url' ] = '';

	update_post_meta( $post_id, 'cpa_author', $_POST[ 'cpa_author' ] );
	update_post_meta( $post_id, 'cpa_author_url', $_POST[ 'cpa_author_url' ] );
}
