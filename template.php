<?php
  $experiences = get_posts(['numberposts' => -1, 'post_type' => 'experiences', 'orderby' => 'menu_order', 'order' => 'ASC',]);
?>

<div class="row experiences">
  <h2>Experiences</h2>

  <ul class="feature-grid">
    <?php foreach($experiences as $experience) { ?>
      <?php
        $link = get_post_meta($experience->ID, 'experiences_link', true);
        $size = get_post_meta($experience->ID, 'experiences_size', true);
        $image_url = wp_get_attachment_url(get_post_thumbnail_id($experience->ID));
      ?>
      
      <li class="item-grid experience-image <?php echo $size; ?>">
	<a href="<?php echo $link; ?>">
	  <div class="ribbon">
	    <h1><?php echo $experience->post_title; ?></h1>
	  </div>
	  
	  <img class="cover" alt="<?php echo $experience->post_title; ?>" src="<?php echo $image_url; ?>" />
	</a>
      </li>
    <?php } ?>
  </ul>
</div>

