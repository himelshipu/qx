<?php

namespace App\Helpers;

class PlatformHelper
{
    public static function getPlatformColor(string $platform): string
    {
        return match ($platform) {
            'facebook'  => '#1877F2',
            'instagram' => '#E1306C',
            'tiktok'    => '#000000',
            'linkedin'  => '#0A66C2',
            'x'         => '#1DA1F2',
            'youtube'   => '#FF0000',
            'ugc'       => '#A855F7',
            default     => '#6B7280',
        };
    }

    public static function getPlatformName(string $platform): string
    {
        return match ($platform) {
            'facebook'  => 'Facebook',
            'instagram' => 'Instagram',
            'tiktok'    => 'TikTok',
            'linkedin'  => 'LinkedIn',
            'x'         => 'X',
            'youtube'   => 'YouTube',
            'ugc'       => 'UGC',
            default     => ucfirst($platform),
        };
    }
}
