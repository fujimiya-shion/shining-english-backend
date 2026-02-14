<?php

use App\Models\Quiz;
use App\Policies\QuizPolicy;

dataset('quizPolicyPermissions', [
    ['viewAny', 'ViewAny:Quiz', false],
    ['view', 'View:Quiz', true],
    ['create', 'Create:Quiz', false],
    ['update', 'Update:Quiz', true],
    ['delete', 'Delete:Quiz', true],
    ['restore', 'Restore:Quiz', true],
    ['forceDelete', 'ForceDelete:Quiz', true],
    ['forceDeleteAny', 'ForceDeleteAny:Quiz', false],
    ['restoreAny', 'RestoreAny:Quiz', false],
    ['replicate', 'Replicate:Quiz', true],
    ['reorder', 'Reorder:Quiz', false],
]);

test('quiz policy checks the expected permission', function (string $method, string $permission, bool $needsModel): void {
    $policy = new QuizPolicy;

    $arguments = $needsModel ? [new Quiz] : [];

    assertPolicyChecksPermission($policy, $method, $permission, $arguments);
})->with('quizPolicyPermissions');
