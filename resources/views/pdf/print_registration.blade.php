@php
    // 1. LOGIKA UTAMA (MAPPING): Menghubungkan Label Admin ke Kolom Database
    // KIRI: Label di Admin (Harus sama persis hurufnya) => KANAN: Data di Database
    $dataMap = [
        'Nama Lengkap' => $user->name,
        'Email' => $user->email,
        
        // Data Siswa
        'NISN' => $user->studentDetail->nisn ?? '-',
        'Nomor Induk Kependudukan' => $user->studentDetail->nik ?? '-', // Sesuaikan label admin anda, misal "NIK" atau "Nomor Induk..."
        'NIK' => $user->studentDetail->nik ?? '-',
        'Jenjang Sekolah' => $user->studentDetail->jenjang ?? '-',
        'Asal Sekolah' => $user->studentDetail->asal_sekolah ?? '-',
        'Tempat Lahir' => $user->studentDetail->tempat_lahir ?? '-',
        'Tanggal Lahir' => $user->studentDetail->tanggal_lahir ?? '-',
        'Tempat & Tanggal Lahir' => ($user->studentDetail->tempat_lahir ?? '') . ', ' . ($user->studentDetail->tanggal_lahir ?? ''),
        'Jenis Kelamin' => ($user->studentDetail->gender ?? '') == 'L' ? 'Laki-laki' : 'Perempuan',
        'Agama' => $user->studentDetail->agama ?? '-',
        'Gol. darah' => $user->studentDetail->golongan_darah ?? '-',
        'Kewarganegaraan' => $user->studentDetail->kewarganegaraan ?? '-',
        'Tempat Tinggal' => $user->studentDetail->tempat_tinggal ?? '-',
        'Alamat Lengkap' => $user->studentDetail->alamat ?? '-',

        // Data Orang Tua
        'Nama Ayah Kandung' => $user->studentDetail->nama_ayah ?? '-',
        'Pendidikan Ayah' => $user->studentDetail->pendidikan_ayah ?? '-',
        'Pekerjaan Ayah' => $user->studentDetail->pekerjaan_ayah ?? '-',
        'Nama Ibu Kandung' => $user->studentDetail->nama_ibu ?? '-',
        'Pendidikan Ibu' => $user->studentDetail->pendidikan_ibu ?? '-',
        'Pekerjaan Ibu' => $user->studentDetail->pekerjaan_ibu ?? '-',
        'Penghasilan Orang Tua' => $user->studentDetail->penghasilan_ortu ?? '-',
        'Nomor Telepon / HP Aktif' => $user->studentDetail->no_hp ?? '-',

        // Data Afirmasi
        'No. Kartu Keluarga Harapan (KKH)' => $user->studentDetail->no_kkh ?? '-',
        'No. Kartu Keluarga Sejahtera (KKS)' => $user->studentDetail->no_kks ?? '-',
        'No. Kartu Indonesia Pintar (KIP)' => $user->studentDetail->no_kip ?? '-',
        'No. Kartu Indonesia Sehat (KIS)' => $user->studentDetail->no_kis ?? '-',
    ];

    // 2. Persiapan Kategori (Kode asli Anda)
    $activeFields = isset($fields) ? $fields->where('is_active', 1) : collect();
    $fieldsByCategory = $activeFields->groupBy('category'); // Pastikan kolom di DB admin namanya 'category' atau 'group'

    $categories = $fieldsByCategory->keys()->sort(function($a, $b) use ($fieldsByCategory) {
        $getWeight = function($name) {
            if (stripos($name, 'IDENTITAS') !== false || stripos($name, 'SISWA') !== false) return 1;
            if (stripos($name, 'ORANG TUA') !== false || stripos($name, 'WALI') !== false) return 2;
            return 99;
        };
        return $getWeight($a) <=> $getWeight($b);
    });
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Formulir Penerimaan Peserta Didik Baru</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; border-bottom: 3px double #000; padding-bottom: 5px; }
        .header-table td { vertical-align: middle; }
        .logo { width: 80px; text-align:center; border: 1px solid #000; height: 80px; line-height: 80px; }
        .school-title { font-size: 14px; font-weight: bold; text-transform: uppercase; text-align: center; }
        .school-name { font-size: 16px; font-weight: bold; text-transform: uppercase; text-align: center; }
        .school-subtitle { font-size: 12px; text-align: center; }
        .form-title { text-align: center; font-weight: bold; font-size: 14px; margin-top: 15px; text-decoration: underline; }
        .form-subtitle { text-align: center; font-size: 12px; margin-bottom: 20px; font-weight: bold; }
        table.form-section { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.form-section td, table.form-section th { border: 1px solid #000; padding: 4px 6px; vertical-align: top; }
        .section-label { font-weight: bold; background: #e6e6e6; text-align: left; padding: 5px; }
        .lampiran-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .lampiran-table td { border: 1px solid #000; padding: 5px; font-size: 11px; }
        .checklist { font-family: DejaVu Sans, sans-serif; font-size: 14px; text-align: center; width: 30px; }
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

    {{-- LOOPING KATEGORI (Permintaan Anda: Tampilan sesuai Admin) --}}
    @foreach($categories as $category)
        @if(isset($fieldsByCategory[$category]))
        
        <table class="form-section">
            <tr>
                <th colspan="3" class="section-label">{{ $category }}</th>
            </tr>
            
            @php $no = 1; @endphp
            @foreach($fieldsByCategory[$category]->sortBy('order') as $field)
                @if($field->is_active) 
                <tr>
                    <td width="5%" style="text-align: center;">{{ $no++ }}.</td>
                    <td width="35%">{{ $field->label }}</td>
                    
                    {{-- DISINI MAGIC-NYA: Kita cocokkan Label Admin dengan DataMap --}}
                    <td width="60%">: 
                        {{-- Cari data di mapping array, kalau tidak ada kosongkan --}}
                        <strong>{{ $dataMap[$field->label] ?? '' }}</strong>
                    </td> 
                </tr>
                @endif
            @endforeach
        </table>
        @endif
    @endforeach

    {{-- BAGIAN LAMPIRAN --}}
    <div style="font-weight: bold; margin-top:15px; margin-bottom: 5px; font-size: 11px;">
        LAMPIRAN YANG DISERAHKAN BERSAMA FORMULIR PENDAFTARAN
    </div>

    <table class="lampiran-table" style="width: 100%;">
        @php $no = 1; @endphp
        @foreach($documents->chunk(2) as $row)
            <tr>
                @foreach($row as $doc)
                    <td style="width: 40%;">{{ $no++ }}. {{ $doc->name }}</td>
                    <td class="checklist" style="width: 10%;">&#9744;</td>
                @endforeach
                @if($row->count() < 2)
                    <td style="width: 40%;"></td><td style="width: 10%;"></td>
                @endif
            </tr>
        @endforeach
        @if($documents->isEmpty())
            <tr><td colspan="4" style="text-align: center;">- Tidak ada lampiran khusus -</td></tr>
        @endif
    </table>

    {{-- TANDA TANGAN --}}
    <table class="ttd-table">
        <tr>
            <td width="60%"></td>
            <td>
                Madiun, {{ date('d F Y') }}<br>
                Orang Tua/Wali,<br><br><br><br><br>
                ( ..................................................... )
            </td>
        </tr>
    </table>
</body>
</html>