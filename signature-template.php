<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

function imza_escape(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Sol marka görseli yoksa, örnek veya banner dosyasından sol yarıyı kesip brand-left.png oluşturur.
 */
function imza_ensure_brand_left_asset(): ?string
{
    $dir = __DIR__ . '/assets';
    $out = $dir . '/brand-left.png';
    if (is_file($out) && filesize($out) > 0) {
        return 'brand-left.png';
    }

    $sources = ['signature-example.png', 'banner-template.png'];
    foreach ($sources as $name) {
        $path = $dir . '/' . $name;
        if (!is_file($path) || !extension_loaded('gd')) {
            continue;
        }
        $blob = @file_get_contents($path);
        if ($blob === false) {
            continue;
        }
        $im = @imagecreatefromstring($blob);
        if ($im === false) {
            continue;
        }
        $w = imagesx($im);
        $h = imagesy($im);
        $cw = (int) max(1, (int) round($w * 0.5));
        $dst = imagecreatetruecolor($cw, $h);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefill($dst, 0, 0, $transparent);
        imagealphablending($dst, true);
        imagecopy($dst, $im, 0, 0, 0, 0, $cw, $h);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagepng($dst, $out);
        imagedestroy($im);
        imagedestroy($dst);

        return 'brand-left.png';
    }

    return null;
}

/**
 * @param array{name?: string, title_en?: string, title_tr?: string, mobile?: string, office?: string} $data
 */
function render_signature(array $data): string
{
    $c = imza_get_config();
    $base = imza_base_url();

    $name = imza_escape(trim($data['name'] ?? ''));
    $titleEn = imza_escape(trim($data['title_en'] ?? ''));
    $titleTr = imza_escape(trim($data['title_tr'] ?? ''));
    $mobile = trim($data['mobile'] ?? '');
    if ($mobile !== '' && stripos($mobile, '(M)') === false) {
        $mobile .= ' (M)';
    }
    $mobileEsc = imza_escape($mobile);
    $officeEsc = imza_escape(trim($data['office'] ?? ''));

    $addr = imza_escape($c['static_address']);
    $webLabel = imza_escape($c['static_website_label']);
    $webHref = imza_escape($c['static_website_href']);

    $red = imza_escape($c['accent_red']);
    $dark = imza_escape($c['text_dark']);

    $asset = static function (string $file) use ($base): string {
        return imza_escape($base . 'assets/' . $file);
    };

    $brandFile = imza_ensure_brand_left_asset();
    $brandImg = '';
    if ($brandFile !== null) {
        $brandImg = '<img src="' . $asset($brandFile) . '" alt="DEDEM MEKATRONIK" width="280" style="display:block;border:0;max-width:280px;width:100%;height:auto;">';
    }

    $phoneLines = [];
    if ($mobileEsc !== '') {
        $phoneLines[] = $mobileEsc;
    }
    if ($officeEsc !== '') {
        $phoneLines[] = $officeEsc;
    }
    $phoneLineEsc = implode('<br>', $phoneLines);

    $fallbackBrand = '';
    if ($brandImg === '') {
        $fallbackBrand = '
<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
  <tr>
    <td style="font-family:Arial,Helvetica,sans-serif;font-size:9px;color:' . $dark . ';padding-bottom:6px;opacity:0.75;">
      <span style="font-weight:bold;">DECONS</span> &nbsp; <span style="font-weight:bold;">TEMCON</span> &nbsp; <span style="font-weight:bold;">inkol</span>
    </td>
  </tr>
  <tr>
    <td style="font-family:Arial,Helvetica,sans-serif;font-size:20px;line-height:1.1;font-weight:bold;color:' . $dark . ';">DEDEM<br><span style="font-size:14px;letter-spacing:0.12em;">MEKATRONIK</span></td>
  </tr>
  <tr>
    <td style="font-family:Georgia,\'Times New Roman\',serif;font-size:13px;font-style:italic;color:' . $dark . ';padding-top:4px;">We handle it!</td>
  </tr>
</table>';
    }

    $contactInner = '';
    $rowspan = 2;
    if ($phoneLineEsc !== '') {
        $rowspan = 3;
        $contactInner .= '
<tr>
  <td rowspan="' . $rowspan . '" bgcolor="' . $red . '" width="3" style="width:3px;background:' . $red . ';font-size:0;line-height:0;">&nbsp;</td>
  <td valign="top" style="font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:1.35;color:' . $red . ';padding:0 8px 0 6px;">&#128241;</td>
  <td valign="top" style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:' . $dark . ';line-height:1.45;padding-top:0;">' . $phoneLineEsc . '</td>
</tr>
<tr>
  <td valign="top" style="font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:1.35;color:' . $red . ';padding:6px 8px 0 6px;">&#128205;</td>
  <td valign="top" style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:' . $dark . ';line-height:1.35;padding-top:6px;">' . $addr . '</td>
</tr>
<tr>
  <td valign="top" style="font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:1.35;color:' . $red . ';padding:6px 8px 0 6px;">&#127760;</td>
  <td valign="top" style="font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.35;padding-top:6px;">
    <a href="' . $webHref . '" style="color:' . $dark . ';text-decoration:none;">' . $webLabel . '</a>
  </td>
</tr>';
    } else {
        $contactInner .= '
<tr>
  <td rowspan="' . $rowspan . '" bgcolor="' . $red . '" width="3" style="width:3px;background:' . $red . ';font-size:0;line-height:0;">&nbsp;</td>
  <td valign="top" style="font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:1.35;color:' . $red . ';padding:0 8px 0 6px;">&#128205;</td>
  <td valign="top" style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:' . $dark . ';line-height:1.35;">' . $addr . '</td>
</tr>
<tr>
  <td valign="top" style="font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:1.35;color:' . $red . ';padding:6px 8px 0 6px;">&#127760;</td>
  <td valign="top" style="font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.35;padding-top:6px;">
    <a href="' . $webHref . '" style="color:' . $dark . ';text-decoration:none;">' . $webLabel . '</a>
  </td>
</tr>';
    }

    $html = '
<!-- Outlook uyumlu — sol marka + sağ kişi / iletişim -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" style="border-collapse:collapse;max-width:600px;font-family:Arial,Helvetica,sans-serif;color:' . $dark . ';background:#ffffff;">
  <tr>
    <td width="280" style="width:280px;vertical-align:middle;padding:0 10px 0 0;">' . ($brandImg !== '' ? $brandImg : $fallbackBrand) . '</td>
    <td style="vertical-align:top;padding:4px 0 0 4px;">
      <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;">
        <tr>
          <td style="font-size:18px;font-weight:bold;color:' . $dark . ';line-height:1.2;padding-bottom:4px;">' . $name . '</td>
        </tr>
        <tr>
          <td style="font-size:12px;font-weight:normal;color:' . $dark . ';line-height:1.35;">' . $titleEn . '</td>
        </tr>
        <tr>
          <td style="font-size:12px;font-weight:normal;color:' . $dark . ';line-height:1.35;padding-bottom:8px;">' . $titleTr . '</td>
        </tr>
        <tr>
          <td style="padding:0 0 10px 0;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;">
              <tr>
                <td style="width:75%;height:2px;background:' . $red . ';font-size:0;line-height:0;">&nbsp;</td>
                <td style="font-size:0;line-height:0;">&nbsp;</td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td style="padding:0;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;">' . $contactInner . '
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>';

    return trim($html);
}
