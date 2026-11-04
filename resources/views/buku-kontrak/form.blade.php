<form method="POST" action="{{ $row->exists ? route('buku-kontrak.update', $row) : route('buku-kontrak.store') }}">
    @csrf
    @if ($row->exists)
        @method('PUT')
    @endif

    <label>No Kontrak</label>
    <input type="text" name="no_kontrak" value="{{ old('no_kontrak', $row->no_kontrak) }}" required>

    <label>Customer</label>
    <select name="customer_id">
        <option value="">-</option>
        @foreach ($customers as $c)
            <option value="{{ $c->id }}" @selected(old('customer_id', $row->customer_id) == $c->id)>{{ $c->name }}</option>
        @endforeach
    </select>

    <label>Tgl DTG</label>
    <input type="date" name="tgl_dtg" value="{{ old('tgl_dtg', optional($row->tgl_dtg)->format('Y-m-d')) }}" required>

    <label>Dibawa Oleh</label>
    <select name="technician_id">
        <option value="">-</option>
        @foreach ($technicians as $t)
            <option value="{{ $t->id }}" @selected(old('technician_id', $row->technician_id) == $t->id)>{{ $t->name }}</option>
        @endforeach
    </select>

    <label>Pada Tgl</label>
    <input type="date" name="tgl_dibawa"
        value="{{ old('tgl_dibawa', optional($row->tgl_dibawa)->format('Y-m-d')) }}">

    <label>Status Sekarang</label>
    <select name="status" required>
        @foreach (\App\Models\BukuKontrak::statusLabels() as $k => $v)
            <option value="{{ $k }}" @selected(old('status', $row->status) == $k)>{{ $v }}</option>
        @endforeach
    </select>

    <label>Pada Tanggal</label>
    <input type="date" name="tgl_status"
        value="{{ old('tgl_status', optional($row->tgl_status)->format('Y-m-d')) }}" required>

    <label>Keterangan</label>
    <textarea name="keterangan">{{ old('keterangan', $row->keterangan) }}</textarea>

    <button type="submit">Simpan</button>
</form>
