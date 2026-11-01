<?php

declare(strict_types=1);

/**
 * @return array{
 *   base_url: string,
 *   static_address: string,
 *   static_website_label: string,
 *   static_website_href: string,
 *   relocation_address: string,
 *   accent_red: string,
 *   text_dark: string,
 *   text_gray: string,
 *   banner_gray_bg: string
 * }
 */
function imza_get_config(): array
{
    return [
        'base_url' => '',
        'static_address' => 'OSB 4. Kısım, Keçiliköy OSB Mahallesi, İsmail Sarıgözoğlu Caddesi No:5, Yunusemre / Manisa',
        'static_website_label' => 'dedemmekatronik.com',
        'static_website_href' => 'https://dedemmekatronik.com',
        'relocation_address' => 'OSB 4. Kısım, Keçiliköy OSB Mahallesi, İsmail Sarıgözoğlu Caddesi No:5, Yunusemre / Manisa',
        'accent_red' => '#E30613',
        'text_dark' => '#231F20',
        'text_gray' => '#6D6E71',
        'banner_gray_bg' => '#4A4A4A',
    ];
}

/**
 * İmza HTML’inde kullanılacak taban URL (sonunda /).
 */
function imza_base_url(): string
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }

    $cfg = imza_get_config();
    $override = trim($cfg['base_url']);
    if ($override !== '') {
        $cached = rtrim($override, '/') . '/';

        return $cached;
    }

    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (string) $_SERVER['SERVER_PORT'] === '443');
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $dir = str_replace('\\', '/', dirname($script));
    $dir = $dir === '/' ? '' : $dir;

    $cached = ($https ? 'https://' : 'http://') . $host . $dir . '/';

    return $cached;
}
