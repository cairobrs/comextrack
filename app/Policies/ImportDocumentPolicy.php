<?php

namespace App\Policies;

use App\Models\ImportDocument;
use App\Models\User;

class ImportDocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, ImportDocument $importDocument): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, ImportDocument $importDocument): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, ImportDocument $importDocument): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, ImportDocument $importDocument): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, ImportDocument $importDocument): bool
    {
        return $user->isAdmin();
    }
}
