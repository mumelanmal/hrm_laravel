<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        return $user !== null; // any authenticated user
    }

    public function view(User $user, Employee $employee): bool
    {
        return true; // allow view if authenticated via middleware
    }

    public function create(User $user): bool
    {
        return (bool) ($user->is_admin ?? false);
    }

    public function update(User $user, Employee $employee): bool
    {
        return (bool) ($user->is_admin ?? false);
    }

    public function delete(User $user, Employee $employee): bool
    {
        return (bool) ($user->is_admin ?? false);
    }

    public function restore(User $user, Employee $employee): bool
    {
        return (bool) ($user->is_admin ?? false);
    }

    public function forceDelete(User $user, Employee $employee): bool
    {
        return (bool) ($user->is_admin ?? false);
    }
}
