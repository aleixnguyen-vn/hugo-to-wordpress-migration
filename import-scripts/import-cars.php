<?php
require __DIR__ . '/vendor/autoload.php';
require_once '/xampp/htdocs/wordpress/wp-load.php'; // sửa đúng đường dẫn WP

use Spatie\YamlFrontMatter\YamlFrontMatter;

date_default_timezone_set('UTC'); // fix timezone toàn cục

function convert_host($host) {
    return match (strtolower($host)) {
        'mods'      => 'Mods.To',
        'modsfire'  => 'ModsFire',
        'sharemods' => 'ShareMods',
        default     => $host,
    };
}

function parse_iso8601_date($date_raw) {
    echo "[DEBUG] Raw date: $date_raw\n";

    // Nếu là số nguyên (chuỗi số) => parse như timestamp
    if (ctype_digit($date_raw)) {
        $timestamp = (int)$date_raw;
        echo "[DEBUG] Detected UNIX timestamp: $timestamp\n";

        // Kiểm tra range hợp lệ
        if ($timestamp < 946684800 || $timestamp > 4102444800) { // từ 2000 đến 2100 unix timestamp
            echo "[DEBUG] Timestamp $timestamp out of range, reject\n";
            return false;
        }

        $dt = (new DateTime())->setTimestamp($timestamp)->setTimezone(new DateTimeZone('UTC'));
        echo "[DEBUG] Converted timestamp to datetime: " . $dt->format('Y-m-d H:i:s T') . "\n";

        return $dt->format('Y-m-d H:i:s');
    }

    // Nếu không phải số nguyên, thử parse như ISO8601
    $dt = DateTime::createFromFormat(DateTime::ATOM, $date_raw);
    if ($dt) {
        echo "[DEBUG] Parsed by ATOM format: " . $dt->format('Y-m-d H:i:s T') . "\n";
    } else {
        echo "[DEBUG] Failed ATOM parse, try generic DateTime\n";
        try {
            $dt = new DateTime($date_raw);
            echo "[DEBUG] Parsed by DateTime constructor: " . $dt->format('Y-m-d H:i:s T') . "\n";
        } catch (Exception $e) {
            echo "[DEBUG] Exception parsing date: " . $e->getMessage() . "\n";
            $dt = false;
        }
    }

    if (!$dt) {
        echo "[DEBUG] Parsing failed, return false\n";
        return false;
    }

    $dt->setTimezone(new DateTimeZone('UTC'));

    $year = (int)$dt->format('Y');
    if ($year < 2000 || $year > 2100) {
        echo "[DEBUG] Year $year out of range (2000-2100), reject\n";
        return false;
    }

    echo "[DEBUG] After timezone fix: " . $dt->format('Y-m-d H:i:s T') . "\n";

    return $dt->format('Y-m-d H:i:s');
}


function parse_mod_post($filepath) {
    $document = YamlFrontMatter::parseFile($filepath);
    $front = $document->matter();

    $mapped = [];

    $mapped['post_title'] = $front['title'] ?? '';

    $date_raw = $front['date'] ?? '';

    $post_date = parse_iso8601_date($date_raw);
    if (!$post_date) {
        $post_date = current_time('mysql');
    }
    $mapped['post_date'] = $post_date;

    $mapped['post_status'] = ($front['draft'] ?? false) ? 'draft' : 'publish';
    $mapped['post_category'] = [2]; // category mặc định

    $mapped['acf'] = [
        'csp_required' => !in_array(strtolower(trim($front['csp'] ?? '')), ['no', 'unknown']),
        'folder'      => $front['folder'] ?? '',
        'mod_name'    => $front['carname'] ?? '',
        'credit'      => $front['creator'] ?? '',
        'brand'       => $front['manu'] ?? '',
        'built_by'    => $front['logo2'] ?? '',
        'thumb'       => $front['thumb'] ?? '',
        'imgur_ids'   => implode(',', array_filter(array_merge(
            [$front['mainimage'] ?? ''],
            $front['cargallery'] ?? []
        ))),
        'nation'      => $front['country'] ?? '',
        'year'        => $front['year'] ?? '',
        'class'       => $front['class'] ?? '',
        'gearbox'     => $front['gearbox'] ?? '',
        'drivetrain'  => $front['drivetrain'] ?? '',
        'power'       => $front['power'] ?? '',
        'torque'      => $front['torque'] ?? '',
        'mass'        => $front['mass'] ?? '',
        'accel'       => $front['accel'] ?? '',
        'top_speed'   => $front['speed'] ?? '',
        'downloadlink'=> $front['link'] ?? '',
        'file_size'   => $front['zipsize'] ?? '',
        'download_host' => convert_host($front['host'] ?? ''),
        'version'     => $front['version'] ?? '',
        'dlc'         => $front['dlcrequired'] ?? '',
        'dlc_link'    => $front['dlclink'] ?? '',
    ];

    $mapped['tags'] = $front['tags'] ?? [];

    return $mapped;
}

function assign_taxonomies($post_id, $tags) {
    $allowed_classes = [
        'GT2', 'GT3', 'GT4', 'Hypercar', 'Sports cars', 'Sports coupe',
        'LMDh', 'LMH', 'LMP1', 'LMP2', 'LMP3', 'Formula'
    ];

    $class_terms = [];

    foreach ($tags as $tag) {
        if (!in_array($tag, $allowed_classes)) continue;

        switch ($tag) {
            case 'GT2':
            case 'GT3':
            case 'GT4':
                $class_terms[] = $tag;
                $class_terms[] = 'Track';
                break;
            case 'Hypercar':
            case 'Sports cars':
            case 'Sports coupe':
                $class_terms[] = $tag;
                break;
            case 'LMDh':
            case 'LMH':
                $class_terms[] = $tag;
                $class_terms[] = 'Prototype';
                break;
            case 'LMP1':
            case 'LMP2':
            case 'LMP3':
                $class_terms[] = 'Prototype';
                break;
            case 'Formula':
                $class_terms[] = 'Open Wheel';
                $class_terms[] = 'Track';
                break;
        }
    }

    $class_terms = array_unique($class_terms);

    if (!empty($class_terms)) {
        wp_set_post_terms($post_id, $class_terms, 'class');
    }
}

// =========================== //
//         MAIN SCRIPT         //
// =========================== //

$folder = 'C:/Users/jcw/Desktop/vx4love/aslab/content/cars/';
$files = glob($folder . '*.md');

echo "Đang import " . count($files) . " bài...\n";

foreach ($files as $mdfile) {
    $mapped = parse_mod_post($mdfile);
    $title = $mapped['post_title'];

    if (empty($title)) {
        echo "[SKIP] Không có title trong file: $mdfile\n";
        continue;
    }

    $existing = get_page_by_title($title, OBJECT, 'post');
    if ($existing) {
        $post_id = $existing->ID;
        wp_update_post([
            'ID' => $post_id,
            'post_date' => $mapped['post_date'],
            'post_status' => $mapped['post_status'],
            'post_category' => $mapped['post_category'],
        ]);
        echo "[UPDATE] $title (ID: $post_id)\n";
    } else {
        $post_id = wp_insert_post([
            'post_title'    => $title,
            'post_date'     => $mapped['post_date'],
            'post_status'   => $mapped['post_status'],
            'post_category' => $mapped['post_category'],
        ]);

        if (is_wp_error($post_id)) {
            echo "[ERROR] Không insert được: $title\n";
            continue;
        }

        echo "[INSERT] $title (ID: $post_id)\n";
    }

    foreach ($mapped['acf'] as $key => $value) {
        update_field($key, $value, $post_id);
    }

    assign_taxonomies($post_id, $mapped['tags']);
}

echo "Import hoàn tất.\n";
