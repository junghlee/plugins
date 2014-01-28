<?php

/*
Plugin Name: Members Import
Plugin URI: 
Description: Allows the batch importation of users/members via an uploaded CSV file.
Author: Manish Kumar Agarwal
Author URI: http://www.youngtechleads.com
Version: 1.1
Author Emailid: manishkrag@yahoo.co.in/manisha@mindfiresolutions.com 
*/


// add admin menu
add_action('admin_menu', 'memberimport_menu');

function memberimport_menu() {	
	add_submenu_page( 'users.php', 'Members Import', 'Members Import', 'manage_options', 'members-import', 'memberimport_page');	
}

// show import form
function memberimport_page() {

	global $wpdb;
	// User data fields list used to differentiate with user meta
	$userdata_fields       = array(
		'user_login', 'user_pass',
		'user_email', 'user_url', 'user_nicename',
		'display_name', 'user_registered', 'first_name',
		'last_name', 'nickname', 'description',
		'rich_editing', 'comment_shortcuts', 'admin_color',
		'use_ssl', 'show_admin_bar_front', 'show_admin_bar_admin',
		'role'
	);
  	if (!current_user_can('manage_options'))
    	wp_die( __('You do not have sufficient permissions to access this page.') );

	// if the form is submitted
	if ($_POST['mode'] == "submit") {
	
		$arr_rows = file($_FILES['csv_file']['tmp_name']);
		$login_username        = isset( $_POST['login_username'] ) ? $_POST['login_username'] : false;
		$password_nag          = isset( $_POST['password_nag'] ) ? $_POST['password_nag'] : false;
		$new_member_notification = isset( $_POST['new_member_notification'] ) ? $_POST['new_member_notification'] : false;
		
		// loop around
		if ( is_array( $arr_rows ) ) {
			$first = true;
			$not_imported = '';
			$flag = 0;
			$not_import_message = "";
			foreach ( $arr_rows as $row ) {
				
				// If a row is empty, just skip it
				if ( empty( $row ) ) {
					if ( $first )
						break;
					else
						continue;
				}

				// If we are on the first line, the columns are the headers
				if ( $first ) {
					//replace " by null
					$calumn_names = str_replace('"', '', $row);
					// split into values
					$headers = split(",", $calumn_names);
					$first = false;
					continue;
				}

				// split into values
				$arr_values = str_replace('"', '', $row);
				$arr_values = split(",", $arr_values);
				
				// Separate user data from meta
				$userdata = $usermeta = array();
				
				foreach ( $arr_values as $ckey => $cvalue ) {
					$column_name = trim( $headers[$ckey] );
					$cvalue = trim( $cvalue );

					if ( empty( $cvalue ) )
						continue;

					if ( in_array( $column_name, $userdata_fields ) ) 
						$userdata[$column_name] = $cvalue;
					else
						$usermeta[$column_name] = $cvalue;
					
				}
				// If no user data, bailout!
				if ( empty( $userdata ) )
					continue;
				
				// If creating a new user and no password was set, let auto-generate one!
				if ( empty( $userdata['user_pass'] ) )
					$userdata['user_pass'] = wp_generate_password( 12, false );
				
				$userdata['user_login'] = strtolower($userdata['user_login']);
				
				if ( ( $login_username ) && ( $userdata['user_email'] == '' ) )
					$userdata['user_email'] = $userdata['user_login'];
				else if ( ( $login_username ) && ( $userdata['user_login'] == '' ) )
					$userdata['user_login'] = $userdata['user_email'];
					
				$user_id = wp_insert_user( $userdata );
				
				// Is there an error?
				if ( is_wp_error( $user_id ) ) {
					$flag = 1;
					$not_imported_usernames  .= "<b>" . $userdata['user_login'] . '</b> ' . $user_id->errors[existing_user_login][0] . "<br />";
				}
				else {
					// If no error, let's update the user meta too!
					if ( $usermeta )
						foreach ( $usermeta as $metakey => $metavalue ) {
							$metavalue = maybe_unserialize( $metavalue );
							update_user_meta( $user_id, $metakey, $metavalue );
						}

					// If we created a new user, maybe set password nag and send new user notification?
					if ( $password_nag )
						update_user_option( $user_id, 'default_password_nag', true, true );

					if ( $new_member_notification )
						wp_new_user_notification( $user_id, $userdata['user_pass'] );

					$user_ids[] = $user_id;
				}

			}	// end of 'for each around arr_rows'

			if( $flag == 1 ) {
				$not_import_message = "Following user(s) are not imported as they are already registered in your website:<br />";
				$not_import_message .= $not_imported_usernames;
				$not_import_message .= 'Except above ';
			}
			
			$html_message = "<div class='updated'>";
			$html_message .= $not_import_message;
			$html_message .= "All users/members appear to be have been imported successfully.";
			$html_message .= "</div>";
			
		} // end of 'if arr_rows is array'
		else
			$html_message = "<div class='updated' style='color: red'>It seems the file was not uploaded correctly.</div>";
	} 	// end of 'if mode is submit'
	

?>
<div class="wrap">	
	<?php echo $html_message; ?>	
	<div id="icon-users" class="icon32"><br /></div>
	<h2>CSV Members Import</h2>
	<p>Please select the CSV file you want to import below.</p>
	
	<form action="users.php?page=members-import" method="post" enctype="multipart/form-data">
		<input type="hidden" name="mode" value="submit">
		<input type="file" name="csv_file" />		
		<input type="submit" value="Register" />

		<br/>
		<table>
			<tr valign="top">
				<th scope="row">Login with email ID: </th>
				<td>
					<label for="login_username">
						<input id="login_username" name="login_username" type="checkbox" value="1" />
						Username and e-mail ID are same.
					</label>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Notification: </th>
				<td>
					<label for="new_member_notification">
						<input id="new_member_notification" name="new_member_notification" type="checkbox" value="1" />
						Send username and password to new users.
					</label>
				</td>
			</tr>
			<tr valign="top">
					<th scope="row">Password nag: </th>
					<td>
						<label for="password_nag">
							<input id="password_nag" name="password_nag" type="checkbox" value="1" />
							Show password nag on new users signon.
						</label>
					</td>
				</tr>
			<tr>
				<th scope="row">Notice: </th>
				<td>The CSV file should be in the following format:</td>
			</tr>
			<tr>
				<th scope="row"></th>
				<td>1: Fields name should be at the top line in CSV file separated by comma(,) and delimitted by double quote(").</td>
			</tr>
		</table>
	</form>
	<p style="color: red">Please make sure to have back up your database before proceeding!</p>	
</div>
<?php
}	// end of 'function memberimport_page()'
?>