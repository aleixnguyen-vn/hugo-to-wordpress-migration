<footer class="panel panel-default" style="margin-top: 2rem; padding: 1.5rem; margin-bottom: 0px;">
  <div class="container">
    <div class="hidden show-flex flex-row justify-between items-start"  style="flex-wrap: nowrap !important;">
      <div class="col-12 content-start md:col-4 ml-8 text-left md:text-left mb-6 md:mb-0 flex flex-col items-center md:items-start py-4">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-block mb-2" style="padding-top: 5px;">
          <img src="/wp-content/uploads/2025/06/logo-1.png" alt="Assetto Lab" style="height: 20px; width: auto;">
        </a>
        <p class="text-gray-400 max-w-sm">
          Elevate your driving experience.
        </p>
        <?php {{/*   Total views: <?php echo number_format(aslab_get_total_views()); ?>   */}}?>
      </div>
       
      <div class="col-12 md:col-4 text-left ml-2 mb-6 md:mb-0 py-4" style="padding-left: 8px">
          <h3 class="panel-title font-white mb-4">Useful Links</h3>
          <ul class="ml-8 space-y-2 list-item">
            <li><a href="/contact-us" class="hover:underline">Contact Us</a></li>
          <li><a href="/terms-of-service" class="hover:underline">Terms of Service</a></li>
          <li><a href="/privacy-policy" class="hover:underline">Privacy Policy</a></li>
          <li><a href="/cookie-policy" class="hover:underline">Cookie Policy</a></li>
        </ul>
      </div>

      <div class="col-12 md:col-4 text-left md:text-right px-4 py-4">
        <h4 class="text-base font-bold mb-2">Stay Updated</h4>
        <p class="text-gray-400 text-sm mb-3">Get notified when new mods drop.</p>
        <form action="#" method="post" class="flex flex-col md:items-end gap-2">
          <input type="email" name="email" placeholder="Your email"
            class="form-control">
          <button type="submit"
            class="btn btn-success">
            Subscribe
          </button>
        </form>
      </div>

    </div>
    <hr class="hidden show-block">

    <div class="text-center text-gray-600 mt-4 flex flex-row justify-center items-center">
      © <?php echo date('Y'); ?> Assetto Lab. Proudly powered by Litespeed Webserver <img src="/wp-content/uploads/assets/logo/litespeed.svg" alt="Proudly powered by Litespeed Webserver" class="pl-2 h-6">

    </div>
  </div>
  <?php wp_footer(); ?>
</footer>

</body>
</html>
