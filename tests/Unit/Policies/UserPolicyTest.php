<?php

use App\Policies\UserPolicy;

dataset('userPolicyPermissions', [
    ['viewAny', 'ViewAny:User', false],
    ['view', 'View:User', false],
    ['create', 'Create:User', false],
    ['update', 'Update:User', false],
    ['delete', 'Delete:User', false],
    ['restore', 'Restore:User', false],
    ['forceDelete', 'ForceDelete:User', false],
    ['forceDeleteAny', 'ForceDeleteAny:User', false],
    ['restoreAny', 'RestoreAny:User', false],
    ['replicate', 'Replicate:User', false],
    ['reorder', 'Reorder:User', false],
]);

test('user policy checks the expected permission', function (string $method, string $permission, bool $needsModel): void {
    $policy = new UserPolicy;

    $arguments = $needsModel ? [new \App\Models\User] : [];

    assertPolicyChecksPermission($policy, $method, $permission, $arguments);
})->with('userPolicyPermissions');
