<?php
require __DIR__ . '/vendor/autoload.php';
require_once '/xampp/htdocs/wordpress/wp-load.php'; // chỉnh đường dẫn WP đúng môi trường

use Spatie\YamlFrontMatter\YamlFrontMatter;
use League\CommonMark\CommonMarkConverter;

/**
 * =============================================
 *  TRACK IMPORTER – Hugo ➜ Press (WordPress)
 *  Giữ nguyên: title, date, content + ACF fields:
 *    - mod_name
 *    - credit
 *    - credit_link
 *    - thumb
 *    - imgur_ids
 *    - downloadlink
 *    - file_size
 *    - download_host
 *    - version
 *  Các trường khác loại bỏ.
 * =============================================
 */

date_default_timezone_set('UTC');

function convert_host($host)
{
    return match (strtolower($host)) {
        'mods'      => 'Mods.To',
        'modsfire'  => 'ModsFire',
        'sharemods' => 'ShareMods',
        default     => $host,
    };
}

/**
 * Chuẩn hoá ngày tháng (front‑matter) về dạng 'Y-m-d H:i:s'.
 */
function parse_iso8601_date($date_raw)
{
    if (ctype_digit($date_raw)) {
        $timestamp = (int)$date_raw;
        // Hạn chế timestamp vô lý
        if ($timestamp < 946684800 || $timestamp > 4102444800) {
            return false;
        }
        return (new DateTime())->setTimestamp($timestamp)->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s');
    }

    $dt = DateTime::createFromFormat(DateTime::ATOM, $date_raw) ?: false;
    if (!$dt) {
        try {
            $dt = new DateTime($date_raw);
        } catch (Exception $e) {
            return false;
        }
    }

    if (!$dt) {
        return false;
    }

    $dt->setTimezone(new DateTimeZone('UTC'));
    $year = (int)$dt->format('Y');
    if ($year < 2000 || $year > 2100) {
        return false;
    }

    return $dt->format('Y-m-d H:i:s');
}

/**
 * Đọc file Markdown, map sang mảng WP Post.
 */
function parse_track_post($filepath)
{
    $document = YamlFrontMatter::parseFile($filepath);
    $front    = $document->matter();

    $mapped                = [];
    $mapped['post_title']  = $front['title'] ?? '';
    $date_raw              = $front['date'] ?? '';
    $mapped['post_date']   = parse_iso8601_date($date_raw) ?: current_time('mysql');
    $mapped['post_status'] = ($front['draft'] ?? false) ? 'draft' : 'publish';
    $mapped['post_category'] = [7]; // ID category "tracks"
    $mapped['post_author'] = 1;     // admin
    $mapped['post_content'] = $document->body();

    // ===== ACF fields giữ lại =====
    $credit_arr   = array_filter([
        $front['trackcreator']  ?? '',
        $front['trackcreator2'] ?? '',
    ]);
    $credit       = implode(', ', $credit_arr);
    $credit_link  = $front['credit_link'] ?? ($front['creatorlink'] ?? '');

    $mapped['acf'] = [
        'mod_name'      => $front['name'] ?? '',
        'credit'        => $front['creator'] ?? '',
        'credit_link'   => $front['link'] ?? '',
        'thumb'         => $front['thumb'] ?? '',
        'imgur_ids'     => implode(',', array_filter(array_merge([
            $front['mainimage'] ?? ''
        ], $front['trackgallery'] ?? []))) ?? $front['thumb'],
        'downloadlink'  => $front['link'] ?? '',
        'file_size'     => $front['zipsize'] ?? '',
        'download_host' => convert_host($front['host'] ?? ''),
        'version'       => $front['version'] ?? '',
    ];

    return $mapped;
}

// =========================== //
//         MAIN SCRIPT         //
// =========================== //

$folder = 'C:/Users/jcw/Desktop/vx4love/aslab/content/misc/';
$files  = glob($folder . '*.md');

echo "Đang import " . count($files) . " track...\n";

foreach ($files as $mdfile) {
    $mapped = parse_track_post($mdfile);
    $title  = $mapped['post_title'];

    if (empty($title)) {
        echo "[SKIP] Không có title trong file: $mdfile\n";
        continue;
    }

    $existing = get_page_by_title($title, OBJECT, 'post');

    if ($existing) {
        $post_id = $existing->ID;
        wp_update_post([
            'ID'            => $post_id,
            'post_content'  => $mapped['post_content'],
            'post_date'     => $mapped['post_date'],
            'post_status'   => $mapped['post_status'],
            'post_category' => $mapped['post_category'],
        ]);
        echo "[UPDATE] $title (ID: $post_id)\n";
    } else {
        $post_id = wp_insert_post([
            'post_title'    => $title,
            'post_content'  => $mapped['post_content'],
            'post_date'     => $mapped['post_date'],
            'post_status'   => $mapped['post_status'],
            'post_category' => $mapped['post_category'],
            'post_author'   => $mapped['post_author'],
        ]);

        if (is_wp_error($post_id)) {
            echo "[ERROR] Không insert được: $title\n";
            continue;
        }

        echo "[INSERT] $title (ID: $post_id)\n";
    }

    // Cập nhật ACF
    foreach ($mapped['acf'] as $key => $value) {
        update_field($key, $value, $post_id);
    }
}

echo "Import track hoàn tất.\n";
