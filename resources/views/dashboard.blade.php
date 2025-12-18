<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Recofy</title>
  <link rel="icon" href="{{ asset('assets/img/recofy.png') }}" type="image/png">
  <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <style>
    body {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
    }
    * { box-sizing: border-box; }
    #app { width: 100%; min-height: 100%; }
  </style>
</head>

<body>
  <div class="container">

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <div class="menu-container">

        <div class="logo">
          <div class="brand">
            <img src="/assets/img/4-foto.png">
          </div>
          <div class="bars-wrapper" id="toggleSidebar">
            <i class="fa-solid fa-bars"></i>
          </div>
        </div>

        <ul>
          <li class="dashboard active"><a href="#"><i class="fa-solid fa-gauge-high"></i> <span class="menu-text">Dashboard</span></a></li>
          <li><a href="etalase"><i class="fa-solid fa-cart-shopping"></i> <span class="menu-text">Akun & Produk</span></a></li>
          <li><a href="criteria"><i class="fa-solid fa-sliders"></i> <span class="menu-text">Pengaturan Kriteria</span></a></li>
          <li><a href="profile"><i class="fa-solid fa-gear"></i> <span class="menu-text">Pengaturan Akun</span></a></li>
        </ul>
      </div>

      <div class="logout-wrapper">
        <a href="#" onclick="konfirmasiLogout()" class="logout-btn">
          <i class="fa-solid fa-right-from-bracket"></i>
          <span class="logout-text">Keluar</span>
        </a>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">

      <div class="navbar">
        <div class="nav-title">Dashboard</div>

        <div class="user-area">
          <div class="greetingg">Hi, {{ $user->username ?? $user->name }}!</div>
          <a href="{{ route('profile') }}">
            <div class="avatar" style="cursor: pointer;">
              <img src="{{ $user->img_profile ? asset('img_profiles/' . $user->img_profile) : asset('assets/img/profil.jpg') }}" 
              alt="Profil" />
            </div>
          </a>
        </div>
      </div>

      <!-- CONTENT -->
      <div class="dashboard-content">
        <div id="app"></div>
      </div>

    </div>
  </div>

<!-- Sidebar Script -->
<script>
  function konfirmasiLogout() {
    if (confirm("Apakah Anda yakin ingin logout?")) {
      window.location.href = "/";
    }
  }

  const sidebar = document.getElementById('sidebar');
  const mainContent = document.getElementById('mainContent');
  const toggleBtn = document.getElementById('toggleSidebar');

  toggleBtn.addEventListener('click', function () {
    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('expanded');
  });
</script>

<!-- DASHBOARD SCRIPT -->
<script>
  const defaultConfig = {
    background_color: "#f0f4f8",
    card_color: "#ffffff",
    text_color: "#1a202c",
    primary_action_color: "#4299e1",
    accent_color: "#48bb78",
    font_family: "Inter",
    font_size: 16,
    dashboard_title: "Dashboard Performa Akun",
    kpi_section_title: "Key Performance Indicators",
    chart_section_title: "Grafik Perbandingan Akun"
  };

  const accountsData = @json($accountsData);

  function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0
    }).format(amount);
  }

  function createAccountCard(account, config) {
    return `
      <article style="
        background: ${config.card_color};
        padding: 24px; border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 2px solid ${config.primary_action_color};
      ">
        <header style="margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid ${config.background_color};">
          <h3 style="font-size: 20px; color: ${config.text_color}; font-weight: 700; margin: 0;">
            ${account.name}
          </h3>
        </header>

        <div style="display: grid; gap: 16px;">
          <div style="display: flex; justify-content: space-between;">
            <span style="opacity: 0.7;">📦 Total Produk</span>
            <span style="font-weight: 700;">${account.totalProducts}</span>
          </div>

          <div style="display: flex; justify-content: space-between;">
            <span style="opacity: 0.7;">💰 Rata-rata Komisi</span>
            <span style="font-weight: 700; color: ${config.accent_color};">${account.avgCommission}%</span>
          </div>

          <div style="display: flex;  justify-content: space-between;">
            <span style="opacity: 0.7;">🚀 Potensi Komisi Tertinggi</span>
            <span style="font-weight: 700; color: ${config.primary_action_color};">${formatCurrency(account.maxCommission)}</span>
          </div>

          <div style="display: flex; justify-content: space-between;">
            <span style="opacity: 0.7;">⭐ Rating Toko</span>
            <span style="font-weight: 700;">${account.avgRating}</span>
          </div>
        </div>
      </article>
    `;
  }

  function createBarChart(data, config) {
    if (data.length === 0) return "<p>Tidak ada akun affiliate.</p>";

    const maxValue = Math.max(...data.map(d => d.maxCommission));

    return data.map(account => {
      const percentage = maxValue > 0 ? (account.maxCommission / maxValue) * 100 : 0;

      return `
        <div style="margin-bottom: 20px;">
          <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span style="font-weight: 600;">${account.name}</span>
            <span style="font-weight: 700;">${formatCurrency(account.maxCommission)}</span>
          </div>

          <div style="width: 100%; height: 36px; background: ${config.background_color};
                      border-radius: 8px; overflow: hidden;">
            <div style="
              width: ${percentage}%;
              height: 100%;
              background: linear-gradient(90deg, ${config.primary_action_color}, ${config.accent_color});
              display: flex; justify-content: flex-end; align-items: center; padding-right: 12px;
            ">
              <span style="color: white; font-weight: 700;">${percentage.toFixed(1)}%</span>
            </div>
          </div>
        </div>
      `;
    }).join("");
  }

  function render(config) {
    const app = document.getElementById("app");
    app.style.background = config.background_color;

    app.innerHTML = `
      <main style="width: 100%; padding: 40px 24px;">
        <header style="text-align: center; margin-bottom: 40px;">
          <h1 style="font-size: 32px; font-weight: 800; color: ${config.text_color};">${config.dashboard_title}</h1>
          <p style="opacity: .6;">Pantau performa semua akun marketplace Anda</p>
        </header>

        <section style="margin-bottom: 48px;">
          <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 24px;">
            ${config.kpi_section_title}
          </h2>

          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
            ${accountsData.length > 0 
              ? accountsData.map(acc => createAccountCard(acc, config)).join("")
              : "<p>Belum ada akun affiliate.</p>"}
          </div>
        </section>

        <section>
          <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 24px;">
            ${config.chart_section_title}
          </h2>

          <div style="background: ${config.card_color}; padding: 32px; border-radius: 16px;
                      box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 24px;">
              Potensi Komisi per Akun
            </h3>

            ${createBarChart(accountsData, config)}
          </div>
        </section>
      </main>
    `;
  }

  render(defaultConfig);
</script>

</body>
</html>
