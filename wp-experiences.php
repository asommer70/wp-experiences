<?php
/*
   Plugin Name: WP Experiences
   Plugin URI: https://thehoick.com/
   Version: 0.0.1
   Author: Adam Sommer
   Description: Experiences a plugin for WordPress to create a grid of Experiences linking to other pages of the site.
 */

defined( 'ABSPATH' ) or die( 'No please!' );

//
// Add the new post type.
//
function experiences_create_post_type() {
  register_post_type( 'experiences',
    [
      'labels' => ['name' => __( 'Experiences' ), 'singular_name' => __( 'Experience')],
      'public' => true,
      'has_archive' => true,
      'show_ui' => true,
      'menu_icon' => 'dashicons-palmtree',
      'query_var' => true,
      'capability_type' => 'post',
      'hierarchical' => true,
      'supports' => [
        'title',
        'thumbnail',
        'page-attributes',
      ],
      'rewrite' => ['slug' => 'experiences'],
    ]
  );
}
add_action('init', 'experiences_create_post_type');


//
// Create a meta box in the Admin for image size and link url.
//
function experiences_add_custom_box() {
  add_meta_box(
      'exp_image_link',
      'Experience Settings',
      'experiences_meta_box_html',
      ['experiences']
  );
}
add_action('add_meta_boxes', 'experiences_add_custom_box');

// HTML for the meta box.
function experiences_meta_box_html($post) {
  $link = get_post_meta($post->ID, 'experiences_link', true);
  $selected_size = get_post_meta($post->ID, 'experiences_size', true);
  
  ?>
  <label for="exp-link-url">Link URL</label>
  <br/>
  <input id="exp-link-url" name="exp-link-url" type="text" class="exp-input" placeholder="http://..." value="<?php echo $link; ?>" />

  <br/><br/>
  <label for="exp-image-size">Image Size</label>
  <br/>
  <select id="exp-image-size" name="exp-image-size">
    <option value="small" <?php echo (isset($selected_size) && $selected_size == 'small' ? 'selected' : ''); ?>>Small</option>
    <option value="medium" <?php echo (isset($selected_size) && $selected_size == 'medium' ? 'selected' : ''); ?>>Medium</option>
    <option value="large" <?php echo (isset($selected_size) && $selected_size == 'large' ? 'selected' : 'j'); ?>>Large</option>
  </select>
<?php
}

// Save the meta box entries.
function experiences_save_postdata($post_id) {
  if (array_key_exists('exp-link-url', $_POST)) {
    update_post_meta($post_id, 'experiences_link', $_POST['exp-link-url']);
  }

  if (array_key_exists('exp-image-size', $_POST)) {
    update_post_meta($post_id, 'experiences_size', $_POST['exp-image-size']);
  }
}
add_action('save_post', 'experiences_save_postdata');

// Add columns to the screen options of the admin index page.
function experiences_columns_head($defaults) {
  $defaults['featured_image'] = 'Featured Image';
  $defaults['experience_size'] = 'Size';
  $defaults['experience_link'] = 'Link';
  return $defaults;
}

// Featured image HTML.
function experiences_columns_content($column_name, $post_ID) {
  if ($column_name == 'featured_image') {
    $post_thumbnail_id = get_post_thumbnail_id($post_ID);
    $post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'featured_preview');

    echo '<img style="width: 150px;" src="' . $post_thumbnail_img[0] . '" />';
  }

  if ($column_name == 'experience_size') {
    echo get_post_meta($post_ID, 'experiences_size', true);
  }

  if ($column_name == 'experience_link') {
    $link = get_post_meta($post_ID, 'experiences_link', true);
    $parts = explode('/', $link);
    $last = end($parts);
    
    if (!isset($last) || empty($last) || $last == "\n") {
      $sl = count($parts) - 2;
      $last = $parts[count($parts) - 2];
    }
    
    echo '<a href="'. $link .'">'. $last .'</a>';
  }
}
add_filter('manage_experiences_posts_columns', 'experiences_columns_head', 10);
add_action('manage_experiences_posts_custom_column', 'experiences_columns_content', 10, 2);

//
// Allow do_action for embedding in the theme.
//
function experiences_action() {
  include(__DIR__ .'/template.php');
}
add_action( 'wp_experiences', 'experiences_action', 10, 1 );

