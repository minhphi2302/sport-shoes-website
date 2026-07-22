# TASK-[ID]: [Tên ngắn gọn của chức năng]

## 1. Thông tin chung
- **Trạng thái:** [To Do / In Progress / Ready for Test / Done]
- **Người được giao:** [Tên người phụ trách - VD: Hưng, Thảo]
- **Role AI khuyên dùng:** 🛒 Feature Developer (nếu code) / 🧪 QA Engineer (nếu viết test)
- **Ngày tạo:** [YYYY-MM-DD]

## 2. Mô tả yêu cầu (Yêu cầu nghiệp vụ)
[Mô tả chi tiết những gì người dùng cuối sẽ thấy và làm được, hoặc logic backend cần thực thi]

## 3. Tiêu chí nghiệm thu (Acceptance Criteria)
*(QA Engineer sẽ dùng phần này để viết test case, Dev dùng để code logic. Phải cụ thể, rõ ràng, có thể test được)*
- [ ] AC1: ...
- [ ] AC2: ...
- [ ] AC3: ...

## 4. Ghi chú kỹ thuật & Ràng buộc (Tech Notes)
- **Model/Bảng liên quan:** [VD: User, Order]
- **File dự kiến cần sửa:** [VD: app/Controllers/UserController.php]
- **Business Rule tham chiếu (theo AGENTS.md):** [Ghi rõ ràng buộc]

## 5. Security Checklist bắt buộc cho Task này
- [ ] Đã validate form input ở Controller
- [ ] Có chống SQL Injection (dùng Prepared Statement PDO)
- [ ] Có chống XSS (`htmlspecialchars`) khi hiển thị ra View
- [ ] Phân quyền (Auth/Role) hợp lý
