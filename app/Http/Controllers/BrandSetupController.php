<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrandSetupController extends Controller
{
    /**
     * Show the brand setup steps.
     */
    public function show()
    {
        $user = Auth::user();
        
        // Check if brand already exists for this user
        $brand = Brand::where('user_id', $user->id)->first();
            
        if (!$brand) {
            // Create a new brand
            $brand = Brand::create([
                'user_id' => $user->id,
            ]);
        }
        
        return view('auth.brand-setup', ['brand' => $brand]);
    }

    /**
     * Store the setup step data.
     */
    public function storeStep(Request $request)
    {
        $user = Auth::user();
        $brand = Brand::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'step'  => ['required', 'string', 'in:objective,budget,business-type,company-size,influencer-type'],
            'value' => ['nullable']
        ]);

        $profile = $brand->onboardingProfile()->firstOrCreate([], [
            'is_completed' => false
        ]);

        $step = $validated['step'];
        $value = $validated['value'] ?? null;

        match ($step) {
            'objective' => $profile->objective = $this->nullableString($value),
            'budget' => $profile->budget_range = $this->nullableString($value),
            'business-type' => $profile->business_type = $this->nullableString($value),
            'company-size' => $profile->company_size = $this->nullableString($value),
            'influencer-type' => $this->syncIndustryCategories($profile, $value),
            default => null
        };

        $profile->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Step saved successfully',
        ]);
    }

    /**
     * Get all setup data.
     */
    public function getSetupData()
    {
        $user = Auth::user();
        $brand = Brand::where('user_id', $user->id)->with('onboardingProfile.categories:id,slug')->firstOrFail();

        $profile = $brand->onboardingProfile;

        $setupData = [
            'objective'       => $profile?->objective,
            'budget'          => $profile?->budget_range,
            'business-type'   => $profile?->business_type,
            'company-size'    => $profile?->company_size,
            'influencer-type' => $profile ? $profile->categories->pluck('slug')->values()->all() : []
        ];
        
        return response()->json([
            'brand_name' => $brand->brand_name ?? '',
            'setup_data' => $setupData,
            'current_step' => null,
        ]);
    }

    /**
     * Complete the setup.
     */
    public function complete(Request $request)
    {
        $user = Auth::user();
        $brand = Brand::where('user_id', $user->id)->firstOrFail();

        $profile = $brand->onboardingProfile()->firstOrCreate([], [
            'is_completed' => false
        ]);

        $hasData = !empty($profile->objective)
            || !empty($profile->budget_range)
            || !empty($profile->business_type)
            || !empty($profile->company_size)
            || $profile->categories()->exists();

        if ($hasData) {
            $profile->is_completed = true;
            $profile->completed_at = now();
            $profile->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Brand setup completed',
            'redirect' => route('home'),
        ]);
    }

    private function nullableString(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }

    private function syncIndustryCategories($profile, mixed $rawValue): void
    {
        $slugs = array_values(array_filter(array_map(
            fn($item): string => trim((string) $item),
            is_array($rawValue) ? $rawValue : []
        ), fn(string $slug): bool => $slug !== ''));

        if ($slugs === []) {
            $profile->categories()->sync([]);
            return;
        }

        $categoryIds = Category::query()
            ->whereIn('slug', $slugs)
            ->pluck('id')
            ->all();

        $profile->categories()->sync($categoryIds);
    }
}
