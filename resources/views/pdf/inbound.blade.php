<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan - {{ $inbound->code }}</title>
    <style>
        @page { margin: 0.4cm; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 7px;
            color: #1f2937;
            line-height: 1.25;
        }
        .header {
            text-align: center;
            margin-bottom: 4px;
            padding-bottom: 3px;
            border-bottom: 2px double #1f2937;
        }
        .header img { max-height: 30px; margin-bottom: 3px; }
        .company-name { font-size: 10px; font-weight: bold; }
        .company-address { font-size: 6px; color: #6b7280; margin-top: 1px; }
        .doc-title {
            text-align: center;
            font-size: 8px;
            font-weight: bold;
            margin: 5px 0;
            padding: 3px 0;
            border-top: 1.5px solid #1f2937;
            border-bottom: 1.5px solid #1f2937;
        }
        .doc-info { margin-bottom: 5px; }
        .doc-info table { width: 100%; }
        .doc-info td { padding: 1px 0; vertical-align: top; }
        .doc-info .label { width: 80px; font-weight: 600; }
        .party-section {
            margin-bottom: 5px;
            padding: 5px;
            border: 1px solid #d1d5db;
            border-radius: 2px;
            background: #f9fafb;
        }
        .party-section .title { font-weight: bold; font-size: 6px; margin-bottom: 2px; color: #6b7280; }
        .party-section .name { font-size: 8px; font-weight: bold; }
        .party-section .detail { font-size: 6px; color: #4b5563; }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
        }
        table.items th {
            background: #1f2937;
            color: white;
            padding: 3px 3px;
            font-size: 6px;
            text-align: left;
            border: 1px solid #374151;
        }
        table.items th.text-right { text-align: right; }
        table.items th.text-center { text-align: center; }
        table.items td {
            padding: 2px 3px;
            border: 1px solid #d1d5db;
            vertical-align: top;
        }
        table.items td.text-right { text-align: right; }
        table.items td.text-center { text-align: center; }
        table.items .total-row td {
            font-weight: bold;
            background: #f3f4f6;
            border-top: 1.5px solid #1f2937;
        }
        .notes-section {
            margin: 4px 0;
            padding: 4px;
            border: 1px solid #d1d5db;
            border-radius: 2px;
            background: #fffbeb;
            font-size: 6px;
        }
        .notes-section .label { font-weight: bold; font-size: 6px; color: #92400e; }
        .signatures {
            margin-top: 10px;
            width: 100%;
        }
        .signatures td {
            width: 33.33%;
            text-align: center;
            padding-top: 20px;
            font-size: 6px;
        }
        .signatures .line {
            border-top: 1px solid #1f2937;
            margin: 0 10px;
            padding-top: 3px;
        }
        .footer {
            margin-top: 8px;
            padding-top: 4px;
            border-top: 1px solid #d1d5db;
            text-align: center;
            font-size: 5px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ $logoData }}" alt="Logo">
        <div class="company-name">{{ config('app.name') }}</div>
        <div class="company-address">Jl. Raya Gudang No. 1, Brebes | Telp: (021) 1234-5678</div>
    </div>

    <div class="doc-title">SURAT JALAN BARANG MASUK</div>

    <div class="doc-info">
        <table>
            <tr><td class="label">No. Bukti</td><td>: <strong>{{ $inbound->code }}</strong></td></tr>
            <tr><td class="label">Tanggal</td><td>: {{ \Carbon\Carbon::parse($inbound->received_date)->isoFormat('D MMMM YYYY') }}</td></tr>
            <tr><td class="label">Gudang</td><td>: {{ $inbound->warehouse->name }}</td></tr>
        </table>
    </div>

    <div class="party-section">
        <div class="title">DITERIMA DARI</div>
        <div class="name">{{ $inbound->supplier->name }}</div>
        @if($inbound->supplier->address)
            <div class="detail">{{ $inbound->supplier->address }}</div>
        @endif
        <div class="detail">
            Telp: {{ $inbound->supplier->phone ?? '-' }}
            @if($inbound->supplier->contact_person) | Kontak: {{ $inbound->supplier->contact_person }} @endif
        </div>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 35%;">Nama Barang</th>
                <th style="width: 10%;" class="text-center">Sat</th>
                <th style="width: 15%;" class="text-right">Qty</th>
                <th style="width: 18%;" class="text-right">Harga</th>
                <th style="width: 17%;" class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>{{ $inbound->product->name }}</td>
                <td class="text-center">{{ $inbound->product->unit }}</td>
                <td class="text-right">{{ number_format($inbound->quantity, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($inbound->unit_price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($inbound->quantity * $inbound->unit_price, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="5" class="text-right">TOTAL</td>
                <td class="text-right">Rp {{ number_format($inbound->quantity * $inbound->unit_price, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    @if($inbound->notes)
        <div class="notes-section">
            <div class="label">CATATAN</div>
            {{ $inbound->notes }}
        </div>
    @endif

    <table class="signatures">
        <tr>
            <td>
                <div>Penerima,</div>
                <div class="line">( ______________ )</div>
            </td>
            <td>
                <div>Pengirim,</div>
                <div class="line">( ______________ )</div>
            </td>
            <td>
                <div>Mengetahui,</div>
                <div class="line">( ______________ )</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        {{ config('app.name') }} — {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY HH:mm') }} | {{ $inbound->creator->name }}
    </div>
</body>
</html>
