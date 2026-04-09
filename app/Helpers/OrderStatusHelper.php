<?php

namespace App\Helpers;

class OrderStatusHelper
{
    public static function getStatusClasses(string $status): string
    {
        return match ($status) {
            'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300',
            'in_progress', 'in-progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
            'completed', 'delivered'     => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
            'cancelled', 'rejected'      => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300',
            'accepted', 'approved'       => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300',
            default   => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        };
    }
}
