<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Recofy</title>
  <link rel="icon" href="{{ asset('assets/img/recofy.png') }}" type="image/png">
  <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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

    <!-- AREA KOSONG / READY FOR CONTENT -->
    <div class="dashboard-content"></div>

  </div>
</div>

<!-- Script interaktif -->
<script>
  function konfirmasiLogout() {
    const yakin = confirm("Apakah Anda yakin ingin logout?");
    if (yakin) window.location.href = "/";
  }

  const sidebar = document.getElementById('sidebar');
  const mainContent = document.getElementById('mainContent');
  const toggleBtn = document.getElementById('toggleSidebar');

  toggleBtn.addEventListener('click', function () {
    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('expanded');
  });
</script>
</body>
</html>
