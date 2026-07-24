@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Stock Report Generator</h1>

        {{-- Success Message --}}
        @if ($message = session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ $message }}
            </div>
        @endif

        {{-- Form --}}
        <div class="bg-white shadow-md rounded p-6 mb-6">
            <form action="{{ route('reports.download-pdf') }}" method="POST" id="reportForm">
                @csrf

                <div class="grid grid-cols-2 gap-4 mb-6">
                    {{-- Depo --}}
                    <div>
                        <label class="block font-semibold mb-2">Depo</label>
                        <input type="text" name="depo" value="CIREBON"
                            class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Depo">
                    </div>

                    {{-- Tanggal --}}
                    <div>
                        <label class="block font-semibold mb-2">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}"
                            class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>

                    {{-- Dibuat Oleh --}}
                    <div>
                        <label class="block font-semibold mb-2">Dibuat Oleh</label>
                        <input type="text" name="dibuat_oleh" value="Idrus"
                            class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Nama">
                    </div>

                    {{-- Diketahui Oleh --}}
                    <div>
                        <label class="block font-semibold mb-2">Diketahui Oleh</label>
                        <input type="text" name="diketahui_oleh" value="Rizen"
                            class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Nama">
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex gap-3">
                    <button type="submit" formaction="{{ route('reports.download-pdf') }}"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        📥 Download PDF
                    </button>

                    <button type="submit" formaction="{{ route('reports.save-pdf') }}"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        💾 Simpan ke Storage
                    </button>
                </div>
            </form>
        </div>

        {{-- Info --}}
        <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded">
            <p><strong>📋 Catatan:</strong></p>
            <ul class="list-disc ml-5 mt-2">
                <li><strong>Download PDF:</strong> PDF akan langsung di-download ke komputer kamu</li>
                <li><strong>Simpan ke Storage:</strong> PDF akan disimpan di folder server:
                    <code>storage/app/public/pdfs/</code>
                </li>
            </ul>
        </div>
    </div>
@endsection
