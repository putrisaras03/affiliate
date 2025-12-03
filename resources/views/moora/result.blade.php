<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Hasil Ranking Produk (MOORA) - Recofy</title>
  <link rel="icon" href="{{ asset('assets/img/recofy.png') }}" type="image/png">
  <link rel="stylesheet" href="{{ asset('assets/css/result.css') }}">
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
      <li><a href="/dashboard"><i class="fa-solid fa-gauge-high"></i> <span class="menu-text">Dashboard</span></a></li>
      <li class="etalase active"><a href="#"><i class="fa-solid fa-cart-shopping"></i> <span class="menu-text">Akun & Produk</span></a></li>
      <li><a href="/criteria"><i class="fa-solid fa-sliders"></i> <span class="menu-text">Pengaturan Kriteria</span></a></li>
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
        <div class="nav-title">Hasil Ranking Produk (MOORA)</div>

        <div class="user-area">
          <div class="greetingg">Hi, {{ auth()->user()->username ?? auth()->user()->name }}!</div>
          <a href="{{ route('profile') }}">
            <div class="avatar" style="cursor: pointer;">
              <img src="{{ auth()->user()->img_profile ? asset('img_profiles/' . auth()->user()->img_profile) : asset('assets/img/profil.jpg') }}" 
                  alt="Profil" />
            </div>
          </a>
        </div>
      </div>

      <!-- CONTENT -->
      <div class="ranking-content" style="padding: 20px;">
          <h3 class="mb-4">Ranking Produk Berdasarkan Perhitungan MOORA</h3>

          <table class="table table-bordered table-hover">
            <thead class="table-light">
              <tr>
                <th>Rank</th>
                <th>Produk</th>
                <th>Gambar</th>
                <th>Harga</th>
                <th>Komisi</th>
                <th>Terjual</th>
                <th>Rating Produk</th>
                <th>Rating Toko</th>
                <th>Link</th>
                <th>Skor</th>
              </tr>
            </thead>

            <tbody>
              @foreach ($results as $i => $item)
              <tr>
                <td><strong>{{ $results->firstItem() + $loop->index }}</strong></td>

                <td>{{ $item['product']->name }}</td>

                <td>
                  <img src="{{ $item['product']->image }}" width="60">
                </td>

                <td>
                  Rp {{ number_format($item['product']->price_min ?? $item['product']->price ?? 0, 0, ',', '.') }}
                </td>

                <td>
                  Rp {{ number_format($item['product']->commission_value ?? 0, 0, ',', '.') }}
                </td>

                <td>{{ $item['product']->historical_sold ?? 0 }}</td>

                <td>{{ $item['product']->rating_star ?? '-' }}</td>

                <td>{{ $item['product']->shop_rating ?? '-' }}</td>

                <td>
                  @if (!empty($item['product']->product_link))
                    <a href="{{ $item['product']->product_link }}" target="_blank">Kunjungi</a>
                  @else - @endif
                </td>

                <td>{{ number_format($item['score'], 4) }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
<!-- PAGINATION LARAVEL -->
      <div class="d-flex justify-content-center mt-3">
        {{ $results->links('pagination::bootstrap-5') }}
      </div>

    </div>
  </div>
</div>

<!-- SCRIPT -->
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