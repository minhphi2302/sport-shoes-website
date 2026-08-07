---
name: make-mvc
description: Sinh tự động các file Controller, Model và View cho một module mới trong dự án.
---

# Kỹ năng: Sinh tự động MVC (make-mvc)

Kỹ năng này giúp tự động tạo ra cấu trúc file chuẩn cho một module mới (bao gồm Controller, Model và các View mẫu).

## Quy trình thực hiện

1. Hỏi người dùng tên Module cần tạo (VD: `Product`, `Order`, `Banner`).
2. Sinh file `app/Models/[Module].php` kế thừa từ `App\Core\Model`.
3. Sinh file `app/Controllers/Admin/[Module]Controller.php` (hoặc Client) kế thừa `App\Core\Controller`.
4. Khởi tạo thuộc tính kết nối DB trong Model.
5. Cập nhật `public/index.php` (nếu cần thiết) để đăng ký Route cơ bản cho Module mới.

## Mẫu Model

```php
<?php
namespace App\Models;
use App\Core\Model;

class [Module] extends Model {
    protected string $table = '[modules]'; // Thay đổi theo CSDL
}
```

## Mẫu Controller

```php
<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\[Module];

class [Module]Controller extends Controller {
    public function index() {
        $model = new [Module]();
        $data = $model->all();
        $this->view('[module]/index', ['data' => $data]);
    }
}
```
