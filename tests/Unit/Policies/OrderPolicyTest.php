<?php

use App\Models\Order;
use App\Policies\OrderPolicy;

test('order policy checks permissions', function (): void {
    $policy = new OrderPolicy;
    $order = new Order;

    assertPolicyChecksPermission($policy, 'viewAny', 'ViewAny:Order');
    assertPolicyChecksPermission($policy, 'view', 'View:Order', [$order]);
    assertPolicyChecksPermission($policy, 'create', 'Create:Order');
    assertPolicyChecksPermission($policy, 'update', 'Update:Order', [$order]);
    assertPolicyChecksPermission($policy, 'delete', 'Delete:Order', [$order]);
    assertPolicyChecksPermission($policy, 'restore', 'Restore:Order', [$order]);
    assertPolicyChecksPermission($policy, 'forceDelete', 'ForceDelete:Order', [$order]);
    assertPolicyChecksPermission($policy, 'forceDeleteAny', 'ForceDeleteAny:Order');
    assertPolicyChecksPermission($policy, 'restoreAny', 'RestoreAny:Order');
    assertPolicyChecksPermission($policy, 'replicate', 'Replicate:Order', [$order]);
    assertPolicyChecksPermission($policy, 'reorder', 'Reorder:Order');
});
