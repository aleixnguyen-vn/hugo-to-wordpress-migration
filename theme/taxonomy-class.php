<?php get_header(); ?>
<main class="container">
  <div class="page-header">
<?php
$term = get_queried_object();               // luôn có ở trang taxonomy
if ( ! $term || is_wp_error( $term ) ) {
    echo '<h1>Term not found</h1>';
} else {
    printf(
        '<h1 class="font-bold" style="font-size:28px;color:#fff">Discover our %s cars collection</h1>',
        esc_html( $term->name ),
        intval( $term->count )
    );

    $desc = term_description( $term->term_id, $term->taxonomy );
    if ( $desc ) {
        echo '<p>' . wp_kses_post( $desc ) . '</p>';
    }
}
?>
<?php
    global $wp;
    $cat_slug = '';
    if (is_category()) {
      $cat = get_queried_object();
      $cat_slug = $cat->slug;
    }
    $base_url = esc_url(home_url(add_query_arg(array(), $wp->request))) . '/';
  ?>
    <section id="mod-list"  class="section-sm py-4">
        <div class="container" style="padding: 0px">
            <div class="row gx-2 justify-around items-center">
                <div class="lg:col-12">
                    <div class="row">
                            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <div class="md:col-4">
        <?php get_template_part('components/card'); ?>
    </div>
        <?php endwhile; endif; ?>
                    </div>
                </div>
            </div>
        </div>                 

    </section>

  <div id="pagination-wrapper"
       data-category="<?php echo esc_attr($cat_slug); ?>"
       data-baseurl="<?php echo $base_url; ?>"
       class="mt-8 text-center">
    <?php
$pagination_links = paginate_links([
  'total'     => $wp_query->max_num_pages,
  'current'   => max(1, get_query_var('paged')),
  'type'      => 'array',
  'prev_text' => '«',
  'next_text' => '»',
]);

if (is_array($pagination_links)) : ?>
  <ul class="inline-flex items-center justify-center space-x-2 text-sm">
    <?php foreach ($pagination_links as $link) : ?>
      <li>
        <?php
          // Xử lý class thủ công
          if (strpos($link, 'current') !== false) {
            echo str_replace('page-numbers current', 'btn btn-sm btn-success', $link);
          } elseif (strpos($link, 'dots') !== false) {
            echo str_replace('page-numbers dots', 'btn btn-sm btn-link', $link);
          } else {
            echo str_replace('page-numbers', 'btn btn-sm btn-info', $link);
          }
        ?>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

  </div>
</main>
<?php get_footer(); ?>
