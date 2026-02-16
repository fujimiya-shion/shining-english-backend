<?php

use App\Models\Lesson;
use App\Policies\LessonPolicy;

dataset('lessonPolicyPermissions', [
    ['viewAny', 'ViewAny:Lesson', false],
    ['view', 'View:Lesson', true],
    ['create', 'Create:Lesson', false],
    ['update', 'Update:Lesson', true],
    ['delete', 'Delete:Lesson', true],
    ['restore', 'Restore:Lesson', true],
    ['forceDelete', 'ForceDelete:Lesson', true],
    ['forceDeleteAny', 'ForceDeleteAny:Lesson', false],
    ['restoreAny', 'RestoreAny:Lesson', false],
    ['replicate', 'Replicate:Lesson', true],
    ['reorder', 'Reorder:Lesson', false],
]);

test('lesson policy checks the expected permission', function (string $method, string $permission, bool $needsModel): void {
    $policy = new LessonPolicy;

    $arguments = $needsModel ? [new Lesson] : [];

    assertPolicyChecksPermission($policy, $method, $permission, $arguments);
})->with('lessonPolicyPermissions');
