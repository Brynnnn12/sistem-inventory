import { Filter } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

interface Warehouse {
    id: number;
    name: string;
}

interface Filters {
    warehouse_id?: string;
    start_date?: string;
    end_date?: string;
}

interface StockReportToolbarProps {
    selectedWarehouse: string;
    onWarehouseChange: (value: string) => void;
    startDate: string;
    onStartDateChange: (value: string) => void;
    endDate: string;
    onEndDateChange: (value: string) => void;
    onApplyFilter: () => void;
    warehouses: Warehouse[];
    filters: Filters;
}

export function StockReportToolbar({
    selectedWarehouse,
    onWarehouseChange,
    startDate,
    onStartDateChange,
    endDate,
    onEndDateChange,
    onApplyFilter,
    warehouses,
}: StockReportToolbarProps) {
    return (
        <Card>
            <CardHeader>
                <CardTitle className="flex items-center gap-2">
                    <Filter className="h-5 w-5" />
                    Filter
                </CardTitle>
            </CardHeader>
            <CardContent>
                <div className="flex gap-4">
                    <div className="flex-1">
                        <label className="text-sm font-medium">Gudang</label>
                        <Select
                            value={selectedWarehouse}
                            onValueChange={onWarehouseChange}
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="Semua Gudang" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">
                                    Semua Gudang
                                </SelectItem>
                                {warehouses.map((warehouse) => (
                                    <SelectItem
                                        key={warehouse.id}
                                        value={warehouse.id.toString()}
                                    >
                                        {warehouse.name}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </div>
                    <div className="flex-1">
                        <label className="text-sm font-medium">
                            Tanggal Mulai
                        </label>
                        <Input
                            type="date"
                            value={startDate}
                            onChange={(e) => onStartDateChange(e.target.value)}
                        />
                    </div>
                    <div className="flex-1">
                        <label className="text-sm font-medium">
                            Tanggal Akhir
                        </label>
                        <Input
                            type="date"
                            value={endDate}
                            onChange={(e) => onEndDateChange(e.target.value)}
                        />
                    </div>
                    <div className="flex items-end">
                        <Button onClick={onApplyFilter}>Terapkan Filter</Button>
                    </div>
                </div>
            </CardContent>
        </Card>
    );
}
