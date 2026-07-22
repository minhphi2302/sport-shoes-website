# Danh sách Task — Shop Giày Thể Thao

> Cập nhật: 2026-07-22 | Trạng thái tổng thể: **Tuần 1 — Khởi động**

---

## Dependency Graph (Thứ tự ưu tiên)

```
TASK-001 (DB Schema)
    ├── TASK-002 (Auth)          → Minh        [prerequisite: tất cả]
    ├── TASK-003 (Product UI)    → Hưng        [song song với 002]
    ├── TASK-007 (Admin CRUD)    → Minh        [sau 002]
    └── TASK-006 (Order Mgmt)   → Thảo        [sau 002, 005]

TASK-003 → TASK-004 (Cart) → TASK-005 (Checkout) → TASK-006

TASK-001, 002 → TASK-008 (Dashboard)     → Thảo
```

---

## Bảng tổng hợp

| Task | Tên | Người phụ trách | Phụ thuộc | Ưu tiên | Trạng thái |
|---|---|---|---|---|---|
| [TASK-001](./TASK-001-database-schema-migration.md) | Database Schema & Migration | Minh | — | 🔴 CRITICAL | To Do |
| [TASK-002](./TASK-002-authentication.md) | Đăng ký / Đăng nhập / Auth | Minh | 001 | 🔴 HIGH | To Do |
| [TASK-003](./TASK-003-product-listing-ui.md) | Trang chủ & Danh sách sản phẩm | Hưng | 001 | 🟡 HIGH | To Do |
| [TASK-004](./TASK-004-cart.md) | Giỏ hàng | Hưng | 001, 003 | 🟡 HIGH | To Do |
| [TASK-005](./TASK-005-checkout-order.md) | Đặt hàng / Checkout | Hưng | 002, 004 | 🟡 MEDIUM | To Do |
| [TASK-006](./TASK-006-order-management.md) | Lịch sử & Quản lý đơn hàng | Thảo | 002, 005 | 🟢 MEDIUM | To Do |
| [TASK-007](./TASK-007-admin-product-management.md) | Admin CRUD Sản phẩm/Danh mục/Thương hiệu | Minh | 001, 002 | 🟡 HIGH | To Do |
| [TASK-008](./TASK-008-dashboard-customer-mgmt.md) | Dashboard & Quản lý khách hàng | Thảo | 001, 002 | 🟢 LOW | To Do |

---

## Tuần 1 — Bắt đầu ngay

| Người | Task phải hoàn thành cuối tuần 1 |
|---|---|
| **Minh** | TASK-001 (DB migration) → TASK-002 (Auth) |
| **Hưng** | TASK-003 (Product listing UI) — dùng seed data từ TASK-001 |
| **Thảo** | Thiết kế UI đơn hàng/dashboard, đọc kỹ TASK-006 + TASK-008 để chuẩn bị |

---

## Quy ước khi cập nhật trạng thái task

Khi bắt đầu làm task, đổi trạng thái trong file task `.md`:
- `To Do` → `In Progress`
- `In Progress` → `Ready for Test` (sau khi tự test xong)
- `Ready for Test` → `Done` (sau khi có người review approve và merge)

Cập nhật bảng trong file này theo.

---

## Hướng dẫn dùng AI để execute task

Khi muốn AI bắt đầu code 1 task, dùng lệnh:

```
@AI /execute-task TASK-001
```

AI sẽ đọc file spec tương ứng trong `docs/tasks/`, hiểu Acceptance Criteria, và tiến hành implement theo đúng AGENTS.md.
