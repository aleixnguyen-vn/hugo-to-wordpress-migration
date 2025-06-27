<?php get_header(); ?> <main class="container"> <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?> <article class="single-post">
    <div class="page-header" style="margin-bottom: 10px;">
      <h1 style="font-size: 30px; align-items: baseline" class="flex flex-row justify-between">  <?php the_title(); ?> 
      <?php 
      $views = get_post_meta(get_the_ID(), 'view_count', true);
      echo '<div style="font-size: 14px; color: gray; margin-top: 4px"><i class="fa-solid fa-eye"></i> ' . ($views ?: 0) . ' views</div>';
  ?>
  </h1>
  </div>
  <div class="flex flex-col show-flex-row justify-between mb-6"> 
    <?php
        get_template_part('components/page-info');
      ?> 
    </div>
  <div class="post-content"> <?php the_content(); ?> </div>
  </article> 
  <?php endwhile; endif; ?>

<div class="adsense-slot my-6 text-center">
</div>

</main>
<?php get_footer(); ?>