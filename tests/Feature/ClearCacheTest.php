<?php

declare(strict_types=1);

use App\Models\User;
use Laravolt\Platform\Models\Role;
use Laravolt\Platform\Models\Permission;
use Illuminate\Support\Facades\Artisan;

test('guest cannot clear cache', function (): void {
    $this->post(route('clear-cache'))
        ->assertRedirect(route('auth::login.show'));
});

test('user without * permission cannot clear cache', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post(route('clear-cache'))
        ->assertStatus(403);
});

test('user with * permission can clear cache', function (): void {
    $role = Role::firstOrCreate(['name' => 'Admin']);
    $permission = Permission::firstOrCreate(['name' => '*']);
    $role->permissions()->syncWithoutDetaching([$permission->id]);

    $user = User::factory()->create();
    $user->assignRole($role);

    $this->actingAs($user);

    Artisan::spy();

    $this->post(route('clear-cache'))
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('success', 'Cache berhasil dibersihkan.');

    Artisan::shouldHaveReceived('call')
        ->with('cache:clear')
        ->once();
});
