<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Lulus</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.5; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; border-bottom: 3px double #000; padding-bottom: 10px; }
        .header-table td { vertical-align: top; }
        .logo { width: 70px; height: 70px; border:1px solid #000; text-align:center; vertical-align: middle; }
        .school-title { font-size: 15px; font-weight: bold; text-align: center; }
        .school-subtitle { font-size: 13px; text-align: center; }
        
        .form-title { text-align: center; font-weight: bold; font-size: 16px; margin-bottom: 5px; text-decoration: underline; text-transform: uppercase; }
        .form-number { text-align: center; font-size: 12px; margin-bottom: 20px; }
        
        .table-data { width: 100%; margin-left: 20px; margin-top: 10px; margin-bottom: 20px; border-collapse: collapse; }
        .table-data td { padding: 5px; vertical-align: top; font-size: 12px; }

        .box-status {
            border: 2px solid #000;
            padding: 10px 40px;
            font-weight: bold;
            font-size: 16pt;
            display: inline-block;
            margin: 20px 0;
            text-transform: uppercase;
            background-color: #f0f0f0;
        }

        .ttd-table { width: 100%; margin-top: 40px; }
        .ttd-table td { text-align: center; }
    </style>
</head>
<body>
    
    <table class="header-table">
        <tr>
            <td width="80" align="center" style="vertical-align: middle;">
                <div class="logo">LOGO</div> 
            </td>
            <td>
                <div class="school-title">DINAS PENDIDIKAN KEBUDAYAAN PEMUDA DAN OLAH RAGA</div>
                <div class="school-title">SEKOLAH DASAR NEGERI NO. 200/VIII SUNGAI KARANG</div>
                <div class="school-subtitle">KECAMATAN VII KOTO ILIR KABUPATEN TEBO</div>
                <div class="school-subtitle">Jl. Poros Ds. Sungai Karang, Kec. VII Koto Ilir, Kab. Tebo, Prov. Jambi, 37235 <br> Website: www.sd200.sch.id</div>
            </td>
        </tr>
    </table>

    <div class="form-title">SURAT KETERANGAN LULUS SELEKSI</div>
    <div class="form-number">Nomor: {{ $user->id }}/SKL/PPDB/{{ date('Y') }}</div>

    <div style="margin-top: 20px;">
        <p>Panitia Penerimaan Peserta Didik Baru (PPDB) SDN NO. 200/VIII SUNGAI KARANG Tahun Pelajaran <b>{{ $activeYear->name ?? date('Y') }}</b>, dengan ini menerangkan bahwa:</p>
    </div>

    <table class="table-data">
        <tr>
            <td width="150">Kode Pendaftaran</td>
            <td width="10">:</td>
            <td><b>{{ $user->kode_pendaftaran }}</b></td>
        </tr>
        <tr>
            <td>Nama Lengkap</td>
            <td>:</td>
            <td>{{ $user->name }}</td>
        </tr>
        <tr>
            <td>Email Terdaftar</td>
            <td>:</td>
            <td>{{ $user->email }}</td>
        </tr>
        <tr>
            <td>Tanggal Registrasi</td>
            <td>:</td>
            <td>{{ $user->created_at->format('d F Y') }}</td>
        </tr>
    </table>

    <p>Berdasarkan hasil seleksi administrasi dan validasi data yang telah dilaksanakan, peserta didik tersebut dinyatakan:</p>

    <center>
        <div class="box-status">
            LULUS / DI TERIMA
        </div>
    </center>

    <p style="text-align: justify; line-height: 1.6;">
        Selanjutnya, bagi peserta yang dinyatakan lulus <b>WAJIB</b> melakukan <b>Daftar Ulang</b> 
        mulai tanggal <b>{{ $activeYear ? \Carbon\Carbon::parse($activeYear->mulai_daftar_ulang)->format('d F Y') : '-' }}</b> 
        sampai dengan <b>{{ $activeYear ? \Carbon\Carbon::parse($activeYear->selesai_daftar_ulang)->format('d F Y') : '-' }}</b>.
    </p>
    
    <p>Surat keterangan ini dapat digunakan sebagai bukti penerimaan saat melakukan daftar ulang. Demikian surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya.</p>

    <table class="ttd-table">
        <tr>
            <td width="60%"></td> 
            <td>
                Sungai Karang, {{ date('d F Y') }}<br>
                Ketua Panitia PPDB,<br><br><br><br><br>
                (_______________________)<br>
                NIP. ...........................
            </td>
        </tr>
    </table>

</body>
</html>