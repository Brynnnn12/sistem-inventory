<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara - {{ $mutation->code }}</title>
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
        .warehouse-section {
            display: flex;
            margin-bottom: 5px;
        }
        .warehouse-box {
            flex: 1;
            padding: 5px;
            border: 1px solid #d1d5db;
            border-radius: 2px;
            background: #f9fafb;
        }
        .warehouse-box .title { font-weight: bold; font-size: 6px; margin-bottom: 2px; color: #6b7280; }
        .warehouse-box .name { font-size: 7px; font-weight: bold; }
        .warehouse-box .detail { font-size: 6px; color: #4b5563; }
        .warehouse-box.origin { margin-right: 4px; }
        .warehouse-box.destination { margin-left: 4px; }
        .arrow {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
            color: #6b7280;
            padding: 0 6px;
        }
        .details-grid { margin: 5px 0; }
        .details-grid table { width: 100%; }
        .details-grid td { padding: 1px 0; vertical-align: top; }
        .details-grid .lbl { width: 100px; font-weight: 600; color: #4b5563; }
        .status-badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 6px;
            font-size: 6px;
            font-weight: bold;
        }
        .status-dikirim { background: #dbeafe; color: #1d4ed8; }
        .status-diterima { background: #d1fae5; color: #059669; }
        .status-ditolak { background: #fee2e2; color: #dc2626; }
        .status-selesai { background: #f3e8ff; color: #7c3aed; }
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
        <div class="company-address">Jl. Raya Gudang No. 1, Jakarta | Telp: (021) 1234-5678</div>
    </div>

    <div class="doc-title">BERITA ACARA MUTASI BARANG</div>

    <div class="doc-info">
        <table>
            <tr><td class="label">No. Bukti</td><td>: <strong>{{ $mutation->code }}</strong></td></tr>
            <tr><td class="label">Tgl Kirim</td><td>: {{ $mutation->sent_at ? \Carbon\Carbon::parse($mutation->sent_at)->isoFormat('D MMM YYYY HH:mm') : '-' }}</td></tr>
            @if($mutation->received_at)
            <tr><td class="label">Tgl Terima</td><td>: {{ \Carbon\Carbon::parse($mutation->received_at)->isoFormat('D MMM YYYY HH:mm') }}</td></tr>
            @endif
            <tr><td class="label">Status</td><td>: <span class="status-badge status-{{ $mutation->status_display }}">{{ $mutation->status_display === 'sent' ? 'Dikirim' : ($mutation->status_display === 'received' ? 'Diterima' : ($mutation->status_display === 'rejected' ? 'Ditolak' : 'Selesai')) }}</span></td></tr>
        </table>
    </div>

    <div class="warehouse-section">
        <div class="warehouse-box origin">
            <div class="title">GUDANG ASAL</div>
            <div class="name">{{ $mutation->fromWarehouse->name }}</div>
            @if($mutation->fromWarehouse->address)
                <div class="detail">{{ $mutation->fromWarehouse->address }}</div>
            @endif
            @if($mutation->fromWarehouse->phone)
                <div class="detail">Telp: {{ $mutation->fromWarehouse->phone }}</div>
            @endif
        </div>
        <div class="arrow">→</div>
        <div class="warehouse-box destination">
            <div class="title">GUDANG TUJUAN</div>
            <div class="name">{{ $mutation->toWarehouse->name }}</div>
            @if($mutation->toWarehouse->address)
                <div class="detail">{{ $mutation->toWarehouse->address }}</div>
            @endif
            @if($mutation->toWarehouse->phone)
                <div class="detail">Telp: {{ $mutation->toWarehouse->phone }}</div>
            @endif
        </div>
    </div>

    <div class="details-grid">
        <table>
            <tr><td class="lbl">Nama Barang</td><td>: <strong>{{ $mutation->product->name }}</strong></td></tr>
            <tr><td class="lbl">Satuan</td><td>: {{ $mutation->product->unit }}</td></tr>
            <tr><td class="lbl">Jml Dikirim</td><td>: {{ number_format($mutation->quantity, 0, ',', '.') }}</td></tr>
            @if($mutation->status_display !== 'sent')
            <tr><td class="lbl">Jml Diterima</td><td>: {{ number_format($mutation->received_qty, 0, ',', '.') }}</td></tr>
            @endif
            @if($mutation->damaged_qty > 0)
            <tr><td class="lbl">Jml Rusak</td><td>: {{ number_format($mutation->damaged_qty, 0, ',', '.') }}</td></tr>
            @endif
            <tr><td class="lbl">Pengirim</td><td>: {{ $mutation->creator->name }}</td></tr>
            @if($mutation->receiver)
            <tr><td class="lbl">Penerima</td><td>: {{ $mutation->receiver->name }}</td></tr>
            @endif
        </table>
    </div>

    @if($mutation->notes)
        <div class="notes-section">
            <div class="label">CATATAN</div>
            {{ $mutation->notes }}
        </div>
    @endif

    <table class="signatures">
        <tr>
            <td>
                <div>Pengirim,</div>
                <div class="line">( ______________ )</div>
            </td>
            <td>
                <div>Penerima,</div>
                <div class="line">( ______________ )</div>
            </td>
            <td>
                <div>Mengetahui,</div>
                <div class="line">( ______________ )</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        {{ config('app.name') }} — {{ \Carbon\Carbon::now()->isoFormat('D MMM YYYY HH:mm') }} | {{ $mutation->creator->name }}
    </div>
</body>
</html>
