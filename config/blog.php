<?php

return [
    'title_max' => env('BLOG_TITLE_MAX', 255),
    'slug_max' => env('BLOG_SLUG_MAX', 255),
    'excerpt_max' => env('BLOG_EXCERPT_MAX', 1000),
    'meta_description_max' => env('BLOG_META_DESCRIPTION_MAX', 500),
    'meta_keywords_max' => env('BLOG_META_KEYWORDS_MAX', 500),
    'per_page' => env('BLOG_PER_PAGE', 12),
    'featured_image_max_size' => env('BLOG_FEATURED_IMAGE_MAX_SIZE', 5120),
];
