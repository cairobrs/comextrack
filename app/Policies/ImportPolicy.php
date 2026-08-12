<?php

namespace App\Policies;

use App\Models\Import;
use App\Models\User;

class ImportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Import $import): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Import $import): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Import $import): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Import $import): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Import $import): bool
    {
        return $user->isAdmin();
    }
}
