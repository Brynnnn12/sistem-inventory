import {
    ResponsiveContainer,
    LineChart,
    Line,
    XAxis,
    YAxis,
    CartesianGrid,
    Tooltip,
    Legend,
} from 'recharts';
import type { MonthlyChartEntry } from '@/types/models/dashboard';

interface MonthlyChartProps {
    data: MonthlyChartEntry[];
}

export default function MonthlyChart({ data }: MonthlyChartProps) {
    return (
        <div className="h-64">
            <ResponsiveContainer width="100%" height="100%">
                <LineChart
                    data={data}
                    margin={{ top: 20, right: 30, left: 0, bottom: 0 }}
                >
                    <CartesianGrid strokeDasharray="3 3" />
                    <XAxis dataKey="month" />
                    <YAxis />
                    <Tooltip />
                    <Legend />
                    <Line
                        type="monotone"
                        dataKey="inbound"
                        stroke="var(--color-chart-1)"
                        name="Masuk"
                    />
                    <Line
                        type="monotone"
                        dataKey="outbound"
                        stroke="var(--color-chart-2)"
                        name="Keluar"
                    />
                </LineChart>
            </ResponsiveContainer>
        </div>
    );
}
