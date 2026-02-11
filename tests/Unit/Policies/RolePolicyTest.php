<?php

use App\Policies\RolePolicy;
use Spatie\Permission\Models\Role;

dataset('rolePolicyPermissions', [
    ['viewAny', 'ViewAny:Role', false],
    ['view', 'View:Role', true],
    ['create', 'Create:Role', false],
    ['update', 'Update:Role', true],
    ['delete', 'Delete:Role', true],
    ['restore', 'Restore:Role', true],
    ['forceDelete', 'ForceDelete:Role', true],
    ['forceDeleteAny', 'ForceDeleteAny:Role', false],
    ['restoreAny', 'RestoreAny:Role', false],
    ['replicate', 'Replicate:Role', true],
    ['reorder', 'Reorder:Role', false],
]);

test('role policy checks the expected permission', function (string $method, string $permission, bool $needsModel): void {
    $policy = new RolePolicy;

    $arguments = $needsModel ? [new Role] : [];

    assertPolicyChecksPermission($policy, $method, $permission, $arguments);
})->with('rolePolicyPermissions');
