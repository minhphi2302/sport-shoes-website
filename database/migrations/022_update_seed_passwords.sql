-- Migration 022: Cập nhật mật khẩu tất cả user seed data sang "Minh1234@"
-- Business rule USERS: mật khẩu ≥ 8 ký tự, ≥1 chữ hoa, ≥1 chữ thường, ≥1 ký tự đặc biệt
-- Hash bcrypt của "Minh1234@" (PHP password_hash với PASSWORD_BCRYPT, cost=10)

UPDATE users
SET `password` = '$2y$10$iIvkzlIR0r//tvk5tayAFueEQW.QWALFiPwBncHQmUZO75lzAoz6u';
