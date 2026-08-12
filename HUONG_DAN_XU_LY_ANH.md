# Hướng Dẫn Xử Lý Ảnh Sản Phẩm

## 🎯 Vấn Đề: Ảnh Bị Mờ

### Nguyên nhân:
1. CSS có thuộc tính `filter` hoặc `blur`
2. Ảnh có kích thước nhỏ bị kéo giãn
3. Object-fit gây mất chất lượng

### ✅ Đã Sửa:
```css
.product-img-fixed, 
.card-img-top {
    image-rendering: -webkit-optimize-contrast !important;
    image-rendering: crisp-edges !important;
    -webkit-backface-visibility: hidden;
    transform: translateZ(0);
}
```

---

## 📸 Cách 1: Tạo Ảnh Placeholder (Đơn Giản)

**Bước 1:** Chạy script tạo ảnh
```bash
cd c:\xampp\htdocs\sport-shoes-website
php create_placeholder_images.php
```

**Kết quả:**
- Tạo 10 ảnh placeholder màu sắc khác nhau
- File: `public/uploads/products/product_*.jpg`
- Ảnh mặc định: `public/uploads/default-product.jpg`

---

## 🌐 Cách 2: Download Ảnh Từ Internet (Khuyến Nghị)

**Bước 1:** Chạy script download
```bash
cd c:\xampp\htdocs\sport-shoes-website
php download_sample_images.php
```

**Kết quả:**
- Download 10 ảnh giày thật từ Unsplash (miễn phí)
- Chất lượng cao, không mờ

**Lưu ý:**
- Cần có kết nối internet
- PHP phải bật `allow_url_fopen`

---

## 💻 Cách 3: Upload Ảnh Thật Từ Máy Tính (Tốt Nhất)

### A. Tìm Ảnh Miễn Phí

**Website tải ảnh giày miễn phí:**
1. **Unsplash.com**
   - Tìm: "nike shoes", "sneakers", "running shoes"
   - Chất lượng cao, miễn phí thương mại

2. **Pexels.com**
   - Tìm: "sports shoes", "adidas", "athletic shoes"

3. **Pixabay.com**
   - Tìm: "footwear", "trainers"

### B. Upload Vào Website

**Bước 1:** Download ảnh về máy

**Bước 2:** Vào Admin Panel
```
http://localhost:8888/sport-shoes-website/admin/products
```

**Bước 3:** Thêm/Sửa sản phẩm
- Click "Thêm sản phẩm" hoặc "Sửa" sản phẩm có sẵn
- Chọn file ảnh (mục "Hình ảnh sản phẩm")
- Lưu

---

## 🔧 Cách 4: Sửa Ảnh Bị Mờ Trong Database

Nếu ảnh đã có nhưng vẫn mờ:

**Bước 1:** Xóa cache browser
```
Ctrl + Shift + Delete → Xóa cache
```

**Bước 2:** Hard refresh
```
Ctrl + F5 (hoặc Shift + F5)
```

**Bước 3:** Kiểm tra kích thước ảnh
```bash
# Vào thư mục uploads
cd c:\xampp\htdocs\sport-shoes-website\public\uploads\products

# Xem kích thước file
dir
```

Nếu file < 50KB → Ảnh có thể bị nén quá mức

**Bước 4:** Upload lại ảnh chất lượng cao hơn
- Kích thước khuyến nghị: **800x800px**
- Dung lượng: **100KB - 500KB**
- Format: JPG, PNG, WEBP

---

## 🎨 Khuyến Nghị Về Ảnh Sản Phẩm

### ✅ Kích Thước Tốt Nhất:
- **800x800px** hoặc **1000x1000px**
- Tỷ lệ: 1:1 (vuông)

### ✅ Dung Lượng:
- **Tối thiểu:** 100KB
- **Tối ưu:** 200KB - 500KB
- **Tối đa:** 2MB (theo config)

### ✅ Format:
- **WEBP** (nhỏ nhất, chất lượng tốt)
- **JPG** (phổ biến)
- **PNG** (nếu cần nền trong suốt)

### ❌ Tránh:
- Ảnh < 500x500px (sẽ bị mờ khi hiển thị)
- Ảnh quá nén (< 50KB)
- Ảnh có watermark lớn

---

## 🛠️ Troubleshooting

### Vấn đề 1: Không upload được ảnh

**Kiểm tra:**
```ini
; php.ini
upload_max_filesize = 10M
post_max_size = 10M
```

**Cách sửa:**
1. Mở `C:\xampp\php\php.ini`
2. Tìm và sửa 2 dòng trên
3. Restart Apache

---

### Vấn đề 2: Ảnh vẫn mờ sau khi upload

**Nguyên nhân:** Browser cache

**Cách sửa:**
```
1. Ctrl + Shift + Delete
2. Xóa "Cached images and files"
3. Ctrl + F5 (hard refresh)
```

---

### Vấn đề 3: Ảnh không hiển thị (404)

**Kiểm tra:**
```
1. File có tồn tại không?
   → Vào thư mục: public/uploads/products/

2. Đường dẫn đúng chưa?
   → Xem source HTML, kiểm tra src="..."

3. Quyền truy cập file?
   → Chmod 755 (trên Linux)
```

---

## 📝 Seed Data Với Ảnh

Nếu muốn tạo dữ liệu mẫu kèm ảnh:

**Bước 1:** Chạy script tạo ảnh
```bash
php create_placeholder_images.php
```

**Bước 2:** Chạy seed data
```bash
php database/seed.php
```

**Bước 3:** Update path ảnh trong seed.php
```php
// Thêm vào seed.php
$images = glob(__DIR__ . '/../public/uploads/products/*.jpg');
foreach ($products as $index => $product) {
    $product['image_url'] = 'products/' . basename($images[$index % count($images)]);
    // INSERT product...
}
```

---

## 🎯 Checklist Cuối Cùng

- [ ] Đã chạy `create_placeholder_images.php` HOẶC `download_sample_images.php`
- [ ] Đã có file trong `public/uploads/products/`
- [ ] Đã upload ảnh qua Admin Panel
- [ ] Đã xóa cache browser (Ctrl + Shift + Delete)
- [ ] Đã hard refresh (Ctrl + F5)
- [ ] Đã kiểm tra trang chủ → Ảnh hiển thị rõ nét

---

## 💡 Tips

1. **Dùng ảnh WEBP** để giảm dung lượng nhưng giữ chất lượng
2. **Compress ảnh trước khi upload** bằng TinyPNG.com
3. **Đặt tên file có ý nghĩa:** `nike-air-max-red.jpg` thay vì `IMG_001.jpg`
4. **Backup ảnh thường xuyên** vào Google Drive hoặc OneDrive

---

## 🔗 Nguồn Tham Khảo

- Unsplash: https://unsplash.com/s/photos/sneakers
- Pexels: https://www.pexels.com/search/shoes/
- TinyPNG (nén ảnh): https://tinypng.com/
- Convert to WEBP: https://cloudconvert.com/jpg-to-webp

---

**Tác giả:** AI Assistant  
**Ngày:** 11/08/2026  
**Version:** 1.0
