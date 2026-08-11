<?php

namespace App\Policies;

use App\Models\ImportCost;
use App\Models\User;

class ImportCostPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ImportCost $importCost): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ImportCost $importCost): bool
    {
        return true;
    }

    public function delete(User $user, ImportCost $importCost): bool
    {
        return true;
    }

    public function restore(User $user, ImportCost $importCost): bool
    {
        return true;
    }

    public function forceDelete(User $user, ImportCost $importCost): bool
    {
        return true;
    }
}
