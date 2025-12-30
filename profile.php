<?php
// profile.php

function sanitize_username($u) {
  // فقط حروف، اعداد و زیرخط؛ طول منطقی
  $u = trim($u);
  if (!preg_match('/^[A-Za-z0-9_]{3,16}$/', $u)) {
    return false;
  }
  return $u;
}

function curl_get($url, $timeout = 8) {
  $ch = curl_init();
  curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS => 3,
    CURLOPT_CONNECTTIMEOUT => $timeout,
    CURLOPT_TIMEOUT => $timeout,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (Minecraft-Stats-Proxy; +https://example.com)'
  ]);
  $data = curl_exec($ch);
  $err  = curl_error($ch);
  $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
  curl_close($ch);
  return [$data, $code, $err];
}

function extract_sk1er_stats($html) {
  // تلاش برای استخراج چند فیلد عمومی با regex های ملایم
  $stats = [];

  // نمونه الگوها (بسته به ساختار Sk1er ممکنه نیاز به تنظیم داشته باشن)
  // Level
  if (preg_match('/Level[:\s]*<\/?[^>]*>\s*([0-9]+(?:\.[0-9]+)?)/i', $html, $m)) {
    $stats['level'] = $m[1];
  }
  // KDR
  if (preg_match('/KDR[:\s]*<\/?[^>]*>\s*([0-9]+(?:\.[0-9]+)?)/i', $html, $m)) {
    $stats['kdr'] = $m[1];
  }
  // Wins
  if (preg_match('/Wins[:\s]*<\/?[^>]*>\s*([0-9,]+)/i', $html, $m)) {
    $stats['wins'] = str_replace(',', '', $m[1]);
  }
  // Kills
  if (preg_match('/Kills[:\s]*<\/?[^>]*>\s*([0-9,]+)/i', $html, $m)) {
    $stats['kills'] = str_replace(',', '', $m[1]);
  }
  // Played / Playtime
  if (preg_match('/Play(?:ed|time)[:\s]*<\/?[^>]*>\s*([0-9:,hms\s]+)/i', $html, $m)) {
    $stats['playtime'] = trim($m[1]);
  }

  // اگر چیزی پیدا نشد، یک خلاصه خام برگردونیم
  if (empty($stats)) {
    // برش کوتاه از HTML برای نمایش به‌صورت fallback
    $preview = strip_tags($html);
    $preview = preg_replace('/\s+/', ' ', $preview);
    $preview = mb_substr($preview, 0, 500, 'UTF-8') . '...';
    $stats['raw_preview'] = $preview;
  }

  return $stats;
}

$username = $_POST['username'] ?? '';
$username = sanitize_username($username);

if ($username === false) {
  $error = 'نام کاربری نامعتبر است. فقط حروف، اعداد و _ با طول 3 تا 16 کاراکتر مجاز است.';
}

// NameMC اسکین به‌صورت تصویر قابل نمایش مستقیم
$skinUrl = $username ? "https://namemc.com/profile/{$username}.png" : null;

// Sk1er واکشی HTML
$sk1erHtml = null;
$sk1erUrl  = $username ? "https://sk1er.club/stats/{$username}" : null;
$stats     = null;
$fetchErr  = null;

if (!$error && $sk1erUrl) {
  [$html, $code, $err] = curl_get($sk1erUrl, 10);
  if ($err || $code >= 400 || empty($html)) {
    $fetchErr = "دریافت اطلاعات از Sk1er Club با مشکل مواجه شد. کد: {$code}" . ($err ? " — {$err}" : "");
  } else {
    $sk1erHtml = $html;
    $stats = extract_sk1er_stats($html);
  }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>پروفایل بازیکن — <?php echo htmlspecialchars($username ?: 'ناشناس'); ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Vazirmatn:wght@300;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body class="bg">
  <nav class="nav">
    <div class="brand">سرزمین ماینکرفت — 13mahdi90</div>
    <div>
      <a href="index.php">خانه</a>
      <a href="<?php echo htmlspecialchars($sk1erUrl ?? '#'); ?>" target="_blank">Sk1er Club</a>
      <a href="<?php echo htmlspecialchars("https://namemc.com/profile/{$username}"); ?>" target="_blank">NameMC</a>
      <button class="toggle" id="mode">روز/شب</button>
    </div>
  </nav>

  <main class="container">
    <section class="card">
      <h1>پروفایل: <?php echo htmlspecialchars($username ?: '—'); ?></h1>

      <?php if (!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <a class="cta" href="index.php">بازگشت</a>
      <?php else: ?>
        <div class="grid2">
          <div>
            <h2>اسکین NameMC</h2>
            <p class="muted">تصویر مستقیم از NameMC (همیشه به‌روز هنگام لود).</p>
            <img class="skin" src="<?php echo htmlspecialchars($skinUrl); ?>" alt="Skin <?php echo htmlspecialchars($username); ?>">
          </div>
          <div>
            <h2>آمار Sk1er Club</h2>
            <p class="muted">واکشی زنده از Sk1er؛ اگر فیلدها تغییر کرده باشه، خلاصه خام نمایش داده می‌شه.</p>

            <?php if (!empty($fetchErr)): ?>
              <div class="error"><?php echo htmlspecialchars($fetchErr); ?></div>
            <?php elseif (!empty($stats)): ?>
              <ul class="stats">
                <?php foreach ($stats as $k => $v): ?>
                  <li><strong><?php echo htmlspecialchars(strtoupper($k)); ?>:</strong> <?php echo htmlspecialchars($v); ?></li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <div class="error">هیچ داده‌ای پیدا نشد.</div>
            <?php endif; ?>

            <div class="links">
              <a class="cta" href="<?php echo htmlspecialchars($sk1erUrl); ?>" target="_blank">نمایش صفحه کامل Sk1er</a>
            </div>
          </div>
        </div>

        <div class="actions">
          <a class="cta" href="index.php">تغییر نام کاربری</a>
        </div>
      <?php endif; ?>
    </section>

    <footer class="footer">ساخته‌شده با عشق و پیکسل — 13mahdi90</footer>
  </main>

  <script>
    const modeBtn = document.getElementById('mode');
    modeBtn.addEventListener('click', () => {
      document.body.classList.toggle('night');
      modeBtn.textContent = document.body.classList.contains('night') ? 'روز' : 'شب';
    });
  </script>
</body>
</html>