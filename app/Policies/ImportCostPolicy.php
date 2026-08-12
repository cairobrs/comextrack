<?php

namespace App\Policies;

use App\Models\ImportCost;
use App\Models\User;

class ImportCostPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, ImportCost $importCost): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, ImportCost $importCost): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, ImportCost $importCost): bool
    {
        return $user->isAdmin() && $importCost->isAdicional();
    }

    public function restore(User $user, ImportCost $importCost): bool
    {
        return false;
    }

    public function forceDelete(User $user, ImportCost $importCost): bool
    {
        return false;
    }
}
