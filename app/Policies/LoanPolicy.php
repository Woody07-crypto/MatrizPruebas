<?php

namespace App\Policies;

use App\Models\User;

class LoanPolicy
{
    /**
     * Determina si el usuario puede crear un préstamo.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['bibliotecario', 'profesor']);
    }

    /**
     * Determina si el usuario puede ver el historial/listado de préstamos.
     */
    public function viewAny(User $user): bool
    {
        return $this->create($user);
    }
}

