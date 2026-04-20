<?php

namespace App\Helpers;

class OrderStatusHelper
{
    public static function normalizeStatus(string $status): string
    {
        return match (trim($status)) {
            'accepted', 'in-progress' => 'in_progress',
            'on_review' => 'delivered',
            'completed' => 'approved',
            default => trim($status),
        };
    }

    public static function labelForStatus(string $status): string
    {
        return match (self::normalizeStatus($status)) {
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'delivered' => 'Delivered',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
            default => 'Unknown',
        };
    }

    public static function getStatusClasses(string $status): string
    {
        return match (self::normalizeStatus($status)) {
            'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300',
            'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
            'delivered'   => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
            'approved'    => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
            'cancelled', 'rejected'      => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300',
            'accepted'    => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300',
            default   => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        };
    }
}
