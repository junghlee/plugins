=== Database Browser ===
Contributors: mrwiblog
Donate link: http://www.stillbreathing.co.uk/donate/
Tags: database, table, browse, query, download, export
Requires at least: 3.0.1
Tested up to: 3.5.1
Stable tag: 1.1.4

Easily query and browse tables in your database, and download in XML, JSON, CSV and HTML format

== Description ==

Ever needed to check some data in one of your WordPress database tables, but don't have PHPMyAdmin available? Now you can easily query your data and export it in HTML, XML, CSV and JSON formats with a simple plugin.

There are other options, for example you can write your own 'where' and 'order by' clauses, and pagination is built-in. You can also see the complete query run against your database.

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory, or install from the Plugin browser
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Can I delete my data? =

Yes, it is possible to delete or modify data using the custom query, 'Where' and 'Order by' boxes. Be careful.

= Who can export the data? =

Only people with export permisions - the people who can run your WordPress export file.

== Screenshots ==

1. The database browser form

== Upgrade notice ==

Version 1.1 includes security additions which will help protect against hacking.

== Changelog ==

= 1.1.4 (2013/05/29) =

Fixed load_plugin_textdomain() call so translations will work properly.

= 1.1.3 (2013/01/05) =

Fixed incorrect $wpdb->prepare() call.

= 1.1.2 (2011/10/11) =

Fixed SQL for column names which are keywords.

= 1.1.1 (2011/01/04) =

Fixed incorrect link for CSV download. Changed table cell styling. Removed pagination links for custom queries. Removed SQL_CALC_FOUND_ROWS in custom queries.

= 1.1 (2010/12/22) =

Added the ability to run custom queries, and export the results. Also added nonce security (thanks to Julio from Boiteaweb.fr). Added Plugin Register.

= 1.0 (2010/12/06) =

Initial version committed.
