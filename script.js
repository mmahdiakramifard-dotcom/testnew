document.getElementById("playerForm").addEventListener("submit", async function(e) {
  e.preventDefault();
  const username = document.getElementById("username").value.trim();
  const resultDiv = document.getElementById("result");

  if (!username) {
    resultDiv.innerHTML = "<p>لطفاً نام کاربری معتبر وارد کن.</p>";
    return;
  }

  // اسکین از Crafatar (بر اساس UUID Mojang)
  try {
    // گرفتن UUID از Mojang API
    const res = await fetch(`https://api.mojang.com/users/profiles/minecraft/${username}`);
    if (!res.ok) throw new Error("کاربر پیدا نشد");
    const data = await res.json();
    const uuid = data.id;

    // نمایش اسکین
    const skinUrl = `https://crafatar.com/avatars/${uuid}?size=200&overlay`;
    resultDiv.innerHTML = `
      <h2>اسکین ${username}</h2>
      <img src="${skinUrl}" alt="Skin of ${username}">
      <p>UUID: ${uuid}</p>
    `;

    // آمار (نمونه: لینک مستقیم به Hypixel API یا Sk1er Club)
    resultDiv.innerHTML += `
      <p><a href="https://sk1er.club/stats/${username}" target="_blank">مشاهده آمار در Sk1er Club</a></p>
    `;
  } catch (err) {
    resultDiv.innerHTML = `<p>خطا: ${err.message}</p>`;
  }
});
