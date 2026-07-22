---
name: create-task
description: Đóng vai Project Manager để phân tích yêu cầu, sinh ra file đặc tả công việc chuẩn (Task Spec) và lưu vào hệ thống.
---

# Kỹ năng: Phân tích & Giao việc (create-task)

Kỹ năng này giúp Leader biến một yêu cầu thô thành một bản phân tích thiết kế chi tiết (Task Spec) cực chuẩn để giao cho Dev.

## Quy trình thực hiện

Khi người dùng (Leader) muốn giao việc, hãy thực hiện các bước sau:
1. Đọc nội dung file `docs/templates/task_template.md`.
2. Nếu cần thiết, đọc file `AGENTS.md` để đối chiếu Business Rules và đặt tên role phù hợp.
3. Sinh ra một file Markdown mới theo đúng chuẩn của template, điền đầy đủ các thông tin chuyên sâu (Acceptance Criteria, Tech Notes).
4. Xác định số thứ tự tiếp theo của task (Ví dụ nếu có `TASK-001`, thì task tiếp theo là `TASK-002`).
5. Sử dụng tool (`write_to_file`) để tự động lưu file này vào thư mục `docs/tasks/TASK-[ID]-[ten-ngan].md`.
6. Báo cho Leader biết file đã được lưu và nhắc Leader push file đó lên Github (hoặc gõ `/commit` nếu có).
