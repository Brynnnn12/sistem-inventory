<?php

declare(strict_types=1);

namespace App\Actions\Opname;

use App\Models\Opname;
use InvalidArgumentException;

class DeleteOpnameAction
{
    public function execute(int $opnameId): void
    {
        $opname = Opname::findOrFail($opnameId);

        if ($opname->status !== 'rejected') {
            throw new InvalidArgumentException('Hanya opname dengan status ditolak yang dapat dihapus.');
        }

        $opname->delete();
    }
}
