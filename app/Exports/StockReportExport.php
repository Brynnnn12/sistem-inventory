<?php

namespace App\Exports;

use App\Services\ReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StockReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $warehouseId;

    protected $startDate;

    protected $endDate;

    protected $data;

    public function __construct(?int $warehouseId, string $startDate, string $endDate)
    {
        $this->warehouseId = $warehouseId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;

        $service = new ReportService;
        $report = $service->getStockReport($warehouseId, $startDate, $endDate);
        $this->data = $report['data'];
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
            'Warehouse',
            'Product Code',
            'Product Name',
            'Unit',
            'Quantity',
            'Reserved Qty',
            'Available Qty',
            'Min Stock',
            'Max Stock',
            'Cost',
            'Value',
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
            $row['status'],
        ];
    }
}
