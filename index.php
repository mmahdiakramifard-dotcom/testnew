<?php
// index.php
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ورود بازیکن — سرزمین ماینکرفت</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Vazirmatn:wght@300;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body class="bg">
  <nav class="nav">
    <div class="brand">سرزمین ماینکرفت — 13mahdi90</div>
    <div>
      <a href="index.php">خانه</a>
      <a href="#" onclick="alert('به‌زودی!')">ویژگی‌ها</a>
      <a href="#" onclick="alert('به‌زودی!')">گالری</a>
      <a href="#" onclick="alert('به‌زودی!')">دانلود</a>
      <button class="toggle" id="mode">روز/شب</button>
    </div>
  </nav>

  <main class="container">
    <section class="card">
      <h1>نام کاربری ماینکرافت پرمیوم رو وارد کن</h1>
      <p>بعد از ارسال، اسکینت از NameMC و آمار از Sk1er Club به‌روز نمایش داده می‌شه.</p>
      <form class="grid2" action="profile.php" method="POST">
        <input class="input" type="text" name="username" placeholder="مثلاً Notch" required>
        <button class="cta" type="submit">نمایش اطلاعات</button>
      </form>
      <p class="muted">نکته: فقط اکانت‌های پرمیوم (دارای UUID رسمی) اطلاعات معتبر خواهند داشت.</p>
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