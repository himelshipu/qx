<?php

namespace App\View\Components\Frontend\Partials;

use App\Models\Category;
use Illuminate\View\Component;

class Categories extends Component
{
    public $featuredCategories;
    public $fallbackCategories;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->featuredCategories = $this->getFeaturedCategories();
        $this->fallbackCategories = $this->getFallbackCategories();
    }

    /**
     * Get featured categories from database.
     */
    private function getFeaturedCategories()
    {
        return Category::query()
            ->where('is_featured', true)
            ->where('is_active', true)
            ->orderBy('featured_order')
            ->limit(4)
            ->get();
    }

    /**
     * Get fallback static categories (used if no featured categories exist).
     */
    private function getFallbackCategories()
    {
        return collect([
            [
                'name' => 'Fashion',
                'slug' => 'fashion',
                'img' => 'https://d5ik1gor6xydq.cloudfront.net/websiteImages/influencerMarketplace/categories/fashion.png',
            ],
            [
                'name' => 'Music & Dance',
                'slug' => 'music-dance',
                'img' => 'https://d5ik1gor6xydq.cloudfront.net/websiteImages/influencerMarketplace/categories/music%20&%20dance.png',
            ],
            [
                'name' => 'Beauty',
                'slug' => 'beauty',
                'img' => 'https://d5ik1gor6xydq.cloudfront.net/websiteImages/influencerMarketplace/categories/beauty.png',
            ],
            [
                'name' => 'Travel',
                'slug' => 'travel',
                'img' => 'https://d5ik1gor6xydq.cloudfront.net/websiteImages/influencerMarketplace/categories/travel.png',
            ],
        ]);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.frontend.partials.categories');
    }
}
