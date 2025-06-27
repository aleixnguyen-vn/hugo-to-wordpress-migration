<?php
/**
 * Component: Car Filter Form
 * Called in archive or index when category == 'cars'
 */
?>


  <?php
  /* ---------- 1. Các field discrete (brand, nation, …) ---------- */
  $fields = [
    'brand'      => 'Manufacturer',
    'nation'     => 'Nationality',
    'class'      => 'Class',
    'drivetrain' => 'Drivetrain',
    'gearbox'    => 'Gearbox',
  ];


  foreach ($fields as $field => $label) {
      $options = aslab_get_acf_unique_values($field);
      ?>
      <div class="ir-sort-form">
        <label class="block mb-1 font-semibold"><?php echo esc_html($label); ?></label>
        <select name="<?php echo esc_attr($field); ?>" class="form-control border px-2 py-1">
          <option value="">All</option>
          <?php foreach ($options as $val): ?>
            <option value="<?php echo esc_attr($val); ?>"><?php echo esc_html($val); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
  <?php } ?>

  <?php
  /* ---------- 2. Các field range (year, power, top_speed) ---------- */

  // Year – step 10
  $year_opts = aslab_get_range_options('year', 10, '', false); // đóng kín mỗi thập kỷ
  ?>
  <div class="ir-sort-form">
    <label class="block mb-1 font-semibold">Year</label>
    <select name="year_range" class="form-control border px-2 py-1">
      <option value="">All</option>
      <?php foreach ($year_opts as [$val, $lab]): ?>
        <option value="<?php echo esc_attr($val); ?>"><?php echo esc_html($lab); ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <?php
  // Power – step 100 bhp
  $power_opts = aslab_get_range_options('power', 100, ' bhp');
  ?>
  <div class="ir-sort-form">
    <label class="block mb-1 font-semibold">Power (bhp)</label>
    <select name="power_range" class="form-control border px-2 py-1">
      <option value="">All</option>
      <?php foreach ($power_opts as [$val, $lab]): ?>
        <option value="<?php echo esc_attr($val); ?>"><?php echo esc_html($lab); ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <?php
  // Top speed – step 50 km/h
  $speed_opts = aslab_get_range_options('top_speed', 50, ' km/h');
  ?>
  <div class="ir-sort-form">
    <label class="block mb-1 font-semibold">Top speed (km/h)</label>
    <select name="speed_range" class="form-control border px-2 py-1">
      <option value="">All</option>
      <?php foreach ($speed_opts as [$val, $lab]): ?>
        <option value="<?php echo esc_attr($val); ?>"><?php echo esc_html($lab); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
