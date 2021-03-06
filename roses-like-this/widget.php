<?php
/**
 * Popular Post (Most  Liked) Widget Class
 */
class MostLikedPosts extends WP_Widget {
  /** constructor */
  function __construct() {
    parent::__construct('mostlikedposts', 'Most Liked Posts');
  }

  /** @see WP_Widget::widget */
  function widget($args, $instance) {
    extract($args);
    $title               = apply_filters('widget_title', $instance['title']);
    $numberOfPostsToShow = apply_filters('widget_numberOfPostsToShow', $instance['numberOfPostsToShow']);
    print $before_widget;
    if ($title)
      echo $before_title . $title . $after_title;

    $queried = get_queried_object();
    $cats = array();
    if (is_single()) {
        $cats = wp_get_post_categories( $queried->ID );
    } else if (is_category()) {
        array_push($cats, $queried->cat_ID);
    }
    $cat_list = implode(',',$cats);


    global $wpdb;
    $post_status = "AND $wpdb->posts.post_status = 'publish'";
    if (is_user_logged_in()) {
        $post_status = "AND ($wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'private')";
    }
    $querystr = "
    SELECT $wpdb->posts.*
    FROM $wpdb->posts, $wpdb->postmeta, $wpdb->term_relationships, $wpdb->term_taxonomy
    WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id
    AND $wpdb->posts.ID = $wpdb->term_relationships.object_id
    AND $wpdb->term_taxonomy.term_taxonomy_id = $wpdb->term_relationships.term_taxonomy_id
    AND $wpdb->postmeta.meta_key = '_likes'" . $post_status . "
    AND $wpdb->posts.post_password = ''
    AND $wpdb->posts.post_type = 'post'
    AND $wpdb->term_taxonomy.term_id IN ($cat_list)
    ORDER BY $wpdb->postmeta.meta_value DESC
    LIMIT " . $numberOfPostsToShow;

    $pageposts = $wpdb->get_results($querystr, OBJECT);
    if ($pageposts):
      global $post;
      print "<ul>";
      foreach ($pageposts as $post):
        setup_postdata($post);
?>
  <li><a href="<?php
        the_permalink();
?>" rel="bookmark" title="Permanent Link to <?php
        the_title();
?>">
    <?php
        the_title();
?></a> (<?php
        print get_post_meta(get_the_id(), "_likes", 1);
        echo __(' likes', 'like_this');
?> )</li>
     <?php
      endforeach;
      print "</ul>";
?>
 <?php
    endif;

    print $after_widget;

  }

  /** @see WP_Widget::update */
  function update($new_instance, $old_instance) {
    $instance          = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);

    if (is_numeric($new_instance['numberOfPostsToShow'])) {
      $instance['numberOfPostsToShow'] = strip_tags($new_instance['numberOfPostsToShow']);
    } else {

      $instance['numberOfPostsToShow'] = strip_tags("5");
    }
    return $instance;
  }

  /** @see WP_Widget::form */
  function form($instance) {
    if ($instance) {
      $title               = esc_attr($instance['title']);
      $numberOfPostsToShow = esc_attr($instance['numberOfPostsToShow']);
    } else {
      $title               = __('Most Liked Posts', 'like_this');
      $numberOfPostsToShow = __('5', 'like_this');
    }
?>
    <p>
    <label for="<?php
    echo $this->get_field_id('title');
?>"><?php
    _e('Title:');
?></label>
    <input class="widefat" id="<?php
    echo $this->get_field_id('title');
?>" name="<?php
    echo $this->get_field_name('title');
?>" type="text" value="<?php
    echo $title;
?>" />
    </p>

    <p>
    <label for="<?php
    echo $this->get_field_id('numberOfPostsToShow');
?>"><?php
    _e('Number of Posts to Show:');
?></label>
    <input class="shortfat" id="<?php
    echo $this->get_field_id('numberOfPostsToShow');
?>" name="<?php
    echo $this->get_field_name('numberOfPostsToShow');
?>" width="3" type="text" value="<?php
    echo $numberOfPostsToShow;
?>" />
    </p>
    <?php
  }

} // class MostLikedPosts

add_action('widgets_init', create_function('', 'return register_widget("MostLikedPosts");'));
?>
