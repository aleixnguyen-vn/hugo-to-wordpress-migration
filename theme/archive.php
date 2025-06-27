<?php get_header(); ?>
<main class="container">
  <div class="page-header">
    
<?php
  $category = get_queried_object();


  $cat = get_queried_object();

  if ( $cat && isset( $cat->slug ) ) {
    $slug = $cat->slug;

    if ( $slug === 'cars' ) {
    echo '<h1 class="font-bold" style="font-size: 28px; color:#fff">More than ' . $category->count . ' cars to pick, ready to race!</h1>';

    } elseif ( $slug === 'tracks' ) {
    echo '<h1 class="font-bold" style="font-size: 28px; color:#fff">More than ' . $category->count . ' tracks available with over hundred configurations to drive</h1>';

    } elseif ( $slug === 'motorcycles' ) {
    echo '<p class="text-gray-500 mb-6">Speed up your ride with top motorcycle mods.</p>';
    }
    }
  ?>

<p>
  <?php echo category_description($category->term_id); ?>
</p>
</div>
<div class="ir-toolbar flex items-center justify-between mb-0 mt-4" style="margin-bottom: 0px;">
  <!-- TRÁI: SEARCH -->
  <div class="input-icon-wrapper relative">
  <form method="get" action="<?php echo esc_url(home_url()); ?>" class="ir-search-form flex-1">
    <?php if (is_category()) : ?>
      <input type="hidden" name="cat" value="<?php echo get_queried_object_id(); ?>">
    <?php endif; ?>
    <label for="mod-search" class="sr-only"><i class="fa-solid fa-magnifying-glass"></i> Search <?php single_cat_title(); ?></label>
    <input type="text"
        id="mod-search"
        placeholder=" Search mods"
        class="form-control input-with-search ir-search-input"
        style="width: 300px" />
  </form>
    </div>
 
<div class="flex items-center gap-1">
  <label for="sort-select" class="text-md">Sort by:</label>

<form class="ir-sort-form" onsubmit="return false;">
  <select name="sort" id="sort-select" class="bg-[#0d1117] text-black form-control ir-sort-select">
      <option value="latest">Newest First</option>
      <option value="oldest">Oldest First</option>
      <option value="views">Most Viewed</option>
      <option value="rating">Highest Rated</option>
      <option value="title_asc">Name ▲</option>
      <option value="title_desc">Name ▼</option>
  </select>
</form>
<button id="reset-filters"
        class="btn btn-sm btn-secondary items-center"
        title="Reset Filters">
  <i class="fa-solid fa-rotate-left text-xm hover:animate-spin"></i>
  <span style="font-size: 17px">Reset</span>
</button>
    </div>


</div>

<?php
if (is_category('cars')) {
  echo '<div class="page-header" style="margin-top: 6px; margin-bottom: 8px;"></div>';
} else if (is_category('tracks')) {
  echo '<div class="page-header" style="margin-top: 6px; margin-bottom: 8px;"></div>';
}; 
?>


<form id="mod-filter" class="flex flex-row md:flex-row flex-wrap gap-4">

<?php
if (is_category('cars')) {
  get_template_part('components/car-filter', 'cars');
} else if (is_category('tracks')) {
  get_template_part('components/track-filter', 'tracks');
} else if (is_category('motorcycles')) {
  get_template_part('components/motorcycle-filter', 'motorcycles');
} 
?>
</form>

<?php
if (is_category('cars')) {
  echo '<div class="page-header" style="margin-top: 10px; margin-bottom: 8px;"></div>';
} else if (is_category('tracks')) {
  echo '<div class="page-header" style="margin-top: 10px; margin-bottom: 8px;"></div>';
} else if (is_category('motorcycles')) {
  get_template_part('components/motorcycle-filter', 'motorcycles');
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
<?php
$col_class = is_category('tracks') ? 'md:col-3' : 'md:col-4';
?>

<section id="mod-list" class="section-sm py-4">
    <div class="container" style="padding: 0px">
        <div class="row gx-2 justify-around items-center">
            <div class="lg:col-12">
                <div class="row">
                    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                        <div class="<?php echo $col_class; ?>">
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
