<?php

use App\Models\User;
use Illuminate\Support\Facades\Mail;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

test('super-admin bisa melakukan CRUD dan bulk pada karyawan; role lain dilarang', function () {
    Mail::fake();

    $superAdmin = createSuperAdmin();

    // pastikan role tersetup dan buat beberapa karyawan dengan peran
    createRoles(['admin', 'viewer']);

    $adminA = createAdmin();
    $adminB = createAdmin();
    $viewerA = createViewer();

    $response = actingAs($superAdmin)->get(route('employees.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('employees/index')
            ->has('employees.data', 3)
            ->has('employees.data.0', fn ($u) => $u
                ->has('id')
                ->has('name')
                ->has('email')
                ->has('phone_number')
                ->has('roles')
                ->has('created_at')
            )
        );

    // store
    $payload = [
        'name' => 'Agus Santoso',
        'email' => 'agus.santoso@gmail.com',
        'phone_number' => '6281234567890',
        'password' => 'C0mpl3x!Passw0rd$2026',
        'password_confirmation' => 'C0mpl3x!Passw0rd$2026',
        'role' => 'admin',
    ];

    $response = actingAs($superAdmin)->post(route('employees.store'), $payload);

    $response->assertRedirect(route('employees.index'))
        ->assertSessionHas('success', 'Karyawan berhasil dibuat.');

    $employee = User::where('email', $payload['email'])->first();
    expect($employee)->not->toBeNull();
    expect($employee->hasRole('admin'))->toBeTrue();

    // update
    $updateData = [
        'name' => 'Agus Updated',
        'phone_number' => '6281112223334',
        'role' => 'viewer',
    ];

    $response = actingAs($superAdmin)->put(route('employees.update', $employee), $updateData);

    $response->assertRedirect(route('employees.index'))
        ->assertSessionHas('success', 'Karyawan berhasil diperbarui.');

    $employee->refresh();
    expect($employee->name)->toBe('Agus Updated');
    expect($employee->phone_number)->toBe('6281112223334');
    expect($employee->hasRole('viewer'))->toBeTrue();

    // destroy
    $toDelete = \App\Models\User::factory()->create();
    $response = actingAs($superAdmin)->delete(route('employees.destroy', $toDelete));
    $response->assertRedirect(route('employees.index'))
        ->assertSessionHas('success', 'Berhasil menghapus karyawan.');

    expect(User::find($toDelete->id))->toBeNull();
    expect(User::withTrashed()->find($toDelete->id))->not->toBeNull();

    // bulk destroy
    $batch = \App\Models\User::factory()->count(3)->create();
    $ids = $batch->pluck('id')->toArray();

    $response = actingAs($superAdmin)->delete(route('employees.bulk-destroy'), ['ids' => $ids]);
    $response->assertRedirect(route('employees.index'))
        ->assertSessionHas('success', 'Berhasil menghapus 3 karyawan.');

    foreach ($ids as $id) {
        expect(User::find($id))->toBeNull();
        expect(User::withTrashed()->find($id))->not->toBeNull();
    }

    // --- role checks: admin/viewer tidak boleh akses index/store/update/destroy/bulk ---
    $admin = createAdmin();
    $viewer = createViewer();

    // index
    actingAs($admin)->get(route('employees.index'))->assertForbidden();
    actingAs($viewer)->get(route('employees.index'))->assertForbidden();

    // store
    actingAs($admin)->post(route('employees.store'), $payload)->assertForbidden();
    actingAs($viewer)->post(route('employees.store'), $payload)->assertForbidden();

    // update
    actingAs($admin)->put(route('employees.update', $employee), ['name' => 'x'])->assertForbidden();
    actingAs($viewer)->put(route('employees.update', $employee), ['name' => 'x'])->assertForbidden();

    // destroy
    actingAs($admin)->delete(route('employees.destroy', $employee))->assertForbidden();
    actingAs($viewer)->delete(route('employees.destroy', $employee))->assertForbidden();

    // bulk destroy
    actingAs($admin)->delete(route('employees.bulk-destroy'), ['ids' => [$employee->id]])->assertForbidden();
    actingAs($viewer)->delete(route('employees.bulk-destroy'), ['ids' => [$employee->id]])->assertForbidden();
});
