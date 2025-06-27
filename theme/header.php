<!DOCTYPE html>
<html <?php language_attributes(); ?>>
  <head>
    <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "Assetto Lab - Free Assetto Corsa Mods",
  "url": "<?php echo home_url(); ?>",
  "potentialAction": {
    "@type": "SearchAction",
    "target": "<?php echo home_url(); ?>/?s={search_term_string}",
    "query-input": "required name=search_term_string"
  }
}
</script>

    <meta charset="
			<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1"> <?php wp_head(); ?>
  </head>
  <body <?php body_class(); ?> data-post-id="<?php the_ID(); ?>">
    <header>
      <style>
        @media (max-width: 767px) {
      .main-nav {
      display: none !important;
        }
  }
      </style>
      <div id="main-nav-container">
        <div class="panel panel-default NoMarginBottom PositionRelative NoBorder">
          <div class="container mx-auto px-4 py-4 flex items-center justify-between">
            <a 
            href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center space-x-2">
              <img 
              src="/wordpress/wp-content/uploads/2025/06/logo-1.png" alt="Assetto Lab - Free Assetto Corsa Mods" style="max-height: 40px; width: auto;">
            </a>
 
          <nav class="main-nav" id="main-nav">
                       <button id="nav-toggle" class="lg:hidden text-white text-2xl focus:outline-none">
          ☰
          </button>
          <?php
              wp_nav_menu([
              'theme_location' => 'main_menu',
              'menu_class' => 'menu text-white',
              'container_class' => 'main-nav',
              'container' => 'nav'
              ]);
            ?>
          </nav>

            <a href="/submit-mod" class="btn btn-success bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-semibold"> Submit Mod </a>
          </div>
          
    
      </div>
    </header>
    <main>