<?php
/*
 * Template: taxonomy-car_class.php
 * Handles URLs such as /cars/gt3/  /cars/prototype/ …
 * Assumes:
 *   – posts are still in the top-level category “cars”
 *   – you already have the card partials and filter partials
 *   – taxonomy slug is “car_class”
 */

get_header();
$term      = get_queried_object();         // current car class (e.g. GT3)
$term_slug = $term->slug;
$term_name = $term->name;

/* --------------------------------------
 * Detect the parent “main category”.
 * For a car-class archive we force $main_cat_slug = cars
 * ------------------------------------ */
$main_cat_slug = 'cars';                          // adjust if you later reuse for tracks etc.
$main_cat_obj  = get_category_by_slug( $main_cat_slug );
?>

<main class="container">
  <!-- ====================  Page-header  ==================== -->
  <div class="page-header">

    <?php
    if ( $main_cat_slug === 'cars' ) {
        printf(
          '<h1 class="font-bold" style="font-size:28px;color:#fff">%s — %d mods ready to race!</h1>',
          esc_html( $term_name ),
          intval( $main_cat_obj->count )
        );
    }
    ?>

    <?php
      // Optionally echo the term (class) description
      $desc = term_description( $term->term_id, 'car_class' );
      if ( $desc ) {
        echo '<p>' . wp_kses_post( $desc ) . '</p>';
      }
    ?>
  </div>

  <!-- ====================  Toolbar  ==================== -->
  <div class="ir-toolbar flex items-center justify-between mb-0 mt-4">
    <!-- Search -->
    <div class="input-icon-wrapper relative">
      <form method="get" action="<?php echo esc_url( home_url() ); ?>" class="ir-search-form flex-1">
        <input type="hidden" name="car_class" value="<?php echo esc_attr( $term_slug ); ?>">
        <label for="mod-search" class="sr-only">
          <i class="fa-solid fa-magnifying-glass"></i> Search <?php echo esc_html( $term_name ); ?>
        </label>
        <input type="text" id="mod-search" placeholder=" Search mods"
               class="form-control input-with-search ir-search-input" style="width:300px" />
      </form>
    </div>

    <!-- Sort & Reset -->
    <div class="flex items-center gap-1">
      <label for="sort-select" class="text-md">Sort by:</label>
      <form class="ir-sort-form" onsubmit="return false;">
        <select name="sort" id="sort-select"
                class="bg-[#0d1117] text-black form-control ir-sort-select">
          <option value="latest">Newest First</option>
          <option value="oldest">Oldest First</option>
          <option value="views">Most Viewed</option>
          <option value="rating">Highest Rated</option>
          <option value="title_asc">Name ▲</option>
          <option value="title_desc">Name ▼</option>
        </select>
      </form>
      <button id="reset-filters" class="btn btn-sm btn-secondary items-center" title="Reset Filters">
        <i class="fa-solid fa-rotate-left text-xm hover:animate-spin"></i>
        <span style="font-size:17px">Reset</span>
      </button>
    </div>
  </div>

  <!-- ====================  Filter strip  ==================== -->
  <form id="mod-filter" class="flex flex-row md:flex-row flex-wrap gap-4">
  </form>

  <!-- ====================  Mod card grid  ==================== -->
  <section id="mod-list" class="section-sm py-4">
    <div class="container" style="padding:0">
      <div class="row gx-2 justify-around items-center">
        <div class="lg:col-12">
          <div class="row">
            <?php
            /* Main query already limited to current car_class by WP.
               If you need extra ordering, add pre_get_posts in functions.php */
            if ( have_posts() ) :
              while ( have_posts() ) : the_post(); ?>
                <div class="md:col-4">
                  <?php get_template_part( 'components/card' ); ?>
                </div>
            <?php endwhile; endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ====================  Pagination  ==================== -->
  <div id="pagination-wrapper"
       data-class="<?php echo esc_attr( $term_slug ); ?>"
       data-baseurl="<?php echo esc_url( get_term_link( $term ) ); ?>"
       class="mt-8 text-center">
       <?php
         the_posts_pagination( [
           'mid_size'  => 2,
           'prev_text' => '«',
           'next_text' => '»',
           'type'      => 'list',
         ] );
       ?>
  </div>
</main>

<?php get_footer(); ?>
