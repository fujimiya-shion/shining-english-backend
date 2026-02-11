<?php

use App\Models\Category;
use App\Policies\CategoryPolicy;

dataset('categoryPolicyPermissions', [
    ['viewAny', 'ViewAny:Category', false],
    ['view', 'View:Category', true],
    ['create', 'Create:Category', false],
    ['update', 'Update:Category', true],
    ['delete', 'Delete:Category', true],
    ['restore', 'Restore:Category', true],
    ['forceDelete', 'ForceDelete:Category', true],
    ['forceDeleteAny', 'ForceDeleteAny:Category', false],
    ['restoreAny', 'RestoreAny:Category', false],
    ['replicate', 'Replicate:Category', true],
    ['reorder', 'Reorder:Category', false],
]);

test('category policy checks the expected permission', function (string $method, string $permission, bool $needsModel): void {
    $policy = new CategoryPolicy;

    $arguments = $needsModel ? [new Category] : [];

    assertPolicyChecksPermission($policy, $method, $permission, $arguments);
})->with('categoryPolicyPermissions');
