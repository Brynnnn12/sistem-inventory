<?php

namespace App\Actions\Employee;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateEmployeeAction
{
    /**
     * Update the employee (user) information and role.
     *
     * @param  array<string, mixed>  $input
     */
    public function execute(User $user, array $input): User
    {
        return DB::transaction(function () use ($user, $input) {
            $update = [];

            if (array_key_exists('name', $input)) {
                $update['name'] = $input['name'];
            }

            if (array_key_exists('email', $input)) {
                $update['email'] = $input['email'];
            }

            if (array_key_exists('phone_number', $input)) {
                $update['phone_number'] = $input['phone_number'];
            }

            if (! empty($update)) {
                $user->forceFill($update)->save();
            }

            // Check if role needs update
            if (isset($input['role']) && in_array($input['role'], ['admin', 'viewer'])) {
                $user->syncRoles([$input['role']]);
            }

            return $user;
        });
    }
}
