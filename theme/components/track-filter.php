<?php
// components/filter-tracks.php
$dropdown_fields = [
    'location'   => 'Country',
    'track_type' => 'Track Type',
];

$range_fields = [
    'year'     => ['label' => 'Year',      'step' => 20],
    'fia_rank' => ['label' => 'FIA Grade', 'step' => 2],
    'length'   => ['label' => 'Length (km)',  'step' => 5],
    'width'    => ['label' => 'Width (m)',    'step' => 10],
    'pitbox'   => ['label' => 'Pitboxes',     'step' => 10],
];

/**
 * Lấy danh sách giá trị duy nhất cho dropdown, tự xử lý
 * meta kiểu scalar và cả meta đã serialize (checkbox / multiple select).
 */
function aslab_get_unique_dropdown_values( string $field_name ): array {
    global $wpdb;

    $raw = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT DISTINCT meta_value
             FROM {$wpdb->postmeta}
             WHERE meta_key = %s AND meta_value <> ''",
            $field_name
        )
    );

    $values = [];
    foreach ( $raw as $val ) {
        // Nếu là serialized array → bung ra thành từng phần tử
        if ( is_serialized( $val ) ) {
            $un = maybe_unserialize( $val );
            if ( is_array( $un ) ) {
                $values = array_merge( $values, $un );
            }
        } else {
            $values[] = $val;
        }
    }

    // Làm sạch
    $values = array_unique( array_filter( array_map( 'trim', $values ) ) );
    sort( $values, SORT_NATURAL | SORT_FLAG_CASE );

    return $values;
}
?>

<?php /* ---------- DROPDOWNS ---------- */ ?>
<?php foreach ( $dropdown_fields as $field => $label ) : ?>
    <?php $options = aslab_get_unique_dropdown_values( $field ); ?>
    <div class="ir-sort-form">
        <label><?php echo esc_html( $label ); ?></label>
        <select name="<?php echo esc_attr( $field ); ?>" class="form-control">
            <option value="">All</option>
            <?php foreach ( $options as $val ) : ?>
                <option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $val ); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
<?php endforeach; ?>

<?php /* ---------- RANGE FIELDS ---------- */ ?>
<?php foreach ( $range_fields as $field => $info ) : ?>
    <?php
    [ $min, $max ] = aslab_get_acf_min_max( $field );

    // Nếu không có dữ liệu hợp lệ → bỏ qua field
    if ( ! is_numeric( $min ) || ! is_numeric( $max ) || $min == $max ) {
        continue;
    }

    $step = max( 1, (int) $info['step'] );
    $min  = floor( $min / $step ) * $step;
    $max  = ceil(  $max / $step ) * $step;

    $options = [];
    for ( $i = $min; $i <= $max; $i += $step ) {
        $to        = $i + $step - 1;
        $options[] = "{$i}-{$to}";
    }
    sort($options, SORT_NATURAL | SORT_FLAG_CASE);

    ?>
    <div class="ir-sort-form">
        <label><?php echo esc_html( $info['label'] ); ?></label>
        <select name="<?php echo esc_attr( "{$field}_range" ); ?>" class="form-control">
            <option value="">All</option>
            <?php foreach ( $options as $range ) : ?>
                <option value="<?php echo esc_attr( $range ); ?>"><?php echo esc_html( $range ); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
<?php endforeach; ?>
