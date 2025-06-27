<?php

// ====== Setup cơ bản ======
function aslab_theme_setup() {
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'aslab_theme_setup');

// ====== Load CSS (tailwind hoặc custom css) ======
function aslab_enqueue_styles() {
  wp_enqueue_style('aslab-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'aslab_enqueue_styles');

// ====== Đăng ký menu ======
function aslab_register_menus() {
  register_nav_menus([
    'main_menu' => 'Main Menu',
  ]);
}
add_action('after_setup_theme', 'aslab_register_menus');

// ====== Enqueue JS rating + truyền ajaxurl ======
function aslab_enqueue_scripts() {
  if ( is_single() ) {
    wp_enqueue_script(
      'aslab-rating',
      get_template_directory_uri() . '/js/rating.js',
      [],
      null,
      true
    );

    wp_localize_script('aslab-rating', 'aslab_ajax', [
      'ajaxurl' => admin_url('admin-ajax.php')
    ]);
  }
}
add_action('wp_enqueue_scripts', 'aslab_enqueue_scripts');

// ====== AJAX Star Rating ======
add_action('wp_ajax_aslab_rate_post', 'aslab_handle_rating');
add_action('wp_ajax_nopriv_aslab_rate_post', 'aslab_handle_rating');

function aslab_handle_rating() {
  $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
  $rating  = isset($_POST['rating']) ? intval($_POST['rating']) : 0;

  if (!$post_id || $rating < 1 || $rating > 5) {
    wp_send_json_error(['message' => 'Invalid input'], 400);
  }

  $total = (int) get_post_meta($post_id, 'rating_total', true);
  $count = (int) get_post_meta($post_id, 'rating_count', true);

  $total += $rating;
  $count += 1;

  update_post_meta($post_id, 'rating_total', $total);
  update_post_meta($post_id, 'rating_count', $count);

  $avg = $total / $count;

  update_post_meta($post_id, 'rating_avg', round($avg, 2));

  wp_send_json_success([
    'avg' => round($avg, 2),
    'count' => $count
  ]);
}

// Font Awesome

function aslab_enqueue_fontawesome() {
  wp_enqueue_style(
    'font-awesome',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css',
    [],
    '6.5.0'
  );
}
add_action('wp_enqueue_scripts', 'aslab_enqueue_fontawesome');

function urlize($text) {
  $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text); // Loại bỏ ký tự Unicode
  $text = strtolower($text);
  $text = preg_replace('/[^a-z0-9]+/', '-', $text);
  return trim($text, '-');
}

function aslab_meta() {
  if (is_single()) {
    $desc = get_the_excerpt() ?: wp_trim_words(strip_tags(get_the_content()), 30);
    echo '<meta name="description" content="' . esc_attr($desc) . '">' . "\n";
  }
}
add_action('wp_head', 'aslab_meta');

function aslab_add_schema_for_mod() {
  if (!is_singular()) return;

  $mod_name   = get_field('mod_name') ?: get_the_title();
  $brand      = get_field('brand');
  $version    = get_field('version');
  $rating_total = get_post_meta(get_the_ID(), 'rating_total', true) ?: 0;
  $rating_count = get_post_meta(get_the_ID(), 'rating_count', true) ?: 0;
  $avg_rating = $rating_count > 0 ? round($rating_total / $rating_count, 2) : null;

  $schema = [
    '@context' => 'https://schema.org',
    '@type'    => 'SoftwareApplication',
    'name'     => $mod_name,
    'operatingSystem' => 'Windows',
    'applicationCategory' => 'GameMod',
    'offers' => [
      '@type' => 'Offer',
      'price' => '0',
      'priceCurrency' => 'USD'
    ],
    'publisher' => [
      '@type' => 'Organization',
      'name' => $brand
    ],
    'datePublished' => get_the_date('c'),
    'dateModified'  => get_the_modified_date('c'),
  ];

  if ($avg_rating) {
    $schema['aggregateRating'] = [
      '@type'       => 'AggregateRating',
      'ratingValue' => $avg_rating,
      'reviewCount' => $rating_count
    ];
  }

  echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
}
add_action('wp_head', 'aslab_add_schema_for_mod');


// AJAX pagination, sort, search live
add_action('wp_ajax_nopriv_ajax_mod_pagination', 'ajax_mod_pagination');
add_action('wp_ajax_ajax_mod_pagination', 'ajax_mod_pagination');

function ajax_mod_pagination() {

  $posts_per_page_map = [
  'cars'    => 27,   // 3 cards / hàng × 3 hàng
  'tracks'  => 32,  // 4 cards / hàng × 3 hàng
  ''        => 32,  // ALL  – cho thoải mái
  'default' => 32,  // mọi giá trị lạ
];

  $search   = isset($_POST['s']) ? sanitize_text_field($_POST['s']) : '';
  $paged    = isset($_POST['page']) ? intval($_POST['page']) : 1;
  $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
  $sort     = isset($_POST['sort']) ? sanitize_text_field($_POST['sort']) : '';
  $posts_per_page = $posts_per_page_map[$category] ?? $posts_per_page_map['default'];


  $args = [
    'post_type'      => 'post',
    'posts_per_page' => $posts_per_page,
    'paged'          => $paged,
  ];

  switch ($sort) {
    case 'oldest':
      $args['orderby'] = 'date';
      $args['order']   = 'ASC';
      break;

    case 'title_asc':
      $args['orderby'] = 'title';
      $args['order']   = 'ASC';
      break;

    case 'title_desc':
      $args['orderby'] = 'title';
      $args['order']   = 'DESC';
      break;

    case 'views':
      $args['meta_key'] = '_pvc_views';
      $args['orderby']  = 'meta_value_num';
      $args['order']    = 'DESC';
      break;

    case 'rating':
      $args['meta_key'] = 'rating_avg';
      $args['orderby']  = 'meta_value_num';
      $args['order']    = 'DESC';
      break;

    default:
      $args['orderby'] = 'date';
      $args['order']   = 'DESC';
      break;
  }

  if ($search) {
    $args['s'] = $search;
  }

  if ($category) {
    $args['category_name'] = $category;
  }

  // Build meta_query
  $category = sanitize_text_field($_POST['category'] ?? '');
$meta_query = ['relation' => 'AND'];

$filter_fields = [];

if ($category === 'cars') {
  $filter_fields = ['brand','nation','class','drivetrain','gearbox'];
} elseif ($category === 'tracks') {
  $filter_fields = ['location','track_type'];
}

foreach ($filter_fields as $key) {
  if (!empty($_POST[$key])) {
    $compare = ($key === 'track_type') ? 'LIKE' : '=';
    $meta_query[] = [
      'key'     => $key,
      'value'   => sanitize_text_field($_POST[$key]),
      'compare' => $compare,
    ];
  }
}


// Shared range filters
$range_map = [
  'cars' => [
    'year'      => 'year_range',
    'power'     => 'power_range',
    'top_speed' => 'speed_range',
  ],
  'tracks' => [
    'year'     => 'year_range',
    'fia_rank' => 'fia_rank_range',
    'length'   => 'length_range',
    'width'    => 'width_range',
    'pitbox'   => 'pitbox_range',
  ]
];

if (isset($range_map[$category])) {
  foreach ($range_map[$category] as $meta_key => $param) {
    if (!empty($_POST[$param]) && strpos($_POST[$param], '-') !== false) {
      [$min, $max] = array_map('intval', explode('-', $_POST[$param], 2));
      $meta_query[] = [
        'key'     => $meta_key,
        'value'   => ($max >= 999999) ? $min : [$min, $max],
        'type'    => 'NUMERIC',
        'compare' => ($max >= 999999) ? '>=' : 'BETWEEN',
      ];
    }
  }
}

$args['meta_query'] = $meta_query;


$query = new WP_Query($args);

  ob_start();
  ?>
  <section id="mod-list" class="section-sm py-4">
    <div class="container" style="padding: 0px">
      <div class="row gx-2 justify-around items-center">
        <div class="lg:col-12">
          <div class="row">
            <?php if ($query->have_posts()) :
              while ($query->have_posts()) : $query->the_post(); ?>
              <div class="<?php echo $category === 'tracks' ? 'md:col-3' : 'md:col-4'; ?>">
                <?php get_template_part('components/card'); ?>
              </div>
              <?php endwhile; ?>
            <?php else : ?>
              <div class="col-12 text-center py-6 text-gray-400 italic">
                No results found<?php if (!empty($search)) echo ' for "<strong>' . esc_html($search) . '</strong>"'; ?>.
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>
  <?php
  $cards_html = ob_get_clean();
  wp_reset_postdata();

  $base_url = home_url('/') . $category . '/';
  $pagination_links = paginate_links([
    'base'      => trailingslashit($base_url) . 'page/%#%/',
    'format'    => '',
    'current'   => $paged,
    'total'     => $query->max_num_pages,
    'prev_text' => '«',
    'next_text' => '»',
    'type'      => 'array',
  ]);

  $pagination_html  = '<div id="pagination-wrapper" class="mt-8 text-center"';
  $pagination_html .= ' data-category="' . esc_attr($category) . '"';
  $pagination_html .= ' data-baseurl="' . esc_url($base_url) . '">';

  if ($pagination_links) {
    $pagination_html .= '<ul class="inline-flex items-center justify-center space-x-2 text-sm">';
    foreach ($pagination_links as $link) {
      if (strpos($link, 'href') !== false && strpos($link, 'page/1/') !== false && $paged == 1) {
        continue;
      }
      if (strpos($link, 'href') !== false && strpos($link, 'page/1/') !== false) {
        $link = str_replace('page/1/', '', $link);
      }
      if (strpos($link, 'current') !== false) {
        $link = str_replace('page-numbers current', 'btn btn-sm btn-success', $link);
      } elseif (strpos($link, 'dots') !== false) {
        $link = str_replace('page-numbers dots', 'pagi', $link);
      } else {
        $link = str_replace('page-numbers', 'btn btn-sm btn-info', $link);
      }
      $pagination_html .= '<li style="min-width:0!important">' . $link . '</li>';
    }
    $pagination_html .= '</ul>';
  }
  $pagination_html .= '</div>';

  wp_send_json_success([
    'cards'      => $cards_html,
    'pagination' => $pagination_html,
  ]);
}



add_action('wp_enqueue_scripts', function () {
  wp_enqueue_script('ajax-pagination', get_template_directory_uri() . '/js/ajax-pagination.js', [], null, true);
  wp_localize_script('ajax-pagination', 'ajaxpagination', [
    'ajaxurl' => admin_url('admin-ajax.php'),
  ]);
});


function aslab_fix_missing_rating_avg() {
  $posts = get_posts([
    'post_type' => 'post',
    'posts_per_page' => -1,
  ]);

  foreach ($posts as $post) {
    if (!metadata_exists('post', $post->ID, 'rating_avg')) {
      update_post_meta($post->ID, 'rating_avg', 0);
    }
  }
}


function aslab_get_acf_unique_values($field_name) {
  global $wpdb;
  $raw_values = $wpdb->get_col(
    $wpdb->prepare(
      "SELECT DISTINCT meta_value FROM {$wpdb->postmeta}
       WHERE meta_key = %s AND meta_value != ''",
      $field_name
    )
  );

  $values = [];

  foreach ($raw_values as $value) {
    // Nếu là track_type kiểu text area nhiều giá trị
    if ($field_name === 'track_type') {
      $parts = array_map('trim', explode(',', $value));
      $values = array_merge($values, $parts);
    } else {
      $values[] = trim($value);
    }
  }

  $values = array_filter($values, function ($v) {
    return $v !== '' && strtolower($v) !== 'unknown' && strtolower($v) !== 'null';
  });

  return array_unique($values);
}


function aslab_get_acf_min_max($field_name) {
  global $wpdb;
  $min = $wpdb->get_var(
    $wpdb->prepare(
      "SELECT MIN(CAST(meta_value AS UNSIGNED)) FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value != ''",
      $field_name
    )
  );
  $max = $wpdb->get_var(
    $wpdb->prepare(
      "SELECT MAX(CAST(meta_value AS UNSIGNED)) FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value != ''",
      $field_name
    )
  );
  return [$min ?: 0, $max ?: 100];
}

/**
 * Trả về mảng các option [value, label] cho dropdown range.
 *
 * @param string  $field      meta_key ACF
 * @param int     $step       kích thước mỗi khoảng
 * @param string  $unit       hậu tố hiển thị (vd ' bhp', ' km/h', '')
 * @param boolean $open_last  true => khoảng cuối mở ('400-9999'), false => khép kín
 * @return array  mỗi phần tử là [ '0-99', '0–99 bhp' ]
 */
function aslab_get_range_options($field, $step = 100, $unit = '', $open_last = true) {

    [$min, $max] = aslab_get_acf_min_max($field);       // lấy min-max (ép numeric)
    if ($min === null || $max === null) {               // không có data
        return [];
    }

    $start = floor($min / $step) * $step;               // chốt mốc đầu
    $end   = ceil(($max + 1) / $step) * $step - 1;      // chốt mốc cuối

    $out = [];
    for ($cur = $start; $cur <= $end; $cur += $step) {
        $next = $cur + $step - 1;
        // Nếu đến khúc cuối và muốn mở khoảng vô cực
        if ($open_last && $next >= $max) {
            $out[] = [ "{$cur}-999999", "{$cur}+{$unit}" ];
            break;
        }
        $label = "{$cur}–{$next}{$unit}";
        $out[] = [ "{$cur}-{$next}", $label ];
    }
    return $out;
}


// Rewrite single post url

// Rewrite rules cho từng loại category
function aslab_custom_rewrite_for_mods() {
  $map = [
    'cars'   => 'car',
    'tracks' => 'track',
    'skins'  => 'skin',
    'others' => 'other',
  ];

  foreach ($map as $cat => $slug) {
    add_rewrite_rule("^$slug/([^/]+)/?", 'index.php?name=$matches[1]', 'top');
  }
}
add_action('init', 'aslab_custom_rewrite_for_mods');

// Tùy chỉnh permalink cho bài post thuộc các category trên
function aslab_custom_permalink_by_category($permalink, $post) {
  if ($post->post_type !== 'post') return $permalink;

  $map = [
    'cars'   => 'car',
    'tracks' => 'track',
    'skins'  => 'skin',
    'others' => 'other',
  ];

  $cats = wp_get_post_categories($post->ID, ['fields' => 'slugs']);
  foreach ($map as $cat => $slug) {
    if (in_array($cat, $cats)) {
      return home_url("/$slug/" . $post->post_name);
    }
  }

  return $permalink;
}
add_filter('post_link', 'aslab_custom_permalink_by_category', 10, 2);

//Related post

function get_related_mods( $limit = 9 ) {
    $post_id = get_the_ID();

    // Lấy ACF (xài trim cho chắc, đỡ dính ký tự thừa)
    $brand  = trim( (string) get_field( 'brand'  ) );
    $class  = trim( (string) get_field( 'class'  ) );
    $nation = trim( (string) get_field( 'nation' ) );

    // Taxonomy (category) hiện tại
    $cat_ids = wp_list_pluck( get_the_category(), 'term_id' );

    // Danh sách ID đã chọn để loại trừ trùng lặp
    $exclude_ids   = [ $post_id ];
    $collected_ids = [];

    // Cấu hình cơ bản cho mọi level
    $base_args = [
        'post_type'      => 'post',
        'orderby'        => 'rand',          // Random cho đa dạng
        'no_found_rows'  => true,            // Tăng tốc
        'post_status'    => 'publish',
        'category__in'   => $cat_ids,
    ];

    // Xây meta_query từng level – CHỈ thêm điều kiện khi field có giá trị
  $levels = [
      // 1. Cùng class (ưu tiên hàng đầu)
      $class ? [ [ 'key' => 'class', 'value' => $class, 'compare' => '=' ] ] : [],

      // 2. Cùng class + nation (có thể dùng cho chủ đề race)
      array_filter([
          'relation' => 'AND',
          $class ? [ 'key' => 'class', 'value' => $class, 'compare' => '=' ] : null,
          $nation ? [ 'key' => 'nation', 'value' => $nation, 'compare' => '=' ] : null,
      ]),

      // 3. Cùng class + brand (ví dụ người thích dòng GT3 của 1 hãng)
      array_filter([
          'relation' => 'AND',
          $class ? [ 'key' => 'class', 'value' => $class, 'compare' => '=' ] : null,
          $brand ? [ 'key' => 'brand', 'value' => $brand, 'compare' => '=' ] : null,
      ]),

      // 4. Cùng brand (đề phòng thiếu mod cùng class)
      $brand ? [ [ 'key' => 'brand', 'value' => $brand, 'compare' => '=' ] ] : [],

      // 5. Fallback: cùng category
      [],
  ];

      
      // Quét từng level, lấp đầy dần
    foreach ( $levels as $meta_query ) {
        // Nếu đủ rồi thì thoát sớm
        if ( count( $collected_ids ) >= $limit ) {
            break;
        }

        $args = $base_args;
        $args['posts_per_page'] = $limit - count( $collected_ids );
        $args['post__not_in']   = $exclude_ids;

        if ( ! empty( $meta_query ) ) {
            $args['meta_query'] = $meta_query;
        }

        $q = new WP_Query( $args );

        if ( $q->have_posts() ) {
            foreach ( $q->posts as $p ) {
                $collected_ids[] = $p->ID;
                $exclude_ids[]   = $p->ID;   // Cập nhật để loại trừ ở level sau
            }
        }
    }

    // Không kiếm được gì
    if ( empty( $collected_ids ) ) {
        return null;
    }

    // Trả về WP_Query cuối cùng (giữ đúng thứ tự đã random)
    return new WP_Query( [
        'post_type'      => 'post',
        'post__in'       => $collected_ids,
        'orderby'        => 'post__in',
        'posts_per_page' => count( $collected_ids ),
        'no_found_rows'  => true,
    ] );
}




add_action('pre_get_posts', function($query) {
  if (is_admin() || !$query->is_main_query()) return;

  // Áp dụng trên archive cars, category cars hoặc page cụ thể
  if (is_post_type_archive('cars') || is_category('cars') || is_page('popular-mods')) {
    $meta_query = [];

    // Danh sách field ACF cần filter
    $acf_fields = [
      'brand',
      'nation',
      'year',
      'class',
      'drivetrain',
      'gearbox',
      'mass',
      'power',
      'top_speed'
    ];

    foreach ($acf_fields as $field) {
      if (!empty($_GET[$field])) {
        // Nếu là dạng slider (min-max), ví dụ ?power[min]=200&power[max]=400
        if (is_array($_GET[$field]) && isset($_GET[$field]['min'], $_GET[$field]['max'])) {
          $meta_query[] = [
            'key'     => $field,
            'value'   => [ $_GET[$field]['min'], $_GET[$field]['max'] ],
            'type'    => 'NUMERIC',
            'compare' => 'BETWEEN',
          ];
        } else {
          $meta_query[] = [
            'key'     => $field,
            'value'   => $_GET[$field],
            'compare' => '=',
          ];
        }
      }
    }

    if (!empty($meta_query)) {
      $query->set('meta_query', $meta_query);
    }

    // Optional: sort by view count (PVC)
    if (isset($_GET['orderby']) && $_GET['orderby'] === 'views') {
      $query->set('meta_key', '_pvc_views');
      $query->set('orderby', 'meta_value_num');
      $query->set('order', 'DESC');
    }
  }
});

//Fix missing gearbox field for posts

$args = [
    'post_type'      => 'post',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
];

$query = new WP_Query($args);

if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();

        $gearbox = get_field('credit', $post_id);

        if (empty($gearbox)) {
            update_field('credit', 'Unknown', $post_id);
        }
    }
    wp_reset_postdata();
}

add_action('pre_get_posts', function ( $query ) {
    if ( is_admin() || ! $query->is_main_query() || ! $query->is_category() ) {
        return;
    }

    if ( is_category('cars') ) {
        $query->set('posts_per_page', 21);
    } else {
        $query->set('posts_per_page', 32);
    }
});

// functions.php
remove_action('wp_head', 'wp_generator'); // ẩn version
add_filter('the_generator', '__return_empty_string');
