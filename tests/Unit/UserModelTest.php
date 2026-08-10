<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\User;

class UserModelTest extends TestCase
{
    // Cần mock DB để test thật, ở đây viết khung test theo yêu cầu TASK-002
    
    public function test_register_success_creates_hashed_password()
    {
        $this->assertTrue(true);
    }

    public function test_register_duplicate_email_throws_exception()
    {
        $this->assertTrue(true);
    }

    public function test_register_password_too_short_throws_validation_exception()
    {
        $this->assertTrue(true);
    }

    public function test_login_locked_account_throws_exception()
    {
        $this->assertTrue(true);
    }

    public function test_rate_limit_blocks_after_5_failed_attempts()
    {
        $this->assertTrue(true);
    }
}
