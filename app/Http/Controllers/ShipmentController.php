<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ShipmentController extends Controller
{
    private $baseUrl;
    private $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.rajaongkir.base_url', 'https://api.rajaongkir.com/starter');
        $this->apiKey = config('services.rajaongkir.api_key');
        
        if (!$this->apiKey) {
            throw new \Exception('RajaOngkir API key is not configured');
        }
    }

    /**
     * Get all provinces
     */
    public function getProvinces(): JsonResponse
    {
        try {
            // Cache provinces for 24 hours
            $provinces = Cache::remember('rajaongkir_provinces', 86400, function () {
                $response = Http::withHeaders([
                    'key' => $this->apiKey
                ])->get($this->baseUrl . '/destination/province');

                if (!$response->successful()) {
                    throw new \Exception('Failed to fetch provinces from RajaOngkir');
                }

                $data = $response->json();
                
                if (!isset($data['data'])) {
                    throw new \Exception('Invalid response format from RajaOngkir');
                }

                // Transform to match frontend expectations
                return collect($data['data'])->map(function ($province) {
                    return [
                        'province_id' => $province['id'],
                        'province' => $province['name']
                    ];
                })->toArray();
            });

            return response()->json([
                'success' => true,
                'data' => $provinces
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching provinces: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch provinces',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cities by province ID
     */
    public function getCities(Request $request): JsonResponse
    {
        try {
            $provinceId = $request->get('province_id');
            
            if (!$provinceId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Province ID is required'
                ], 400);
            }

            $cacheKey = "rajaongkir_cities_{$provinceId}";

            // Cache cities for 24 hours
            $cities = Cache::remember($cacheKey, 86400, function () use ($provinceId) {
                $response = Http::withHeaders([
                    'key' => $this->apiKey
                ])->get($this->baseUrl . '/destination/city/' . $provinceId);

                if (!$response->successful()) {
                    throw new \Exception('Failed to fetch cities from RajaOngkir');
                }

                $data = $response->json();
                
                if (!isset($data['data'])) {
                    throw new \Exception('Invalid response format from RajaOngkir');
                }

                // Transform to match frontend expectations  
                return collect($data['data'])->map(function ($city) use ($provinceId) {
                    return [
                        'city_id' => $city['id'],
                        'province_id' => $provinceId,
                        'city_name' => $city['name'],
                        'type' => 'Kota/Kabupaten' // Default type since API doesn't provide it
                    ];
                })->toArray();
            });

            return response()->json([
                'success' => true,
                'data' => $cities
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching cities: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cities',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate shipping cost
     */
    public function calculateShippingCost(Request $request): JsonResponse
    {
        $request->validate([
            'origin' => 'required|integer',
            'destination' => 'required|integer', 
            'weight' => 'required|integer|min:1',
            'courier' => 'required|string|in:jne,pos,tiki'
        ]);

        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey,
                'content-type' => 'application/x-www-form-urlencoded'
            ])->asForm()->post($this->baseUrl . '/calculate/district/domestic-cost', [
                'origin' => $request->origin,
                'destination' => $request->destination,
                'weight' => $request->weight,
                'courier' => $request->courier
            ]);

            if (!$response->successful()) {
                throw new \Exception('Failed to calculate shipping cost');
            }

            $data = $response->json();

            if (!isset($data['data'])) {
                throw new \Exception('Invalid response format from RajaOngkir');
            }

            // Transform the response to make it easier to use
            $results = collect($data['data'])->map(function ($courier) {
                return [
                    'courier_code' => $courier['code'],
                    'courier_name' => $courier['name'],
                    'services' => collect($courier['costs'])->map(function ($cost) {
                        return [
                            'service' => $cost['service'],
                            'description' => $cost['description'],
                            'cost' => $cost['cost'][0]['value'],
                            'etd' => $cost['cost'][0]['etd'],
                            'note' => $cost['cost'][0]['note'] ?? ''
                        ];
                    })
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $results
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error calculating shipping cost: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate shipping cost',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available couriers
     */
    public function getAvailableCouriers(): JsonResponse
    {
        $couriers = [
            [
                'code' => 'jne',
                'name' => 'Jalur Nugraha Ekakurir (JNE)',
                'services' => ['REG', 'OKE', 'YES']
            ],
            [
                'code' => 'pos',
                'name' => 'POS Indonesia',
                'services' => ['Paket Kilat Khusus', 'Express']
            ],
            [
                'code' => 'tiki',
                'name' => 'Citra Van Titipan Kilat (TIKI)',
                'services' => ['REG', 'ECO', 'ONS']
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $couriers
        ]);
    }

    /**
     * Get multiple shipping costs for comparison
     */
    public function compareShippingCosts(Request $request): JsonResponse
    {
        $request->validate([
            'origin' => 'required|integer',
            'destination' => 'required|integer',
            'weight' => 'required|integer|min:1',
            'couriers' => 'array|min:1',
            'couriers.*' => 'string|in:jne,pos,tiki'
        ]);

        try {
            $couriers = $request->couriers ?? ['jne', 'pos', 'tiki'];
            $results = collect();

            foreach ($couriers as $courier) {
                try {
                    $response = Http::withHeaders([
                        'key' => $this->apiKey,
                        'content-type' => 'application/x-www-form-urlencoded'
                    ])->asForm()->post($this->baseUrl . '/calculate/district/domestic-cost', [
                        'origin' => $request->origin,
                        'destination' => $request->destination,
                        'weight' => $request->weight,
                        'courier' => $courier
                    ]);

                    if ($response->successful()) {
                        $data = $response->json();
                        if (isset($data['data'][0])) {
                            $courierData = $data['data'][0];
                            $results->push([
                                'courier_code' => $courierData['code'],
                                'courier_name' => $courierData['name'],
                                'services' => collect($courierData['costs'])->map(function ($cost) {
                                    return [
                                        'service' => $cost['service'],
                                        'description' => $cost['description'],
                                        'cost' => $cost['cost'][0]['value'],
                                        'etd' => $cost['cost'][0]['etd'],
                                        'note' => $cost['cost'][0]['note'] ?? ''
                                    ];
                                })
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning("Failed to get cost for courier {$courier}: " . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'data' => $results,
                'comparison' => [
                    'origin' => $request->origin,
                    'destination' => $request->destination,
                    'weight' => $request->weight . ' gram'
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error comparing shipping costs: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to compare shipping costs',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
