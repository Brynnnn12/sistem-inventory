<?php

declare(strict_types=1);

namespace App\Actions\Transaction\Concerns;

use Illuminate\Database\Eloquent\Model;

trait GeneratesTransactionCode
{
    private function generateTransactionCode(string $prefix, Model $model): string
    {
        $date = now()->format('Ymd');

        $lastRecord = $model
            ->where('code', 'like', "{$prefix}-{$date}-%")
            ->orderBy('code', 'desc')
            ->first();

        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->code, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('%s-%s-%03d', $prefix, $date, $newNumber);
    }
}
