<?php

namespace App\Exports;

use App\Services\ReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $type;

    protected $warehouseId;

    protected $startDate;

    protected $endDate;

    protected $data;

    public function __construct(string $type, ?int $warehouseId, string $startDate, string $endDate)
    {
        $this->type = $type;
        $this->warehouseId = $warehouseId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;

        $service = new ReportService;
        $report = $service->getTransactionReport($type, $warehouseId, $startDate, $endDate);
        $this->data = $report['data'];
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        if ($this->type === 'inbound') {
            return [
                'Type',
                'Code',
                'Date',
                'Warehouse',
                'Product',
                'Supplier',
                'Quantity',
                'Unit Price',
                'Total Price',
            ];
        } elseif ($this->type === 'outbound') {
            return [
                'Type',
                'Code',
                'Date',
                'Warehouse',
                'Product',
                'Customer',
                'Quantity',
                'Unit Price',
                'Total Price',
            ];
        } elseif ($this->type === 'mutation') {
            return [
                'Type',
                'Code',
                'Date',
                'From Warehouse',
                'To Warehouse',
                'Product',
                'Quantity',
                'Received Qty',
                'Status',
            ];
        } else {
            // For 'all' type, use combined headings
            return [
                'Type',
                'Code',
                'Date',
                'Warehouse/From',
                'Product',
                'Supplier/Customer/To',
                'Quantity',
                'Unit Price/Received Qty',
                'Total Price/Status',
            ];
        }
    }

    public function map($row): array
    {
        if ($this->type === 'inbound') {
            return [
                $row['type'],
                $row['code'],
                $row['date'],
                $row['warehouse'],
                $row['product'],
                $row['supplier'],
                $row['quantity'],
                $row['unit_price'],
                $row['total_price'],
            ];
        } elseif ($this->type === 'outbound') {
            return [
                $row['type'],
                $row['code'],
                $row['date'],
                $row['warehouse'],
                $row['product'],
                $row['customer'],
                $row['quantity'],
                $row['unit_price'],
                $row['total_price'],
            ];
        } elseif ($this->type === 'mutation') {
            return [
                $row['type'],
                $row['code'],
                $row['date'],
                $row['from_warehouse'],
                $row['to_warehouse'],
                $row['product'],
                $row['quantity'],
                $row['received_qty'],
                $row['status'],
            ];
        } else {
            // For 'all' type
            if ($row['type'] === 'inbound') {
                return [
                    $row['type'],
                    $row['code'],
                    $row['date'],
                    $row['warehouse'],
                    $row['product'],
                    $row['supplier'],
                    $row['quantity'],
                    $row['unit_price'],
                    $row['total_price'],
                ];
            } elseif ($row['type'] === 'outbound') {
                return [
                    $row['type'],
                    $row['code'],
                    $row['date'],
                    $row['warehouse'],
                    $row['product'],
                    $row['customer'],
                    $row['quantity'],
                    $row['unit_price'],
                    $row['total_price'],
                ];
            } else {
                return [
                    $row['type'],
                    $row['code'],
                    $row['date'],
                    $row['from_warehouse'],
                    $row['product'],
                    $row['to_warehouse'],
                    $row['quantity'],
                    $row['received_qty'],
                    $row['status'],
                ];
            }
        }
    }
}
