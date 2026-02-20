<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Admin;
use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('allows super admin to view order history list', function (): void {
    $admin = Admin::factory()->create();
    $roleName = config('filament-shield.super_admin.name', 'super_admin');

    $role = Role::query()->firstOrCreate([
        'name' => $roleName,
        'guard_name' => 'admin',
    ]);

    $admin->assignRole($role);

    $response = $this->actingAs($admin, 'admin')->get('/admin/orders');

    $response->assertStatus(200);
});

it('allows super admin to view order detail with items', function (): void {
    $admin = Admin::factory()->create();
    $roleName = config('filament-shield.super_admin.name', 'super_admin');

    $role = Role::query()->firstOrCreate([
        'name' => $roleName,
        'guard_name' => 'admin',
    ]);

    $admin->assignRole($role);

    $user = User::factory()->create();
    $course = Course::factory()->create([
        'price' => 120000,
    ]);

    $order = Order::query()->create([
        'user_id' => $user->id,
        'total_amount' => 240000,
        'status' => OrderStatus::Pending,
        'payment_method' => PaymentMethod::Cod,
        'placed_at' => now(),
    ]);

    OrderItem::query()->create([
        'order_id' => $order->id,
        'course_id' => $course->id,
        'quantity' => 2,
        'price' => 120000,
    ]);

    $response = $this->actingAs($admin, 'admin')->get("/admin/orders/{$order->id}");

    $response->assertStatus(200);
});
