---
name: execute-task
description: Tự động đọc file đặc tả công việc (Task Spec) theo mã ID và tự động triển khai mã nguồn hoặc viết unit test tùy theo Role.
---

# Kỹ năng: Thực thi công việc tự động (execute-task)

Kỹ năng này giúp Dev và QA lấy thông tin từ Task Spec và bắt tay vào việc ngay lập tức mà không cần prompt dài dòng.

## Quy trình thực hiện

Khi Dev/QA gọi lệnh `/execute-task [TASK-ID]` (VD: `/execute-task TASK-001`), hãy:
1. Dùng tool `view_file` hoặc `grep_search` để tìm và đọc nội dung file task tương ứng trong thư mục `docs/tasks/`.
2. Đọc "Role AI khuyên dùng" trong file Task.
3. Phân tích "Acceptance Criteria" và "Tech Notes".
4. Nếu bạn là **Feature Developer**: Hãy tiến hành sinh hoặc sửa đổi mã nguồn (Controller, Model, View) để đáp ứng chính xác các AC đó.
5. Nếu bạn là **QA Engineer**: Hãy sinh ra các kịch bản PHPUnit Test để test các AC đó.
6. Sau khi hoàn thành và tự verify code thành công, hãy tự động dùng `replace_file_content` để cập nhật trạng thái trong file Task đó thành `Ready for Test` (nếu dev làm) hoặc `Done` (nếu tester làm).
