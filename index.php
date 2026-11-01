<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/signature-template.php';

$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';

$form = [
    'name' => $isPost ? (string) ($_POST['name'] ?? '') : '',
    'title_en' => $isPost ? (string) ($_POST['title_en'] ?? '') : '',
    'title_tr' => $isPost ? (string) ($_POST['title_tr'] ?? '') : '',
    'mobile' => $isPost ? (string) ($_POST['mobile'] ?? '') : '',
    'office' => $isPost ? (string) ($_POST['office'] ?? '') : '',
];

$signatureHtml = '';
if ($isPost) {
    $signatureHtml = render_signature($form);
}

?><!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>DEDEM — Outlook imza oluşturucu</title>
  <style>
    :root { --red: #E30613; --text: #231F20; --muted: #6D6E71; --bg: #f7f7f8; }
    * { box-sizing: border-box; }
    body { font-family: Arial, Helvetica, sans-serif; margin: 0; color: var(--text); background: var(--bg); line-height: 1.45; }
    .wrap { max-width: 920px; margin: 0 auto; padding: 24px 20px 48px; }
    h1 { font-size: 1.35rem; margin: 0 0 8px; }
    .lead { color: var(--muted); margin: 0 0 24px; font-size: 0.95rem; }
    .grid { display: grid; gap: 24px; }
    @media (min-width: 860px) { .grid { grid-template-columns: 320px 1fr; align-items: start; } }
    label { display: block; font-size: 0.8rem; font-weight: bold; color: var(--muted); margin-bottom: 4px; }
    input[type="text"] { width: 100%; padding: 8px 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 0.95rem; }
    input:focus { outline: 2px solid rgba(227, 30, 36, 0.35); border-color: var(--red); }
    .field { margin-bottom: 12px; }
    button, .btn { font-family: inherit; cursor: pointer; }
    .btn-primary { background: var(--red); color: #fff; border: 0; padding: 10px 18px; border-radius: 4px; font-weight: bold; font-size: 0.95rem; }
    .btn-primary:hover { filter: brightness(0.95); }
    .btn-secondary { background: #fff; color: var(--text); border: 1px solid #ccc; padding: 8px 14px; border-radius: 4px; font-size: 0.9rem; }
    .btn-secondary:hover { background: #fafafa; }
    .btn-download { background: var(--red); color: #fff; border: 0; padding: 10px 18px; border-radius: 4px; font-weight: bold; font-size: 0.95rem; }
    .btn-download:hover { filter: brightness(0.95); }
    .btn-download:disabled { opacity: 0.65; cursor: wait; }
    .card { background: #fff; border: 1px solid #e5e5e8; border-radius: 8px; padding: 20px; box-shadow: 0 1px 2px rgba(0,0,0,0.04); }
    .card h2 { font-size: 1rem; margin: 0 0 14px; }
    .preview-box { overflow: auto; max-height: 520px; border: 1px dashed #ccc; padding: 16px; background: #fff; border-radius: 4px; }
    textarea.sig-raw { width: 100%; min-height: 160px; font-family: Consolas, "Courier New", monospace; font-size: 11px; padding: 10px; border: 1px solid #ccc; border-radius: 4px; resize: vertical; }
    .hint { font-size: 0.85rem; color: var(--muted); margin-top: 12px; }
    .actions { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; align-items: center; }
    .ok { color: #1a7f37; font-size: 0.85rem; display: none; }
    .ok.show { display: inline; }
    .callout { margin: 12px 0 0; padding: 12px 14px; border-left: 4px solid var(--red); background: #fff9f9; border-radius: 0 6px 6px 0; font-size: 0.88rem; color: var(--text); line-height: 1.5; }
    .callout strong { display: block; margin-bottom: 6px; }
    .callout ol { margin: 8px 0 0; padding-left: 1.2rem; }
    .callout code { font-size: 0.85em; word-break: break-all; }
    kbd { font-family: Consolas, monospace; font-size: 0.85em; border: 1px solid #ccc; border-radius: 3px; padding: 1px 4px; background: #f5f5f5; }
  </style>
</head>
<body>
  <div class="wrap">
    <h1>Outlook imza oluşturucu</h1>
    <p class="lead">Ad, ünvanlar, cep ve santral sizden; adres ve web sitesi <code>config.php</code> içinden gelir. İmzayı <strong>PNG görsel</strong> olarak indirip Outlook’a görsel olarak ekleyebilirsiniz; HTML kopyalama isteğe bağlıdır.</p>

    <div class="grid">
      <div class="card">
        <h2>Bilgiler</h2>
        <form method="post" action="">
          <div class="field">
            <label for="name">Ad Soyad</label>
            <input type="text" id="name" name="name" value="<?= imza_escape($form['name']) ?>" autocomplete="name">
          </div>
          <div class="field">
            <label for="title_en">Ünvan (İngilizce)</label>
            <input type="text" id="title_en" name="title_en" value="<?= imza_escape($form['title_en']) ?>">
          </div>
          <div class="field">
            <label for="title_tr">Ünvan (Türkçe)</label>
            <input type="text" id="title_tr" name="title_tr" value="<?= imza_escape($form['title_tr']) ?>">
          </div>
          <div class="field">
            <label for="mobile">Cep telefonu</label>
            <input type="text" id="mobile" name="mobile" value="<?= imza_escape($form['mobile']) ?>" placeholder="+90 5xx xxx xx xx">
          </div>
          <div class="field">
            <label for="office">Ofis / santral</label>
            <input type="text" id="office" name="office" value="<?= imza_escape($form['office']) ?>" placeholder="+90 850 xxx xx xx">
          </div>
          <button type="submit" class="btn-primary">İmza oluştur</button>
        </form>
      </div>

      <div class="card">
        <h2>Önizleme</h2>
        <?php if ($isPost) : ?>
          <div class="preview-box" id="sig-preview"><?= $signatureHtml ?></div>
          <div class="callout">
            <strong>Outlook’ta imza penceresine HTML yapıştırdıysanız</strong>
            Dosya → Seçenekler → Posta → İmzalar’daki düzenleyici <em>ham HTML’i okumaz</em>; kodu olduğu gibi metin gösterir. Bu normaldir, sizin tarafınızda bir hata değildir.
            <ol>
              <li>Outlook’ta bir imza adı oluşturun (bir kez kaydedin; böylece klasörde <code>.htm</code> dosyası oluşur).</li>
              <li><kbd>Win</kbd> + <kbd>R</kbd> ile çalıştırın, şunu yapıştırın: <code>%AppData%\Microsoft\Signatures</code></li>
              <li>Oluşturduğunuz imzayla aynı ada sahip <code>.htm</code> dosyasını Not Defteri ile açın, içeriği tamamen silip bu sayfadaki HTML ile değiştirin, <strong>UTF-8</strong> olarak kaydedin.</li>
            </ol>
            Alternatif: en kolayı <strong>Görseli indir (PNG)</strong> ile tek görsel imzadır; HTML zorunlu değildir.
          </div>
          <p class="hint">PNG, önizleme kutusunu tarayıcıda görüntüye çevirir; imzadaki logoların gelmesi için sayfa adresinin görsellere erişebilmesi gerekir (aynı sunucu / <code>config.php</code> base URL).</p>
          <div class="actions" style="margin-top:12px;">
            <button type="button" class="btn-download" id="btn-download-png">Görseli indir (PNG)</button>
            <span class="ok" id="png-ok" role="status">PNG indirildi.</span>
          </div>
          <details style="margin-top:16px;">
            <summary style="cursor:pointer;color:var(--muted);font-size:0.9rem;">İsteğe bağlı: HTML kaynağı</summary>
            <label for="sig-html" style="margin-top:10px;display:block;">Kopyalanacak HTML (Outlook penceresine değil, yukarıdaki <code>Signatures</code> klasöründeki .htm dosyasına)</label>
            <textarea id="sig-html" class="sig-raw" readonly><?= htmlspecialchars($signatureHtml, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></textarea>
            <p class="hint" style="margin-top:10px;margin-bottom:0;">Tarayıcılar <strong>Belgeler\\imza</strong> klasörünü otomatik oluşturamaz. İndirme penceresinde bu klasöre gidip oluşturarak kaydedebilir veya dosyayı İndirilenler’den taşıyabilirsiniz. Chrome / Edge’de “Kaydet” diyaloğu ile konumu seçmek daha kolay olabilir.</p>
            <div class="actions">
              <button type="button" class="btn-secondary" id="btn-download-html">HTML kaynağını indir</button>
              <button type="button" class="btn-secondary" id="btn-copy">HTML’i kopyala</button>
              <span class="ok" id="html-ok" role="status">HTML dosyası kaydedildi.</span>
              <span class="ok" id="copy-ok" role="status">Panoya kopyalandı.</span>
            </div>
          </details>
        <?php else : ?>
          <p class="hint" style="margin:0;">Formu gönderdikten sonra önizleme ve kopyalanabilir HTML burada görünür.</p>
        <?php endif; ?>
      </div>
    </div>

    <p class="hint" style="margin-top:28px;">
      <strong>Outlook (PNG):</strong> İndirdiğiniz görseli imza alanına ekleyin (Ekle → Resimler / sürükleyip bırakma). <strong>HTML:</strong> yalnızca <code>%AppData%\Microsoft\Signatures</code> içindeki <code>.htm</code> dosyasını düzenleyerek. <code>http://localhost/...</code> görseller yalnızca bilgisayarınızda XAMPP açıkken çalışır; başkalarına e-posta için <code>config.php</code> içinde internetten erişilebilir bir <code>base_url</code> kullanın.
    </p>
  </div>
  <?php if ($isPost) : ?>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script>
    (function () {
      var preview = document.getElementById('sig-preview');
      var btnPng = document.getElementById('btn-download-png');
      var pngOk = document.getElementById('png-ok');
      if (btnPng && preview && typeof html2canvas !== 'undefined') {
        btnPng.addEventListener('click', function () {
          btnPng.disabled = true;
          html2canvas(preview, {
            scale: 2,
            backgroundColor: '#ffffff',
            useCORS: true,
            allowTaint: false,
            logging: false
          }).then(function (canvas) {
            canvas.toBlob(function (blob) {
              if (!blob) {
                btnPng.disabled = false;
                return;
              }
              var a = document.createElement('a');
              var url = URL.createObjectURL(blob);
              a.href = url;
              a.download = 'dedem-imza.png';
              a.rel = 'noopener';
              document.body.appendChild(a);
              a.click();
              document.body.removeChild(a);
              URL.revokeObjectURL(url);
              if (pngOk) {
                pngOk.classList.add('show');
                setTimeout(function () { pngOk.classList.remove('show'); }, 2500);
              }
              btnPng.disabled = false;
            }, 'image/png');
          }).catch(function (err) {
            btnPng.disabled = false;
            alert('Görsel oluşturulamadı. Sayfayı sunucu adresinden açtığınızdan ve görsellerin yüklendiğinden emin olun.\n' + (err && err.message ? err.message : ''));
          });
        });
      } else if (btnPng) {
        btnPng.addEventListener('click', function () {
          alert('html2canvas yüklenemedi. İnternet bağlantısı veya CDN erişimi gerekir.');
        });
      }

      var btnDl = document.getElementById('btn-download-html');
      var ta = document.getElementById('sig-html');
      var htmlOk = document.getElementById('html-ok');

      function downloadHtmlWithAnchor(blob) {
        var a = document.createElement('a');
        var url = URL.createObjectURL(blob);
        a.href = url;
        a.download = 'dedem-imza.html';
        a.rel = 'noopener';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
      }

      function showHtmlSavedOk() {
        if (htmlOk) {
          htmlOk.classList.add('show');
          setTimeout(function () { htmlOk.classList.remove('show'); }, 2500);
        }
      }

      if (btnDl && ta) {
        btnDl.addEventListener('click', function () {
          var blob = new Blob([ta.value], { type: 'text/html;charset=utf-8' });
          if (typeof window.showSaveFilePicker === 'function') {
            btnDl.disabled = true;
            window.showSaveFilePicker({
              suggestedName: 'dedem-imza.html',
              types: [{
                description: 'HTML',
                accept: { 'text/html': ['.html', '.htm'] }
              }]
            }).then(function (handle) {
              return handle.createWritable();
            }).then(function (writable) {
              return writable.write(blob).then(function () {
                return writable.close();
              });
            }).then(function () {
              showHtmlSavedOk();
            }).catch(function (err) {
              if (err && err.name === 'AbortError') {
                return;
              }
              downloadHtmlWithAnchor(blob);
              showHtmlSavedOk();
            }).finally(function () {
              btnDl.disabled = false;
            });
          } else {
            downloadHtmlWithAnchor(blob);
            showHtmlSavedOk();
          }
        });
      }

      var btn = document.getElementById('btn-copy');
      var ok = document.getElementById('copy-ok');
      if (!btn || !ta) return;
      btn.addEventListener('click', function () {
        ta.focus();
        ta.select();
        ta.setSelectionRange(0, ta.value.length);
        var done = function () {
          ok.classList.add('show');
          setTimeout(function () { ok.classList.remove('show'); }, 2500);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(ta.value).then(done).catch(function () {
            try { document.execCommand('copy'); done(); } catch (e) {}
          });
        } else {
          try { document.execCommand('copy'); done(); } catch (e) {}
        }
      });
    })();
  </script>
  <?php endif; ?>
</body>
</html>
