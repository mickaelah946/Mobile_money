<?php

namespace App\Libraries;

class ReferenceGenerator
{
    public static function transaction(): string
    {
        return 'TXN-' . date('Ymd') . '-' . str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
    }

    public static function client(int $id): string
    {
        return 'CL-' . str_pad((string) $id, 6, '0', STR_PAD_LEFT);
    }

    public static function agent(int $id): string
    {
        return 'AG-' . str_pad((string) $id, 6, '0', STR_PAD_LEFT);
    }

    public static function compte(int $id): string
    {
        return 'CPT-' . str_pad((string) $id, 7, '0', STR_PAD_LEFT);
    }
}
