<?php

namespace App\Http\Controllers;

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
                'setup_data' => null,
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
        
        $step = $request->input('step');
        $value = $request->input('value');
        
        // Get existing setup data
        $setupData = $brand->setup_data ?? [];
        
        // Update the specific step
        $setupData[$step] = $value;
        
        // Save the updated setup data
        $brand->update(['setup_data' => $setupData]);
        
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
        $brand = Brand::where('user_id', $user->id)->firstOrFail();
        
        return response()->json([
            'setup_data' => $brand->setup_data ?? [],
        ]);
    }

    /**
     * Complete the setup.
     */
    public function complete(Request $request)
    {
        $user = Auth::user();
        $brand = Brand::where('user_id', $user->id)->firstOrFail();
        
        
        
        return response()->json([
            'success' => true,
            'message' => 'Brand setup completed',
            'redirect' => route('dashboard'),
        ]);
    }
}
