<?php

declare(strict_types=1);

namespace App\Actions\WarehouseUsers;

use App\Models\WarehouseUser;

class DeleteWarehouseUserAction
{
    public function execute(WarehouseUser $warehouseUser): void
    {
        $warehouseUser->delete();
    }
}
