# HƯỚNG DẪN HOÀN THIỆN TÀI LIỆU DỰ ÁN & NỘP TESTCASE

Tài liệu này hướng dẫn chi tiết cách chuẩn bị báo cáo, slide thuyết trình và **cách nộp/trình bày phần Testcase cho Giảng viên chấm bảo vệ đồ án**.

---

## 📋 1. HƯỚNG DẪN NỘP VÀ TRÌNH BÀY TESTCASE CHO GIẢNG VIÊN

### A. Hình thức nộp file Testcase (Nộp trước / Nộp kèm Source Code)
1. **Nộp file mềm (File `.md` hoặc `.xlsx`):**
   - Đặt file `testcase.md` ở thư mục gốc của repository dự án.
   - **Mẹo lấy điểm cộng:** Copy toàn bộ nội dung từ `testcase.md` sang file **Excel (`Testcase_Duan1_ShopGiay.xlsx`)**. Giảng viên thường thích xem file Excel vì có thể dễ dàng lọc (Filter) theo `Module`, `Status (PASS/FAIL)`, `Mức độ (DỄ/TRUNG BÌNH/KHÓ)`.
2. **Nộp trong Báo cáo bản cứng (Quyển báo cáo in ấn):**
   - **Tại Phần 5 (Kiểm lỗi):** Trình bày phương pháp kiểm thử + **Bảng tổng quan 95 Testcase** + **Bảng trích dẫn 10–15 Testcase tiêu biểu nhất** (gồm Auth, Cart, Order COD, Race condition, Security, Upload logo, Variants).
   - **Tại Phần Phụ lục (Cuối quyển báo cáo):** Có thể in toàn bộ danh mục 95 testcases nếu giảng viên yêu cầu báo cáo chi tiết.

---

### B. Cách trình bày phần Testcase trên Slide thuyết trình (Slide 10)
- Trình bày con số thống kê ấn tượng: **Tổng số 95 Testcases**
  - 🟢 **DỄ (48 testcase - 50%):** Form validation, UI rendering, luồng mua hàng cơ bản.
  - 🟡 **TRUNG BÌNH (28 testcase - 30%):** Rate limit đăng nhập, session timeout, phân trang, upload security.
  - 🔴 **KHÓ (19 testcase - 20%):** Race condition trừ tồn kho, Transaction rollback đa bước, SQL Injection, XSS protection.

---

### C. Kịch bản trả lời khi Giảng viên yêu cầu Demo Testcase trực tiếp
Khi hội đồng giảng viên yêu cầu: *"Hãy test trực tiếp một số tính năng/testcase quan trọng"*, nhóm tiến hành kịch bản demo 3 bước:

1. **Demo Testcase Bảo mật & Mật khẩu yếu (`TC-AUTH-003` & `TC-AUTH-008`):**
   - Mở trang Đăng ký / Đổi mật khẩu. Gõ mật khẩu `123456` -> Cho giảng viên thấy **Checklist báo lỗi real-time** (yêu cầu ít nhất 8 ký tự, 1 chữ hoa, 1 chữ thường, 1 ký tự đặc biệt).
   - Thử đăng nhập sai 5 lần liên tiếp -> Cho giảng viên thấy thông báo bị **Rate limit khóa đăng nhập trong 15 phút**.

2. **Demo Testcase Xử lý Đơn hàng & Transaction (`TC-ORDER-002`):**
   - Đặt hàng thanh toán COD -> Cho giảng viên thấy quá trình bọc **Transaction atomic** (Tạo Order + Tạo OrderDetail + Trừ tồn kho đồng thời, nếu 1 bước lỗi sẽ rollback sạch sẽ).

3. **Demo Testcase Nâng cao - Race Condition (`TC-ORDER-006`):**
   - Mở 2 trình duyệt ẩn danh (ví dụ 1 Chrome, 1 Edge) cùng đăng nhập 2 tài khoản.
   - Chọn 1 sản phẩm còn tồn kho đúng = 1. Cùng bấm "Đặt hàng" đồng thời ở cả 2 trình duyệt.
   - **Kết quả:** 1 tài khoản đặt thành công, tài khoản còn lại báo lỗi *"Sản phẩm không đủ tồn kho"*.

---

## 🎨 2. HƯỚNG DẪN HOÀN THIỆN SLIDE THUYẾT TRÌNH (SLIDE_THUYET_TRINH.md)

1. **Chụp screenshots thực tế từ website:**
   - Trang chủ (Home)
   - Danh sách & Chi tiết sản phẩm (Product list / detail)
   - Giỏ hàng & Form Checkout COD
   - Trang cá nhân Đổi mật khẩu
   - Admin Dashboard, Admin Product Matrix & Upload logo Thương hiệu
2. **Chỉnh sửa Slide 8 (DEMO GIAO DIỆN):** Thay thế các gạch đầu dòng bằng hình chụp giao diện thực tế.
3. **Xuất file Slide:** Copy nội dung từ `SLIDE_THUYET_TRINH.md` vào phần mềm PowerPoint hoặc Canva để trình chiếu.

---

## 🖨️ 3. HƯỚNG DẪN IN BÁO CÁO BẢN CỨNG (BAO_CAO_DU_AN.md)

1. **Định dạng in:**
   - Giấy A4 (70gsm), in 2 mặt đen trắng.
   - Font chữ Times New Roman / Arial size 13pt, giãn dòng 1.3 - 1.5.
   - Đóng bìa mica nhựa trong (mặt trước), bìa xanh (mặt sau) đóng gáy xoắn.
2. **Cấu trúc quyển báo cáo (Đã hoàn thiện đầy đủ 7 phần):**
   - Lời mở đầu & Mục lục
   - Phần 1: Giới thiệu đề tài
   - Phần 2: Phân tích yêu cầu & Business Rules
   - Phần 3: Thiết kế ứng dụng & **Sơ đồ Mermaid ERD (Quan hệ 1-1, 1-N, N-N)**
   - Phần 4: Thực hiện dự án (MVC, Transaction, Anti Race Condition)
   - Phần 5: **Kiểm lỗi & Bảng kết quả 95 Testcase**
   - Phần 6: Đóng gói & Triển khai
   - Phần 7: Kết luận

---

## ⚡ 4. CHECKLIST DÀNH CHO NHÓM TRƯỚC BẢO VỆ

- [ ] Đã nộp file source code + `testcase.md` (hoặc xuất thêm `Testcase.xlsx`) lên hệ thống/CMS.
- [ ] Đã in báo cáo bản cứng `BAO_CAO_DU_AN.md`.
- [ ] Đã thiết kế slide `.pptx` dựa trên `SLIDE_THUYET_TRINH.md`.
- [ ] Đã chuẩn bị sẵn data demo trong database (Sản phẩm Nike/Adidas, Đơn hàng mẫu, Tài khoản Admin & Customer).
- [ ] Đã luyện tập thuyết trình nhóm (15-20 phút).
