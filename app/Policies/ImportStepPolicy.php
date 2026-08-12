<?php

namespace App\Policies;

use App\Models\ImportStep;
use App\Models\User;

class ImportStepPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, ImportStep $importStep): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, ImportStep $importStep): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, ImportStep $importStep): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, ImportStep $importStep): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, ImportStep $importStep): bool
    {
        return $user->isAdmin();
    }
}
