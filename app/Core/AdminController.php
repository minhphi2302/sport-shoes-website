<?php

namespace App\Core;

abstract class AdminController extends Controller
{
    public function __construct()
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }

        $user = Auth::user();
        if ($user === null || $user['role'] !== 'admin') {
            http_response_code(403);
            die('403 Forbidden - You do not have permission to access this page.');
        }
    }
}
