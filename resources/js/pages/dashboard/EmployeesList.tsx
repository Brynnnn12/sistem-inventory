import { Users, User } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { formatDate } from '@/lib/utils';

interface Employee {
    id: number;
    name: string;
    email: string;
    role?: string;
    is_active: boolean;
    created_at: string;
}

interface EmployeesListProps {
    employees: Employee[];
}

export function EmployeesList({ employees }: EmployeesListProps) {
    return (
        <Card>
            <CardHeader>
                <CardTitle className="flex items-center gap-2">
                    <Users className="h-5 w-5" />
                    Karyawan Terbaru
                </CardTitle>
                <CardDescription>
                    {employees.length} karyawan terdaftar dalam sistem
                </CardDescription>
            </CardHeader>
            <CardContent>
                <div className="space-y-4">
                    {employees.length === 0 ? (
                        <div className="py-8 text-center text-muted-foreground">
                            Belum ada karyawan terdaftar
                        </div>
                    ) : (
                        employees.slice(0, 5).map((employee) => (
                            <div
                                key={employee.id}
                                className="flex items-center justify-between rounded-lg border p-3"
                            >
                                <div className="flex items-center gap-3">
                                    <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-muted">
                                        <User className="h-5 w-5 text-muted-foreground" />
                                    </div>
                                    <div>
                                        <div className="font-medium">
                                            {employee.name}
                                        </div>
                                        <div className="flex items-center gap-2 text-sm text-muted-foreground">
                                            <span>{employee.email}</span>
                                            {employee.role && (
                                                <>
                                                    <span>•</span>
                                                    <Badge
                                                        variant="outline"
                                                        className="text-xs"
                                                    >
                                                        {employee.role}
                                                    </Badge>
                                                </>
                                            )}
                                        </div>
                                    </div>
                                </div>
                                <div className="text-right">
                                    <Badge
                                        variant={
                                            employee.is_active
                                                ? 'default'
                                                : 'secondary'
                                        }
                                        className="text-xs"
                                    >
                                        {employee.is_active
                                            ? 'Aktif'
                                            : 'Tidak Aktif'}
                                    </Badge>
                                    <div className="mt-1 text-xs text-muted-foreground">
                                        {formatDate(employee.created_at)}
                                    </div>
                                </div>
                            </div>
                        ))
                    )}
                </div>
            </CardContent>
        </Card>
    );
}
