<?php

if (! function_exists('formatMontant')) {
    function formatMontant(float $montant, string $devise = 'MGA'): string
    {
        return number_format($montant, 0, ',', ' ') . ' ' . $devise;
    }
}

if (! function_exists('formatDate')) {
    function formatDate(?string $date, string $format = 'd/m/Y H:i'): string
    {
        if (empty($date)) {
            return '-';
        }

        return date($format, strtotime($date));
    }
}
