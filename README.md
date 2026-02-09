# Shining English Backend

## Tổng quan
Repository này là backend API cho Shining English, xây dựng trên Laravel 12 với PHP 8.3. Dự án áp dụng mô hình base Service/Repository và có unit test cho các layer cốt lõi và model.

## Yêu cầu
- PHP 8.3+
- Composer
- Node.js + npm (chỉ cần nếu build frontend assets)
- Một DB được Laravel hỗ trợ (MySQL/PostgreSQL/SQLite)

## Cài đặt
1. Cài dependencies PHP:

```bash
composer install
```

2. Tạo file môi trường:

```bash
cp .env.example .env
php artisan key:generate
```

3. Cấu hình DB trong `.env`, sau đó chạy migrations:

```bash
php artisan migrate
```

4. Chạy ứng dụng:

```bash
php artisan serve
```

## Cấu trúc dự án
Các layer chính trong dự án:
- `app/Repositories`:
  - `IRepository.php`: Contract repository cơ bản
  - `Repository.php`: Base repository với query helpers và pagination
- `app/Services`:
  - `IService.php`: Contract service cơ bản
  - `Service.php`: Base service (ủy quyền cho repository)
- `app/ValueObjects/QueryOption.php`:
  - Đóng gói options phân trang và eager-load

## Kiến trúc phân lớp
Backend theo mô hình phân lớp đơn giản để tách trách nhiệm rõ ràng và dễ test.

### Service Layer
- Mục đích: Điều phối business logic và workflow.
- Phụ thuộc: Interface repository (`IRepository`) và các service khác.
- Không nên: Truy cập DB/Eloquent trực tiếp.
- Base class: `app/Services/Service.php`.

### Repository Layer
- Mục đích: Đóng gói truy vấn dữ liệu.
- Phụ thuộc: Eloquent models và `QueryOption`.
- Base class: `app/Repositories/Repository.php`.
- Hành vi chung: `getAll`, `getBy`, `paginateAll`, `paginateBy`, `autoComplete`.

### Value Objects
- Mục đích: Đối tượng nhỏ để truyền options có cấu trúc.
- Ví dụ: `QueryOption` chứa `page`, `perPage`, `with`.
- Cách dùng: Truyền vào method của repository/service để chuẩn hóa query options.

### Ví dụ luồng xử lý
1. Controller nhận request.
2. Controller gọi method trong Service.
3. Service gọi Repository kèm `QueryOption`.
4. Repository trả models/collections/paginators về Service.
5. Service trả response DTO/resource cho Controller.

## Testing
Unit tests viết bằng Pest.

Chạy tất cả unit tests:

```bash
php artisan test --compact --testsuite=Unit
```

Chạy coverage:

```bash
php artisan test --compact --testsuite=Unit --coverage
```

Các unit test trong `tests/Unit/Models` tự động dùng `RefreshDatabase`.

## Ghi chú
- Base repository triển khai các hành vi query/pagination/auto-complete phổ biến.
- `QueryOption::getPage()` sẽ throw `TypeError` nếu gọi khi chưa set.
- Khi thay đổi base layer, hãy update unit tests tương ứng.
