<?php

namespace App\Traits;

trait NormalizePhoneNumber
{
    /**
     * Normalizes phone numbers to standard Indonesian format (628xxx).
     */
    protected function normalizePhoneNumber(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $cleaned = preg_replace('/[^0-9]/', '', $value);

        if (empty($cleaned)) {
            return null;
        }

        if (str_starts_with($cleaned, '0')) {
            $cleaned = '62' . substr($cleaned, 1);
        } elseif (str_starts_with($cleaned, '8')) {
            $cleaned = '62' . $cleaned;
        }

        return $cleaned;
    }
}
