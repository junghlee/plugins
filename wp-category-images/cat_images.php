<?php
/*
Plugin Name: Category Images
Plugin URI: http://dmikam.wordpress.com/
Description: Permite adjuntar imágenes a categorías. Compatible con WP v.3.x.
Author: dmikam
Version: 3.0
Author URI: http://dmikam.wordpress.com/
*/


function my_cat_form_pre($p){
	ob_start();
}

function my_cat_form($cat){
	$form = ob_get_contents();
	ob_end_clean();
	$form = str_replace('<form name="edittag"','<form name="editcat" enctype="multipart/form-data" ', $form);

	$attachments = get_cat_images($cat->term_id);
	$image_list = "";
	foreach ($attachments as $att){
		$image_list .= "<li>".wp_get_attachment_image($att->ID, $size='thumbnail', $icon = false)."<input type=\"checkbox\" class=\"remove_cat_image\" name=\"remove_cat_image[$att->ID]\" title=\"remove image\" /></li>";
	}

	$form = str_replace('<table class="form-table">'
		,'<style>.category-thumbnail li {float:left;position:relative;} .category-thumbnail li .remove_cat_image {position:absolute;top:2px; right:2px; z-index:2;} </style>'
			.'<ul class="category-thumbnail clearfix">'.$image_list.'</ul>'
			.'<table class="form-table"><tr><th>'.__('Set category image').'</th><td><input type="hidden" name="att_cat_id" value="'.$cat->cat_ID.'" /><input type="file" name="cat_image_0" value="Imagen"></td></tr>'
		,$form
	);
	echo $form;
}


function my_cat_form_save($cid){
//	$cat_id = (int)$_POST['att_cat_id'];

	$cat_id = (int)$_POST['tag_ID'];
	$catinfo = get_category($cat_id);
	if (!empty($_POST['remove_cat_image'])){
		$cat_images = get_cat_images($cat_id);

/*
			var_dump(get_cat_images($cat_id));
			var_dump('<br />---------<br />',$_POST['remove_cat_image']);
			echo cat_image($cat_id,true);
			die();
*/

		foreach($cat_images as $cat_image){
			if (isset($_POST['remove_cat_image'][$cat_image->ID])){
				wp_delete_attachment($cat_image->ID);
			}
		}
	}


	foreach (array(0) as $i ){  // future posibility to upload multiple images
		if (empty($_FILES['cat_image_'.$i]) || $_FILES['cat_image_'.$i]['error']!=0){
			continue;
		}
		$FILE = $_FILES['cat_image_'.$i];
		$overrides = array( 'action' => 'wp_handle_upload','test_form'=>false );
		$file = wp_handle_upload($FILE, $overrides);
		if ($file){
			if ( isset($file['error']) )
				die( $file['error'] );

			$url = $file['url'];
			$type = $file['type'];
			$file = $file['file'];
			$filename = basename($file);

			// Construct the object array
			$object = array(
				'post_title' => "[".$catinfo->name."]".$filename,
				'post_content' => var_export($_POST,true),//$url,
				'post_mime_type' => $type,
				'guid' => $url
			);

			// Save the data
			$id = wp_insert_attachment($object, $file);

			wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );

			do_action('wp_create_file_in_uploads', $file, $id); // For replication
			if (add_post_meta($id, 'category_id', $cat_id, true)) {
				update_post_meta($id, 'category_id', $cat_id, true);
			}
		}
	}

}


function get_cat_images($cid,$limit=-1){
	return get_posts('numberposts='.$limit.'&post_type=attachment&meta_key=category_id&meta_value='.$cid);
}


function cat_image($cid=0,$echo=true){
	if (!$cid){
		$cid = get_the_ID();
	}
	$return = "";
	$attachments = get_cat_images($cid,1);
	foreach ($attachments as $att){
		$return .= wp_get_attachment_image($att->ID, $size='thumbnail', $icon = false);
	}

	return $return;
}

add_action("edit_category_form_pre","my_cat_form_pre");
add_action("edit_category_form",		"my_cat_form");
add_action("edit_category",			"my_cat_form_save");

?>