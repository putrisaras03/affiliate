<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pengaturan Bobot Kriteria - Recofy</title>
  <link rel="icon" href="{{ asset('assets/img/recofy.png') }}" type="image/png">
  <link rel="stylesheet" href="{{ asset('assets/css/criteria.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <!-- Bootstrap -->
  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
          <li><a href="/dashboard"><i class="fa-solid fa-gauge-high"></i> <span class="menu-text">Dashboard</span></a></li>
          <li><a href="/etalase"><i class="fa-solid fa-cart-shopping"></i> <span class="menu-text">Akun & Produk</span></a></li>
          <li class="criteria active"><a href="#"><i class="fa-solid fa-sliders"></i> <span class="menu-text">Pengaturan Kriteria</span></a></li>
          <li><a href="/profile"><i class="fa-solid fa-gear"></i> <span class="menu-text">Pengaturan Akun</span></a></li>
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
        <div class="nav-title">Pengaturan Bobot Kriteria</div>

        <div class="user-area">
          <div class="greetingg">Hi, {{ auth()->user()->username ?? auth()->user()->name }}!</div>
          <a href="{{ route('profile') }}">
            <div class="avatar" style="cursor: pointer;">
              <img src="{{ auth()->user()->img_profile
                ? asset('img_profiles/' . auth()->user()->img_profile)
                : asset('assets/img/profil.jpg') }}">
            </div>
          </a>
        </div>
      </div>


      <!-- =========================== -->
      <!--    FORM PENGATURAN KRITERIA -->
      <!-- =========================== -->
      <div class="dashboard-content" style="padding: 20px;">
        <div class="box" style="padding: 20px;">
          <h3 class="mb-3">Atur Bobot & Tipe Kriteria MOORA</h3>

          <form action="{{ route('criteria.store') }}" method="POST">
            @csrf

            <table class="table table-bordered">
              <thead class="table-light">
              <tr>
                <th>Nama Field</th>
                <th>Bobot</th>
                <th>Tipe</th>
              </tr>
              </thead>

              <tbody>
              @foreach($columns as $col)
                <tr>
                  <td>{{ $col }}</td>

                  <td>
                    <input type="number"
                           step="0.01"
                           class="form-control"
                           name="criteria[{{ $col }}][weight]"
                           value="{{ $settings[$col]->weight ?? 0 }}">
                  </td>

                  <td>
                    <select class="form-control" name="criteria[{{ $col }}][type]">
                      <option value="benefit"
                          {{ (isset($settings[$col]) && $settings[$col]->type=='benefit') ? 'selected':'' }}>
                        Benefit
                      </option>
                      <option value="cost"
                          {{ (isset($settings[$col]) && $settings[$col]->type=='cost') ? 'selected':'' }}>
                        Cost
                      </option>
                    </select>
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>

            <button class="btn btn-primary mt-2">Simpan</button>
          </form>
        </div>
      </div>

    </div><!-- /main-content -->
  </div>

  <!-- Script interaktif -->
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
</body>
</html>
