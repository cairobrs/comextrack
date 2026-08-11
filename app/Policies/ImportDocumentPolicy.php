<?php

namespace App\Policies;

use App\Models\ImportDocument;
use App\Models\User;

class ImportDocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ImportDocument $importDocument): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ImportDocument $importDocument): bool
    {
        return true;
    }

    public function delete(User $user, ImportDocument $importDocument): bool
    {
        return true;
    }

    public function restore(User $user, ImportDocument $importDocument): bool
    {
        return true;
    }

    public function forceDelete(User $user, ImportDocument $importDocument): bool
    {
        return true;
    }
}
