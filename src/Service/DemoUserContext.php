<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;

class DemoUserContext
{
    public function getUserId(Request $request): int
    {
        $fallback = (int) ($_ENV['DEMO_USER_ID'] ?? $_SERVER['DEMO_USER_ID'] ?? 2);
        $userId = (int) $request->query->get('user', $fallback);

        return $userId > 0 ? $userId : $fallback;
    }
}
