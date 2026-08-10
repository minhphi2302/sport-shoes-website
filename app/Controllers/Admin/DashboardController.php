<?php

namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;

class DashboardController extends AdminController
{
    private Order $orderModel;
    private User $userModel;
    private Product $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->orderModel = new Order();
        $this->userModel = new User();
        $this->productModel = new Product();
    }

    public function index(): void
    {
        $monthlyRevenue = $this->orderModel->getMonthlyRevenue();
        $todayOrdersCount = $this->orderModel->getTodayOrdersCount();
        $totalCustomersCount = $this->userModel->countAllCustomers([]);
        $totalProductsCount = $this->productModel->countAllWithFilters([]);
        
        $latestOrders = $this->orderModel->getLatestPendingOrders(5);

        $this->view('admin/dashboard', [
            'monthlyRevenue' => $monthlyRevenue,
            'todayOrdersCount' => $todayOrdersCount,
            'totalCustomersCount' => $totalCustomersCount,
            'totalProductsCount' => $totalProductsCount,
            'latestOrders' => $latestOrders
        ]);
    }
}
