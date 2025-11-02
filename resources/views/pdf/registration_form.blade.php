@php
$fieldsByCategory = $fields->groupBy('category');
// Urutkan kategori berdasarkan id field pertama di kategori tsb (paling awal masuk = paling atas)
$categories = $fieldsByCategory->sortBy(function($group) {
    return $group->first()->id;
})->keys();
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Formulir Penerimaan Peserta Didik Baru</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .header-table td { vertical-align: top; }
        .logo { width: 70px; height: 70px; border:1px solid #000; text-align:center; }
        .school-title { font-size: 15px; font-weight: bold; }
        .school-subtitle { font-size: 13px; }
        .form-title { text-align: center; font-weight: bold; font-size: 14px; margin-bottom: 2px; }
        .form-subtitle { text-align: center; font-size: 13px; margin-bottom: 8px; }
        table.form-section { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table.form-section td, table.form-section th { border: 1px solid #000; padding: 3px 5px; }
        .section-label { font-weight: bold; background: #f2f2f2; }
        .isian { border-bottom: 1px dotted #000; display: inline-block; min-width: 200px; height: 14px; }
        .lampiran-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .lampiran-table td, .lampiran-table th { border: 1px solid #000; padding: 2px 4px; font-size: 11px; }
        .ttd-table { width: 100%; margin-top: 20px; }
        .ttd-table td { text-align: center; }
        .checklist { font-family: DejaVu Sans, sans-serif; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td class="logo">
                LOGO
            </td>
            <td style="text-align:center">
                <div class="school-title">DINAS PENDIDIKAN KEBUDAYAAN PEMUDA DAN OLAH RAGA</div>
                <div class="school-title">SEKOLAH DASAR NEGERI NO. 200/VIII SUNGAI KARANG</div>
                <div class="school-subtitle">KECAMATAN VII KOTO ILIR KABUPATEN TEBO</div>
                <div class="school-subtitle">Jl. Poros Ds. Sungai Karang, Kec. VII Koto Ilir, Kab. Tebo, Prov. Jambi, 37235 Website: www.sd200.sch.id</div>
            </td>
        </tr>
    </table>
    <div class="form-title">FORMULIR PENERIMAAN PESERTA DIDIK BARU</div>
    <div class="form-subtitle">SDN NO. 200/VIII SUNGAI KARANG TAHUN PELAJARAN {{ $academicYear->name ?? '' }}</div>
    @foreach($categories as $category)
        @if(isset($fieldsByCategory[$category]))
        <table class="form-section">
            <tr><th colspan="4" class="section-label">{{ $category }}</th></tr>
            @php $no = 1; @endphp
            @foreach($fieldsByCategory[$category]->sortBy('order') as $field)
                <tr>
                    <td width="3%">{{ $no++ }}.</td>
                    <td width="30%">{{ $field->label }}</td>
                    <td width="2%">:</td>
                    <td><span class="isian">&nbsp;</span></td>
                </tr>
            @endforeach
        </table>
        @endif
    @endforeach
    <div class="section-label" style="margin-top:10px;">D. LAMPIRAN YANG DISERAHKAN BERSAMA FORMULIR PENDAFTARAN</div>
    <table class="lampiran-table">
        <tr>
            <td>1. Pas photo 3x4 cm</td>
            <td class="checklist">&#x2610;</td>
            <td>2. Akta Kelahiran</td>
            <td class="checklist">&#x2610;</td>
            <td>3. Kartu Keluarga (KK)</td>
            <td class="checklist">&#x2610;</td>
            <td>4. Ijazah/PAUD/TK (jika ada)</td>
            <td class="checklist">&#x2610;</td>
        </tr>
    </table>
    <table class="ttd-table">
        <tr>
            <td width="60%"></td>
            <td>
                Sungai Karang, {{ date('d F Y') }}<br>
                Orang Tua/Wali,<br><br><br><br>
                (_______________________)
            </td>
        </tr>
    </table>
</body>
</html> 