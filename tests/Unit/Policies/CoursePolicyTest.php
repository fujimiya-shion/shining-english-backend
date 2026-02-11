<?php

use App\Models\Course;
use App\Policies\CoursePolicy;

dataset('coursePolicyPermissions', [
    ['viewAny', 'ViewAny:Course', false],
    ['view', 'View:Course', true],
    ['create', 'Create:Course', false],
    ['update', 'Update:Course', true],
    ['delete', 'Delete:Course', true],
    ['restore', 'Restore:Course', true],
    ['forceDelete', 'ForceDelete:Course', true],
    ['forceDeleteAny', 'ForceDeleteAny:Course', false],
    ['restoreAny', 'RestoreAny:Course', false],
    ['replicate', 'Replicate:Course', true],
    ['reorder', 'Reorder:Course', false],
]);

test('course policy checks the expected permission', function (string $method, string $permission, bool $needsModel): void {
    $policy = new CoursePolicy;

    $arguments = $needsModel ? [new Course] : [];

    assertPolicyChecksPermission($policy, $method, $permission, $arguments);
})->with('coursePolicyPermissions');
