<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterData;

class MasterDataController extends Controller
{
    /**
     * Get all master data or filter by type
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $type = $request->query('type');
        
        if ($type) {
            $data = MasterData::ofType($type)->get();
        } else {
            $data = MasterData::where('is_active', true)
                ->orderBy('type')
                ->orderBy('sort_order')
                ->get()
                ->groupBy('type');
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get route visibility options
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRouteVisibility()
    {
        $data = MasterData::getRouteVisibility();
        
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get route status options
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRouteStatus()
    {
        $data = MasterData::getRouteStatus();
        
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get marina types
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMarinaTypes()
    {
        $data = MasterData::getMarinaTypes();
        
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get yacht types
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getYachtTypes()
    {
        $data = MasterData::getYachtTypes();
        
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get countries
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCountries()
    {
        $data = MasterData::getCountries();
        
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get all master data types (for mobile app initialization)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll()
    {
        $data = [
            'route_visibility' => MasterData::getRouteVisibility(),
            'route_status' => MasterData::getRouteStatus(),
            'marina_types' => MasterData::getMarinaTypes(),
            'yacht_types' => MasterData::getYachtTypes(),
            'countries' => MasterData::getCountries(),
        ];
        
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
