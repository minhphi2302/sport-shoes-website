<?php
require __DIR__ . '/layouts/header.php';

$user = $_SESSION['user'] ?? null;
$name = $user['name'] ?? 'User';
$email = $user['email'] ?? '';
$phone = $user['phone'] ?? 'Chưa có';
$address = $user['address'] ?? 'Chưa có';
?>

<!-- Breadcrumb -->
<div class="bg-light py-3 border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= ($_ENV['APP_URL'] ?? '') ?>/" class="text-decoration-none text-dark">Trang chủ</a></li>
                <li class="breadcrumb-item active text-secondary" aria-current="page">Tài khoản</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <h5 class="fw-bold mb-3">TRANG TÀI KHOẢN</h5>
            <p class="mb-4">Xin chào, <span class="fw-bold"><?= htmlspecialchars($name) ?> !</span></p>
            
            <ul class="nav flex-column list-unstyled mb-4" id="accountTabs" role="tablist">
                <li class="mb-3 nav-item" role="presentation">
                    <a href="#info" class="nav-link text-dark fw-bold px-0 active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-pane" type="button" role="tab" aria-controls="info-pane" aria-selected="true" onclick="changeActiveTab(this)">Thông tin tài khoản</a>
                </li>
                <li class="mb-3 nav-item" role="presentation">
                    <a href="#address" class="nav-link text-dark px-0" id="address-tab" data-bs-toggle="tab" data-bs-target="#address-pane" type="button" role="tab" aria-controls="address-pane" aria-selected="false" onclick="changeActiveTab(this)">Địa chỉ (1)</a>
                </li>
                <li class="nav-item">
                    <a href="<?= ($_ENV['APP_URL'] ?? '') ?>/logout" class="nav-link text-primary px-0">Đăng xuất</a>
                </li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9 tab-content" id="accountTabsContent">
            
            <!-- Tab: Thông tin tài khoản -->
            <div class="tab-pane fade show active" id="info-pane" role="tabpanel" aria-labelledby="info-tab" tabindex="0">
                <h5 class="fw-bold mb-4">TÀI KHOẢN</h5>
                <p class="mb-3">Tên tài khoản: <span class="fw-bold" id="infoAddrName"><?= htmlspecialchars($name) ?></span>!</p>
                <p class="mb-3"><i class="fa-solid fa-house text-dark me-2"></i> Địa chỉ: <span id="infoAddrDetail"><?= htmlspecialchars($address) ?></span></p>
                <p class="mb-4"><i class="fa-solid fa-mobile-screen-button text-dark me-2"></i> Điện thoại: <span id="infoAddrPhone"><?= htmlspecialchars($phone) ?></span></p>
                
                <h5 class="fw-bold mb-3 mt-5">ĐƠN HÀNG CỦA BẠN</h5>
                <div class="table-responsive">
                    <table class="table border-bottom">
                        <thead>
                            <tr class="align-middle">
                                <th scope="col" class="border-0 bg-transparent text-dark py-3 text-center">Mã đơn hàng</th>
                                <th scope="col" class="border-0 bg-transparent text-dark py-3 text-center">Ngày đặt</th>
                                <th scope="col" class="border-0 bg-transparent text-dark py-3 text-center">Thành tiền</th>
                                <th scope="col" class="border-0 bg-transparent text-dark py-3 text-center">Phương thức thanh toán</th>
                                <th scope="col" class="border-0 bg-transparent text-dark py-3 text-center">Trạng Thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($orders)): ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr class="align-middle">
                                        <td class="fw-bold text-dark py-3 text-center">#<?= str_pad($order['order_id'], 5, '0', STR_PAD_LEFT) ?></td>
                                        <td class="text-secondary py-3 text-center"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                        <td class="fw-bold text-red py-3 text-center"><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</td>
                                        <td class="py-3 text-center">
                                            <?php if ($order['payment_method'] === 'COD'): ?>
                                                <span class="badge bg-secondary rounded-pill">COD</span>
                                            <?php else: ?>
                                                <span class="badge bg-success rounded-pill"><?= htmlspecialchars($order['payment_method']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3 text-center">
                                            <?php
                                                $status = $order['status'] ?? 'pending';
                                                $statusBadge = match($status) {
                                                    'pending' => '<span class="badge bg-secondary rounded-pill">Chờ xử lý</span>',
                                                    'confirmed' => '<span class="badge bg-info rounded-pill">Đã xác nhận</span>',
                                                    'completed' => '<span class="badge bg-success rounded-pill">Hoàn thành</span>',
                                                    'cancelled' => '<span class="badge bg-danger rounded-pill">Đã hủy</span>',
                                                    default => '<span class="badge bg-secondary rounded-pill">Chờ xử lý</span>'
                                                };
                                                echo $statusBadge;
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-muted">Không có đơn hàng nào.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Tab: Sổ địa chỉ -->
            <div class="tab-pane fade" id="address-pane" role="tabpanel" aria-labelledby="address-tab" tabindex="0">
                <h5 class="fw-bold mb-4">ĐỊA CHỈ CỦA BẠN</h5>
                
                <!-- Nút Thêm địa chỉ gọi Modal -->
                <button type="button" class="btn btn-dark rounded-1 px-4 py-2 mb-4" data-bs-toggle="modal" data-bs-target="#addressModal" onclick="prepareAddAddress()">Thêm địa chỉ</button>
                
                <hr class="mb-4">
                
                <div class="d-flex justify-content-between align-items-start" id="addressDisplayBlock">
                    <div>
                        <p class="mb-2 text-dark">
                            Họ tên: <span id="displayAddrName"><?= htmlspecialchars($name) ?></span> 
                            <span class="text-success ms-2" id="displayAddrDefault" style="font-size: 0.85rem;"><i class="fa-regular fa-circle-check"></i> Địa chỉ mặc định</span>
                        </p>
                        <p class="mb-0 text-dark mt-1">Điện thoại: <span id="displayAddrPhone"><?= htmlspecialchars($phone) ?></span></p>
                        <p class="mb-0 text-dark mt-1">Địa chỉ: <span id="displayAddrDetail"><?= htmlspecialchars($address) ?></span></p>
                    </div>
                    <!-- Nút Chỉnh sửa gọi Modal -->
                    <a href="javascript:void(0)" class="text-decoration-none text-info" data-bs-toggle="modal" data-bs-target="#addressModal" onclick="prepareEditAddress()">Chỉnh sửa địa chỉ</a>
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- Modal Thêm/Sửa Địa Chỉ -->
<div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold" id="addressModalLabel">Thêm địa chỉ mới</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addressForm">
            <div class="mb-3">
                <label class="form-label text-dark fw-semibold">Họ và tên</label>
                <input type="text" class="form-control rounded-1" id="addrName" value="">
            </div>
            <div class="mb-3">
                <label class="form-label text-dark fw-semibold">Số điện thoại</label>
                <input type="tel" class="form-control rounded-1" id="addrPhone" value="">
            </div>
            <div class="mb-3">
                <label class="form-label text-dark fw-semibold">Địa chỉ cụ thể</label>
                <textarea class="form-control rounded-1" id="addrDetail" rows="3"></textarea>
            </div>
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" id="addrDefault" checked>
                <label class="form-check-label text-dark" for="addrDefault">
                    Đặt làm địa chỉ mặc định
                </label>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-light border rounded-1 px-4" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-dark rounded-1 px-4" data-bs-dismiss="modal" onclick="saveAddress()">Lưu địa chỉ</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// Xử lý đổi độ đậm (fw-bold) cho tab đang chọn
function changeActiveTab(element) {
    document.querySelectorAll('#accountTabs .nav-link').forEach(el => {
        el.classList.remove('fw-bold');
    });
    element.classList.add('fw-bold');
}

// Giữ nguyên tab nếu URL có hash #address
document.addEventListener('DOMContentLoaded', function() {
    let hash = window.location.hash;
    if (hash === '#address') {
        let triggerEl = document.querySelector('#address-tab');
        if (triggerEl) {
            let tab = new bootstrap.Tab(triggerEl);
            tab.show();
            changeActiveTab(triggerEl);
        }
    }
});

// Xử lý Modal Thêm vs Sửa
function prepareAddAddress() {
    document.getElementById('addressModalLabel').innerText = 'Thêm địa chỉ mới';
    document.getElementById('addrName').value = '<?= htmlspecialchars($name) ?>';
    document.getElementById('addrPhone').value = '';
    document.getElementById('addrDetail').value = '';
    document.getElementById('addrDefault').checked = true;
}

function prepareEditAddress() {
    document.getElementById('addressModalLabel').innerText = 'Chỉnh sửa địa chỉ';
    document.getElementById('addrName').value = document.getElementById('displayAddrName').innerText;
    
    let phoneText = document.getElementById('displayAddrPhone').innerText;
    document.getElementById('addrPhone').value = (phoneText === 'Chưa có') ? '' : phoneText;
    
    document.getElementById('addrDetail').value = document.getElementById('displayAddrDetail').innerText;
    
    document.getElementById('addrDefault').checked = (document.getElementById('displayAddrDefault').style.display !== 'none');
}

// Lưu địa chỉ (Gọi API)
async function saveAddress() {
    let newName = document.getElementById('addrName').value;
    let newPhone = document.getElementById('addrPhone').value;
    let newDetail = document.getElementById('addrDetail').value;
    let isDefault = document.getElementById('addrDefault').checked;

    if (!newName) {
        alert("Tên không được để trống!");
        return;
    }

    try {
        let response = await fetch('<?= ($_ENV['APP_URL'] ?? '') ?>/account/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name: newName,
                phone: newPhone,
                address: newDetail
            })
        });

        let result = await response.json();
        if (result.success) {
            document.getElementById('displayAddrName').innerText = newName;
            document.getElementById('displayAddrPhone').innerText = newPhone ? newPhone : 'Chưa có';
            document.getElementById('displayAddrDetail').innerText = newDetail ? newDetail : 'Chưa có';
            
            document.getElementById('infoAddrName').innerText = newName;
            document.getElementById('infoAddrPhone').innerText = newPhone ? newPhone : 'Chưa có';
            document.getElementById('infoAddrDetail').innerText = newDetail ? newDetail : 'Chưa có';
            
            if (isDefault) {
                document.getElementById('displayAddrDefault').style.display = 'inline';
            } else {
                document.getElementById('displayAddrDefault').style.display = 'none';
            }
        } else {
            alert("Lỗi: " + result.message);
        }
    } catch (error) {
        alert("Đã xảy ra lỗi kết nối.");
        console.error(error);
    }
}
</script>

<?php
require __DIR__ . '/layouts/footer.php';
?>
