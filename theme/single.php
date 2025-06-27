<?php get_header(); ?> <main class="container"> <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?> <article class="single-post">
  
    <div class="page-header" style="margin-bottom: 10px;">
      <h1 style="font-size: 30px; align-items: baseline" class="flex flex-row justify-between">  <?php the_title(); ?> 
      <?php 
      $views = get_post_meta(get_the_ID(), '_pvc_views', true);
      echo '<div style="font-size: 14px; color: gray; margin-top: 4px"><i class="fa-solid fa-eye"></i> ' . ($views ?: 0) . ' views</div>';
  ?>
  </h1>
  </div>
  <div class="flex flex-col show-flex-row justify-between mb-6"> 
    <?php
      if (has_category('cars')) {
        get_template_part('components/car-info');
          } elseif (has_category('tracks')) {
        get_template_part('components/track-info');
          } elseif (has_category('skins')) {
        get_template_part('components/skin-info');
          } elseif (has_category('others')) {
        get_template_part('components/other-info');
          } else {
        the_content(); 
          }
      ?> 
    </div>
  <div class="post-content"> <?php the_content(); ?> </div>

<?php if (get_field('csp_required')): ?>
<div class="notice2 info text-light" style="margin-bottom:4px">
<p><b>This car requires <a class="font-bold" target="_blank" href="/other/csp-0-2-10-preview1-custom-shaders-patch/">CSP<i class="fa-solid fa-link fa-xs" style="padding-left: 3px;padding-right: 3px;"></i></a> to work properly.</b></p></div>
<?php endif; ?>
<?php 
$dlc_name = get_field('dlc');
$dlc_link = get_field('dlc_link');

if ($dlc_name) {
    echo '<div class="notice2 info2" style="margin-top:7px;border-radius: var(--radius-sm)">';
    echo '<p><b> DLC <a class="font-bold" target="_blank" href="' . esc_url($dlc_link) . '">';
    echo esc_html($dlc_name) . ' <i class="fa-solid fa-cart-arrow-down fa-xs" style="padding-right:3px"></i></a> is required too</b></p>';
    echo '</div>';
}
?>

  <div class="download-box thumbnail modsfire-style">
<h2 class="flex items-center gap-2 justify-between flex-wrap" style="align-items: start;">
  Download <?php the_field('mod_name'); ?> <?php
    $version = get_field('version');
    if ($version && $version !== '-') {
      echo ' (' . esc_html($version) . ')';
    }
  ?> for free

  <span id="rating-box" data-post-id="<?php echo get_the_ID(); ?>" style="display: flex; gap: 2px; align-items: start;">
    Rating:  
    <?php
      $post_id = get_the_ID();
      $total = get_post_meta($post_id, 'rating_total', true) ?: 0;
      $count = get_post_meta($post_id, 'rating_count', true) ?: 0;
      $avg = ($count > 0) ? ($total / $count) : 0;
      $rounded = round($avg);

      for ($i = 1; $i <= 5; $i++) {
        $class = ($i <= $rounded) ? 'star filled' : 'star';
        echo '<svg class="' . $class . '" data-star="' . $i . '" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:20px;height:20px;cursor:pointer;">
          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.946a1 1 0 00.95.69h4.148c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 00-.364 1.118l1.287 3.946c.3.921-.755 1.688-1.538 1.118l-3.36-2.44a1 1 0 00-1.175 0l-3.36 2.44c-.783.57-1.838-.197-1.538-1.118l1.287-3.946a1 1 0 00-.364-1.118L2.075 9.373c-.783-.57-.38-1.81.588-1.81h4.148a1 1 0 00.95-.69l1.286-3.946z"/>
        </svg>';
      }
    ?>
    
 <span class="rating-text"
        data-avg="<?php echo round($avg, 2); ?>"
        data-count="<?php echo $count; ?>"
        style="font-size: 14px; color: #aaa; align-self: center;">
  <?php
    if ($count == 1) {
      echo '(1 vote)';
    } elseif ($count > 1) {
      echo "($count votes)";
    }
    // Nếu 0 thì không echo gì
  ?>
  </span>
  </span>
</h2>

  </span>
      <p>
        <b>Credit: </b>
        <strong class="font-white"> <?php if (get_field('credit_link')): ?> <a href="
						<?php the_field('credit)link'); ?>" rel="nofollow noreferrer" target="_blank"> <?php endif; ?> <?php the_field('credit'); ?> </a>
        </strong>
      </p>
<?php if (!has_category('others')) : ?>
  <p>
    <b>File Name: </b>
    <strong> <?php the_field('folder'); ?> </strong>
  </p>
<?php endif; ?>
<?php if ( get_field('file_size') ) : ?>
  <p>
    <b>File Size: </b>
    <strong><?php the_field('file_size'); ?></strong>
  </p>
<?php endif; ?>

      <p>
        <span>Added: <?php echo get_the_date('F j, Y'); ?> </span> &nbsp;&nbsp; <?php if (get_the_date() !== get_the_modified_date()): ?> | &nbsp;&nbsp; <span>Updated: <?php echo get_the_modified_date('F j, Y'); ?> </span> <?php endif; ?>
      </p>
      <div class="download-button">
        <a href="
					<?php the_field('downloadlink'); ?>" title="Download from external host 
						<?php the_field('download_host'); ?>" rel="nofollow norefferer" target="_blank">
          <img src="
						<?php echo get_site_url(); ?>/wp-content/uploads/assets/logo/
						<?php the_field('download_host'); ?>.png" alt="Download from external host 
						<?php the_field('download_host'); ?>">
          <div class="label"> <?php the_field('download_host'); ?> <br>
            <span>External + Ads</span>
          </div>
        </a>
      </div>
    </div>
  </article> 
  <?php endwhile; endif; ?>
<?php
$limit = in_category('cars') ? 9 : 12;
$related = get_related_mods($limit);

if ( $related ) :
?>


<div class="adsense-slot my-6 text-center">
</div>

<section id="mod-list" class="section-sm py-8 mt-8">
    <div class="container" style="padding: 0px">
        <h2 class="font-xl font-bold mb-4 flex flex-row items-center">
        <i class="mr-2 font-xs fa-solid fa-square-caret-down"></i>
        Related Mods
        </h2>
        <div class="row gx-2 justify-around items-center">
            <div class="lg:col-12">
                <div class="row">
                    <?php while ( $related->have_posts() ) : $related->the_post(); ?>
                    <?php
                      $col_class = in_category('cars') ? 'md:col-4' : 'md:col-3';
                      ?>
                      <div class="<?php echo $col_class; ?>">
                          <?php get_template_part('components/card'); ?>
                      </div>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php endif; ?> 
</main>
<?php get_footer(); ?>