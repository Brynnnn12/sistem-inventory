<?php

namespace App\Exports;

use App\Services\ReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionReportExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithCustomStartCell,
    WithEvents
{
    protected array $data = [];

    public function __construct(
        protected string $type,
        protected $warehouseId,
        protected string $startDate,
        protected string $endDate,
        protected ?array $warehouseIds = null
    ) {
        if ($this->warehouseId === 'all') {
            $this->warehouseId = null;
        }

        $service = app(ReportService::class);

        $report = $service->getTransactionReport(
            $this->type,
            $this->warehouseId,
            $this->startDate,
            $this->endDate,
            $this->warehouseIds
        );

        $this->data = $report['data'] ?? [];
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            'Jenis',
            'Kode',
            'Tanggal',
            'Gudang',
            'Produk',
            'Relasi',
            'Qty',
            'Harga',
            'Total',
            'Status',
        ];
    }

    public function map($row): array
    {
        $type = match ($row['type'] ?? null) {
            'inbound' => 'Masuk',
            'outbound' => 'Keluar',
            'mutation' => 'Mutasi',
            default => '-',
        };

        $status = $row['status'] ?? '-';

        return [
            $type,
            $row['code'] ?? '-',
            $row['date'] ?? '-',
            $row['warehouse']
                ?? ($row['from_warehouse'] ?? '-'),
            $row['product'] ?? '-',
            $row['supplier']
                ?? ($row['customer']
                ?? ($row['to_warehouse'] ?? '-')),
            $row['quantity'] ?? 0,
            $row['unit_price']
                ?? ($row['received_qty'] ?? '-'),
            $row['total_price'] ?? '-',
            $status,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();

                $sheet->setCellValue('A1', 'PT Rizquna Berkah Mandiri');
                $sheet->setCellValue('A2', 'Laporan Transaksi');
                $sheet->setCellValue('A3', $this->startDate.' s/d '.$this->endDate);

                $sheet->mergeCells('A1:J1');
                $sheet->mergeCells('A2:J2');
                $sheet->mergeCells('A3:J3');

                $sheet->freezePane('A5');
                $sheet->setAutoFilter('A4:J4');

                $totalDataRows = count($this->data);
                $lastRow = $totalDataRows > 0 ? 4 + $totalDataRows : 5;

                if ($totalDataRows === 0) {
                    $sheet->setCellValue('A5', 'Tidak ada data transaksi pada periode ini.');
                    $sheet->mergeCells('A5:J5');
                }

                $sheet->getStyle('A1:J3')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '1F2937'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A1:J1')->getFont()->setSize(16);
                $sheet->getStyle('A2:J2')->getFont()->setSize(13);
                $sheet->getStyle('A3:J3')->getFont()->setSize(11);

                $sheet->getStyle('A4:J4')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '0F766E'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A4:J'.$lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D1D5DB'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                if ($totalDataRows > 0) {
                    $sheet->getStyle('C5:C'.$lastRow)
                        ->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);

                    $sheet->getStyle('G5:G'.$lastRow)
                        ->getNumberFormat()
                        ->setFormatCode('#,##0');

                    $sheet->getStyle('H5:I'.$lastRow)
                        ->getNumberFormat()
                        ->setFormatCode('#,##0');

                    $sheet->getStyle('A5:F'.$lastRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                    $sheet->getStyle('G5:I'.$lastRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                    $sheet->getStyle('J5:J'.$lastRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                $sheet->getRowDimension(1)->setRowHeight(26);
                $sheet->getRowDimension(2)->setRowHeight(22);
                $sheet->getRowDimension(3)->setRowHeight(20);
                $sheet->getRowDimension(4)->setRowHeight(22);
            },
        ];
    }
}
