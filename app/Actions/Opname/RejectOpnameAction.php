<?php

declare(strict_types=1);

namespace App\Actions\Opname;

use App\Models\Opname;
use InvalidArgumentException;

class RejectOpnameAction
{
    public function execute(int $opnameId): Opname
    {
        $opname = Opname::findOrFail($opnameId);

        if ($opname->status !== 'draft') {
            throw new InvalidArgumentException('Opname hanya bisa ditolak jika status draft.');
        }

        $opname->update(['status' => 'rejected']);

        return $opname->load(['warehouse', 'product', 'creator']);
    }
}
