<?php

declare(strict_types=1);

namespace App\Actions\WarehouseUsers;

use App\Models\WarehouseUser;
use InvalidArgumentException;

class BulkDeleteWarehouseUsersAction
{
    /**
     * @param  array<int>  $ids
     */
    public function execute(array $ids): int
    {
        if (empty($ids)) {
            throw new InvalidArgumentException('Tidak ada ID yang dipilih untuk dihapus');
        }

        return WarehouseUser::whereIn('id', $ids)->delete();
    }
}
