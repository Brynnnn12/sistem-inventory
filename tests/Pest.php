<?php

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(function () {
        $this->withoutMiddleware(ValidateCsrfToken::class);
    })
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

expect()->extend('toHaveRole', function (string $roleName) {
    expect($this->value->hasRole($roleName))->toBeTrue();

    return $this;
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Create a user with specific role(s)
 */
function createUserWithRole(string|array $roles, array $attributes = [], bool $withWarehouse = false): User
{
    $user = User::factory()->create($attributes);

    $roleInstances = collect((array) $roles)->map(function ($roleName) {
        return Role::firstOrCreate(['name' => $roleName]);
    });

    $user->assignRole($roleInstances);

    if ($withWarehouse) {
        $warehouse = Warehouse::factory()->create();
        $user->warehouses()->attach($warehouse->id, [
            'assigned_at' => now(),
            'is_primary' => true,
        ]);
    }

    return $user;
}

/**
 * Create a super-admin user
 */
function createSuperAdmin(array $attributes = []): User
{
    return createUserWithRole('super-admin', $attributes);
}

/**
 * Create an admin user
 */
function createAdmin(array $attributes = [], bool $withWarehouse = true): User
{
    return createUserWithRole('admin', $attributes, $withWarehouse);
}

/**
 * Create a viewer user
 */
function createViewer(array $attributes = [], bool $withWarehouse = true): User
{
    return createUserWithRole('viewer', $attributes, $withWarehouse);
}

/**
 * Create multiple roles
 */
function createRoles(array $roles): Collection
{
    return collect($roles)->map(fn ($role) => Role::firstOrCreate(['name' => $role]));
}
