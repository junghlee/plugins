=== Members Import ===
Contributors: manishkrag
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=manishkrag@yahoo.co.in&item_name=Members%20Import%20&return=http://www.youngtechleads.com/thanks/
Tags: csv, user, import, users, member, members, batch, batch import, batch import members, batch import users, one click user creation, import csv, import from csv, user registration, wordpress members import,wordpress csv import, wordpress users import, buddypress, buddypress members import
Stable tag: 1.1
Tested up to: 3.8

This plug-in allows you to batch import of users/members taken from an uploaded CSV file.

== Description ==

This allows you to batch import of users/members taken from an uploaded CSV file.

It will add users/members with basic information, including firstname, lastname, username, password and email address.
Each user who is added will be a 'subscriber' if value not provided in csv file, and be able to login to your site.

You can also choose to send a notification to the new users and to display password nag on user login.

There are no additional options available at the moment, but if you want to add a bunch of users in one go, this will do this for you quickly.

<b>Basic Fields:</b>
<br />user_login
<br />user_pass
<br />user_email 
<br />user_url 
<br />user_nicename
<br />display_name 
<br />user_registered 
<br />first_name
<br />last_name 
<br />nickname 
<br />description
<br />rich_editing 
<br />comment_shortcuts 
<br />admin_color
<br />use_ssl 
<br />show_admin_bar_front 
<br />show_admin_bar_admin
<br />role

**Author's Other Plugins:** 
<br /> <a href="http://wordpress.org/extend/plugins/ajaxify-wordpress-site/" target="_blank">Ajaxify WordPress Site(AWS)</a>

== Features ==

* Create users/members in wordpress site in a single click.
* Imports all users/members fields.
* Imports user/members meta fields.
* Allows setting user role.
* Sends new user notification (if the option is selected)
* Shows password nag on user login (if the option is selected)
* If username already present in database then this user will not register, in this way we can avoid the multi use of same username.

== Installation ==

* Upload `members-import` to the `/wp-content/plugins/` directory.
* Activate the plugin through the 'Plugins' menu in WordPress.
* You will see a new 'Members Import' option under the existing 'Users' menu area.
* Data in csv file should be delimitted by double quote(").

== Frequently Asked Questions ==

= CSV file format? =

* CSV file should have all the field name separated by comma(,) and delimitted by double quote(") in first row.
* Data present from the second row are assume as data to be import.
* In a row all the data should be separated by comma(,) and delimitted by double quote(").

== Changelog ==

= 1.1 =
* If you are using email id as user login/username then in CSV use only one and select first check box before import.
= 1.0.0 =
* First release of plug-in. No changes.
