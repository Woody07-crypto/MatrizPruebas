<?php

use App\Models\User;
use App\Policies\LoanPolicy;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'estudiante', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'profesor', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'bibliotecario', 'guard_name' => 'web']);
});

it('store_permite_prestar_segun_rol_autorizado', function () {
    $user = User::factory()->create()->assignRole('bibliotecario');
    $policy = new LoanPolicy();

    expect($policy->create($user))->toBeTrue();
});

it('store_deniega_prestar_segun_rol_no_autorizado', function () {
    $user = User::factory()->create()->assignRole('estudiante');
    $policy = new LoanPolicy();

    expect($policy->create($user))->toBeFalse();
});

it('view_any_permite_listar_prestamos_a_roles_autorizados', function () {
    $user = User::factory()->create()->assignRole('bibliotecario');
    $policy = new LoanPolicy();

    expect($policy->viewAny($user))->toBeTrue();
});

it('view_any_deniega_listar_prestamos_a_estudiante', function () {
    $user = User::factory()->create()->assignRole('estudiante');
    $policy = new LoanPolicy();

    expect($policy->viewAny($user))->toBeFalse();
});

