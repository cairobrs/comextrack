<?php

namespace App\Policies;

use App\Models\ImportStep;
use App\Models\User;

class ImportStepPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ImportStep $importStep): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ImportStep $importStep): bool
    {
        return true;
    }

    public function delete(User $user, ImportStep $importStep): bool
    {
        return true;
    }

    public function restore(User $user, ImportStep $importStep): bool
    {
        return true;
    }

    public function forceDelete(User $user, ImportStep $importStep): bool
    {
        return true;
    }
}
