<?php get_header(); ?>

<section class="section-sm text-center">
  <div class="container">
    <div class="row justify-center" style="padding-bottom: 6rem">
      <div class="sm:col-10 md:col-8 lg:col-6" style="padding-bottom:3rem; padding-top:2rem">
        <span class="text-[8rem] block font-bold font-white">
          404
        </span>
        <h1 class="h2 mb-4" style="font-size:42px">Page not found</h1>
        <div class="content">
          <p>The resource requested could not be found on this server!</p>
        </div>
        
        <a href="<?php echo home_url(); ?>"
           class="inline-block btn btn-default mt-4" style="font-size: 15px">
          <i class="fa fa-backward mr-2"></i> Return to pitbox
        </a>
      </div>
    </div>
  </div>
</section>

<?php get_footer(); ?>
