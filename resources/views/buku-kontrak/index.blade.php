<h2>Buku Kontrak</h2>

@if (session('success'))
    <div style="background:#dcfce7; color:#166534; padding:8px 12px; margin:8px 0; border-radius:6px;">
        {{ session('success') }}
    </div>
@endif

<a href="{{ route('buku-kontrak.create') }}">+ Tambah</a>

<form method="GET" style="margin:10px 0;">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="No kontrak">
    <select name="status" onchange="this.form.submit()">
        <option value="">Semua status</option>
        @foreach (\App\Models\BukuKontrak::statusLabels() as $k => $v)
            <option value="{{ $k }}" @selected(request('status') == $k)>{{ $v }}</option>
        @endforeach
    </select>
    <button type="submit">Cari</button>
</form>

<table border="1" cellpadding="6" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>No</th>
            <th>No Kontrak</th>
            <th>Customer</th>
            <th>Tgl DTG</th>
            <th>Dibawa Oleh</th>
            <th>Pada Tgl</th>
            <th>Status Sekarang</th>
            <th>Pada Tanggal</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($data as $i => $r)
            @php
                $warna = match ($r->status) {
                    'depo_cirebon' => '#6b7280',
                    'dibawa_teknisi' => '#d97706',
                    'di_customer' => '#2563eb',
                    'selesai' => '#16a34a',
                    default => '#6b7280',
                };
            @endphp
            <tr>
                <td>{{ $data->firstItem() + $i }}</td>
                <td>{{ $r->no_kontrak }}</td>
                <td>{{ $r->customer->nama_customer ?? '-' }}</td>
                <td>{{ $r->tgl_dtg->format('d/m/Y') }}</td>
                <td>{{ $r->technician->nama_technician ?? '-' }}</td>
                <td>{{ $r->tgl_dibawa?->format('d/m/Y') ?? '-' }}</td>
                <td>
                    <span
                        style="background: {{ $warna }}; color:#fff; padding:2px 10px; border-radius:12px; font-size:12px;">
                        {{ \App\Models\BukuKontrak::statusLabels()[$r->status] ?? $r->status }}
                    </span>
                </td>
                <td>{{ $r->tgl_status->format('d/m/Y') }}</td>
                <td>
                    <a href="{{ route('buku-kontrak.edit', $r) }}">Edit</a>
                    <form action="{{ route('buku-kontrak.destroy', $r) }}" method="POST" style="display:inline"
                        onsubmit="return confirm('Hapus?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" align="center">Belum ada data</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{ $data->links() }}
