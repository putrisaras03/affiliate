<div class="container py-4">

  <h2 class="mb-4">Hasil Ranking Produk (MOORA)</h2>

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
      @foreach ($result as $i => $item)
      <tr>
        <td><strong>{{ $i + 1 }}</strong></td>

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

        <td>
          {{ $item['product']->historical_sold ?? 0 }}
        </td>

        <td>
          {{ $item['product']->rating_star ?? '-' }}
        </td>

        <td>
          {{ $item['product']->shop_rating ?? '-' }}
        </td>

        <td>
          @if (!empty($item['product']->product_link))
            <a href="{{ $item['product']->product_link }}" target="_blank">Kunjungi</a>
          @else
            -
          @endif
        </td>

        <td>{{ number_format($item['score'], 4) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

</div>
