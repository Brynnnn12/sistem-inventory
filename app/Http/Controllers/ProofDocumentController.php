<?php

namespace App\Http\Controllers;

use App\Models\InboundTransaction;
use App\Models\OutboundTransaction;
use App\Models\StockMutation;
use Dompdf\Dompdf;

class ProofDocumentController extends Controller
{
    public function download(string $type, int $id)
    {
        abort_if(! in_array($type, ['inbound', 'outbound', 'mutation']), 404);

        $record = match ($type) {
            'inbound' => InboundTransaction::with(['supplier', 'warehouse', 'product', 'creator'])
                ->findOrFail($id),
            'outbound' => OutboundTransaction::with(['customer', 'warehouse', 'product', 'creator'])
                ->findOrFail($id),
            'mutation' => StockMutation::with(['fromWarehouse', 'toWarehouse', 'product', 'creator', 'receiver'])
                ->findOrFail($id),
        };

        $logoPath = public_path('images/logo.png');
        $logoData = $logoPath && file_exists($logoPath)
            ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
            : null;

        $view = "pdf.{$type}";

        $html = view($view, [$type => $record, 'logoData' => $logoData])->render();

        $dompdf = new Dompdf;
        $dompdf->loadHtml($html);
        $dompdf->setPaper([0, 0, 297.64, 467.72]);
        $dompdf->render();

        $filename = match ($type) {
            'inbound' => "Surat-Jalan-Masuk-{$record->code}.pdf",
            'outbound' => "Surat-Jalan-Keluar-{$record->code}.pdf",
            'mutation' => "Berita-Acara-Mutasi-{$record->code}.pdf",
        };

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
