<?php

namespace App\Exports;

use App\Services\ReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class StockReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithCustomStartCell, ShouldAutoSize, WithEvents
{
    protected ?int $warehouseId;

    protected string $startDate;

    protected string $endDate;

    protected ?array $warehouseIds;

    protected mixed $data;

    public function __construct(?int $warehouseId, string $startDate, string $endDate, ?array $warehouseIds = null)
    {
        $this->warehouseId = $warehouseId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->warehouseIds = $warehouseIds;

        $service = app(ReportService::class);
        $report = $service->getStockReport($warehouseId, $startDate, $endDate, $warehouseIds);
        $this->data = $report['data'];
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function collection()
    {
        if ($this->warehouseId) {
            return collect($this->data);
        } else {
            $flattened = [];
            foreach ($this->data as $warehouse => $stocks) {
                foreach ($stocks as $stock) {
                    $flattened[] = $stock;
                }
            }

            return collect($flattened);
        }
    }

    public function headings(): array
    {
        return [
            'Gudang',
            'Kode Produk',
            'Nama Produk',
            'Satuan',
            'Qty',
            'Qty Dipesan',
            'Qty Tersedia',
            'Min Stock',
            'Max Stock',
            'Harga',
            'Nilai',
            'Status',
        ];
    }

    public function map($row): array
    {
        return [
            $row['warehouse_name'],
            $row['product_code'],
            $row['product_name'],
            $row['unit'],
            $row['quantity'],
            $row['reserved_qty'],
            $row['available_qty'],
            $row['min_stock'],
            $row['max_stock'],
            $row['cost'],
            $row['value'],
            match ($row['status']) {
                'out_of_stock' => 'Habis',
                'low_stock' => 'Stok Rendah',
                default => 'Normal',
            },
        ];
    }

    public function title(): string
    {
        return 'Laporan Stok - PT Rizquna Berkah Mandiri';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();

                $sheet->setCellValue('A1', 'PT Rizquna Berkah Mandiri');
                $sheet->setCellValue('A2', 'Laporan Stok');
                $sheet->setCellValue('A3', $this->startDate.' s/d '.$this->endDate);

                $sheet->mergeCells('A1:L1');
                $sheet->mergeCells('A2:L2');
                $sheet->mergeCells('A3:L3');

                $sheet->freezePane('A5');
                $sheet->setAutoFilter('A4:L4');

                $totalDataRows = $this->collection()->count();
                $lastRow = $totalDataRows > 0 ? 4 + $totalDataRows : 5;

                if ($totalDataRows === 0) {
                    $sheet->setCellValue('A5', 'Tidak ada data stok pada periode ini.');
                    $sheet->mergeCells('A5:L5');
                }

                $sheet->getStyle('A1:L3')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '1F2937'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A1:L1')->getFont()->setSize(16);
                $sheet->getStyle('A2:L2')->getFont()->setSize(13);
                $sheet->getStyle('A3:L3')->getFont()->setSize(11);

                $sheet->getStyle('A4:L4')->applyFromArray([
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

                $sheet->getStyle('A4:L'.$lastRow)->applyFromArray([
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
                    $sheet->getStyle('E5:I'.$lastRow)
                        ->getNumberFormat()
                        ->setFormatCode('#,##0');

                    $sheet->getStyle('J5:K'.$lastRow)
                        ->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                    $sheet->getStyle('A5:D'.$lastRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                    $sheet->getStyle('E5:K'.$lastRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                    $sheet->getStyle('L5:L'.$lastRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    for ($row = 5; $row <= $lastRow; $row++) {
                        $status = strtolower((string) $sheet->getCell('L'.$row)->getValue());

                        $rowStyle = match ($status) {
                            'habis' => [
                                'fill' => ['rgb' => 'FEE2E2'],
                                'font' => ['rgb' => '991B1B'],
                            ],
                            'stok rendah' => [
                                'fill' => ['rgb' => 'FEF3C7'],
                                'font' => ['rgb' => '92400E'],
                            ],
                            default => [
                                'fill' => ['rgb' => 'ECFDF5'],
                                'font' => ['rgb' => '065F46'],
                            ],
                        };

                        $sheet->getStyle('A'.$row.':L'.$row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => $rowStyle['fill']['rgb']],
                            ],
                            'font' => [
                                'color' => ['rgb' => $rowStyle['font']['rgb']],
                            ],
                        ]);
                    }
                }

                $sheet->getRowDimension(1)->setRowHeight(26);
                $sheet->getRowDimension(2)->setRowHeight(22);
                $sheet->getRowDimension(3)->setRowHeight(20);
                $sheet->getRowDimension(4)->setRowHeight(22);
            },
        ];
    }
}
