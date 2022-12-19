<?php

namespace App\Middleware;

class Auth
{
    public static function handle()
    {
        if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
            redirect('/login');
        }
    }
}
