@php
    // 1. Ambil hanya field yang AKTIF saja agar tidak perlu cek if di dalam loop
    // Pastikan $fields tidak null
    $activeFields = isset($fields) ? $fields->where('is_active', 1) : collect();

    // 2. Kelompokkan berdasarkan kategori
    $fieldsByCategory = $activeFields->groupBy('category');

    // 3. LOGIKA PENGURUTAN CUSTOM
    // Kita beri "Bobot" manual:
    // - Identitas = 1
    // - Orang Tua = 2
    // - Sisanya = 100 + ID (Agar urut sesuai waktu pembuatan)
    
    $categories = $fieldsByCategory->keys()->sort(function($a, $b) use ($fieldsByCategory) {
        $nameA = strtoupper($a);
        $nameB = strtoupper($b);

        // Fungsi Helper untuk menentukan bobot
        $getWeight = function($name) use ($fieldsByCategory) {
            if (str_contains($name, 'IDENTITAS') || str_contains($name, 'SISWA')) return 1;
            if (str_contains($name, 'ORANG TUA') || str_contains($name, 'WALI')) return 2;
            
            // Untuk kategori baru lainnya, urutkan berdasarkan ID field pertamanya
            // Semakin kecil ID (dibuat duluan), semakin di atas
            $firstField = $fieldsByCategory[$name]->first();
            return 100 + ($firstField->id ?? 9999);
        };

        return $getWeight($nameA) <=> $getWeight($nameB);
    });
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Formulir Penerimaan Peserta Didik Baru</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        
        /* Gaya untuk Kop Surat */
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; border-bottom: 3px double #000; padding-bottom: 5px; }
        .header-table td { vertical-align: middle; }
        .logo { width: 80px; text-align:center; border: 1px solid #000; height: 80px; line-height: 80px; } /* Kotak Logo */
        
        .school-title { font-size: 14px; font-weight: bold; text-transform: uppercase; text-align: center; }
        .school-name { font-size: 16px; font-weight: bold; text-transform: uppercase; text-align: center; }
        .school-subtitle { font-size: 12px; text-align: center; }
        
        /* Judul Formulir */
        .form-title { text-align: center; font-weight: bold; font-size: 14px; margin-top: 15px; text-decoration: underline; }
        .form-subtitle { text-align: center; font-size: 12px; margin-bottom: 20px; font-weight: bold; }
        
        /* Tabel Form */
        table.form-section { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.form-section td, table.form-section th { border: 1px solid #000; padding: 4px 6px; vertical-align: top; }
        .section-label { font-weight: bold; background: #e6e6e6; text-align: left; padding: 5px; }
        
        /* Titik-titik isian */
        .isian { 
            display: inline-block; 
            width: 98%; 
            border-bottom: 1px dotted #000; 
            height: 14px; 
        }
        
        /* Lampiran */
        .lampiran-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .lampiran-table td { border: 1px solid #000; padding: 5px; font-size: 11px; }
        .checklist { font-family: DejaVu Sans, sans-serif; font-size: 14px; text-align: center; width: 30px; }
        
        /* Tanda Tangan */
        .ttd-table { width: 100%; margin-top: 30px; }
        .ttd-table td { text-align: center; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td class="logo" width="15%">LOGO</td>
            <td width="85%">
                <div class="school-title">DINAS PENDIDIKAN DAN KEBUDAYAAN</div>
                <div class="school-name">YAYASAN INSAN MADANI</div>
                <div class="school-subtitle">KECAMATAN GEGER, KABUPATEN MADIUN</div>
                <div class="school-subtitle">JL Segaran Permai, Klotok, Jatisari, Kec. Geger, Kabupaten Madiun, Jawa Timur 63171</div>
            </td>
        </tr>
    </table>

    <div class="form-title">FORMULIR PENERIMAAN PESERTA DIDIK BARU</div>
    <div class="form-subtitle">TAHUN PELAJARAN {{ $academicYear->name ?? date('Y') . '/' . (date('Y')+1) }}</div>

    @foreach($categories as $category)
        @if(isset($fieldsByCategory[$category]))
        
        <table class="form-section">
            <tr>
                <th colspan="3" class="section-label">{{ $category }}</th>
            </tr>
            
            @php $no = 1; @endphp
            
            {{-- Loop Field di dalam kategori tersebut --}}
            @foreach($fieldsByCategory[$category]->sortBy('order') as $field)
                
                {{-- LOGIKA UTAMA: Hanya tampilkan jika statusnya AKTIF (Checked di Admin) --}}
                @if($field->is_active) 
                <tr>
                    <td width="5%" style="text-align: center;">{{ $no++ }}.</td>
                    <td width="35%">{{ $field->label }}</td>
                    <td width="60%">: <span class="isian"></span></td> 
                </tr>
                @endif
                
            @endforeach
        </table>
        
        @endif
    @endforeach

    <div style="font-weight: bold; margin-top:15px; margin-bottom: 5px; font-size: 11px;">
        LAMPIRAN YANG DISERAHKAN BERSAMA FORMULIR PENDAFTARAN
    </div>

    <table class="lampiran-table" style="width: 100%;">
        @php 
            $no = 1; 
            // Kita pecah data jadi per baris isi 2 item (Kiri & Kanan)
        @endphp
        
        @foreach($documents->chunk(2) as $row)
            <tr>
                @foreach($row as $doc)
                    {{-- Nama Dokumen --}}
                    <td style="width: 40%;">
                        {{ $no++ }}. {{ $doc->name }}
                    </td>
                    
                    {{-- Kotak Centang --}}
                    <td class="checklist" style="width: 10%;">
                        &#9744;
                    </td>
                @endforeach

                {{-- Jika datanya Ganjil, kita tambah sel kosong biar tabel tetap rapi --}}
                @if($row->count() < 2)
                    <td style="width: 40%;"></td>
                    <td style="width: 10%;"></td>
                @endif
            </tr>
        @endforeach
        
        {{-- Jika tidak ada dokumen sama sekali di database --}}
        @if($documents->isEmpty())
            <tr>
                <td colspan="4" style="text-align: center; font-style: italic;">
                    - Tidak ada lampiran khusus -
                </td>
            </tr>
        @endif
    </table>

    <table class="ttd-table">
        <tr>
            <td width="60%"></td>
            <td>
                Sungai Karang, {{ date('d F Y') }}<br>
                Orang Tua/Wali,<br><br><br><br><br>
                ( ..................................................... )
            </td>
        </tr>
    </table>
</body>
</html>