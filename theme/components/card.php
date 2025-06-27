<article class="thumbnail" itemscope itemtype="https://schema.org/Article">
  <div class="grid-item-content-container" onclick="javascript: document.location = '<?php the_permalink(); ?>';">
    <div class="grid-item-content-back relative" >
        <?php if ( is_archive() ) : ?>
            <?php
        /* -------- Badge HOT -------- */
        $views       = (int) get_post_meta( get_the_ID(), '_pvc_views', true );
        $rating_avg  = (float) get_field( 'rating_avg' );  
        if ( ! is_single() && $views >= 500 && $rating_avg >= 3.5 ) {
          echo '<span class="badge-hot pr-2"><i class="fas fa-fire"></i> Hot</span>';
        }
      ?>
      <?php endif; ?>
      <div class="grid-item-img-container">
        <a href="<?php the_permalink(); ?>" data-post-id="<?php the_ID(); ?>" itemprop="url">
          <?php
            $imgur_id = get_field('thumb');

            if ( $imgur_id ) {
              $imgur_url = 'https://i.imgur.com/' . esc_attr($imgur_id) . '.jpg';
              echo '<img src="' . esc_url($imgur_url) . '" alt="' . get_the_title() . '" itemprop="image">';
              echo '<meta itemprop="url" content="' . esc_url($imgur_url) . '">';
            } elseif ( has_post_thumbnail() ) {
              the_post_thumbnail('mod-thumb', ['alt' => get_the_title(), 'itemprop' => 'image']);
              echo '<meta itemprop="url" content="' . get_the_post_thumbnail_url() . '">';
            } else {
              echo '<img src="https://via.placeholder.com/370x208?text=No+Image" alt="' . get_the_title() . '" itemprop="image">';
            }
          ?>
        </a>
      </div>
      
    </div>
    <div class="grid-item-content">
      <p class="pt-1 pl-0.8">
        <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" style="font-size: 14px;" class="font-bold item-desc pt-2 pr">
          <span itemprop="headline"><?php the_title(); ?></span>
        </a>
      </p>
      <p>
        <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="font-bold btn btn-success btn-block">
          <b>LEARN MORE</b>
        </a>
      </p>
    </div>
    <meta itemprop="datePublished" content="<?php echo get_the_date('c'); ?>">
    <meta itemprop="author" content="<?php echo get_the_author(); ?>">
    <?php
      $rating_avg = get_field('rating_avg');
      if ($rating_avg) {
        echo '<meta itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">';
        echo '<meta itemprop="ratingValue" content="' . esc_attr($rating_avg) . '">';
        echo '</meta>';
      }
    ?>
    <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><span class="img-link"></span></a>
  </div>
</article>
