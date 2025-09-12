<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WilayahController extends Controller
{
    /**
     * Get all provinces from wilayah.id API
     */
    public function getProvinces()
    {
        try {
            // Cache the provinces data for 24 hours since it doesn't change often
            $provinces = Cache::remember('wilayah_provinces', 24 * 60 * 60, function () {
                $response = Http::timeout(30)->get('https://wilayah.id/api/provinces.json');
                
                if ($response->successful()) {
                    return $response->json();
                }
                
                throw new \Exception('Failed to fetch provinces');
            });

            return response()->json($provinces['data'] ?? []);

        } catch (\Exception $e) {
            \Log::error('Error fetching provinces: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data provinsi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get regencies/cities for a specific province
     */
    public function getRegencies($provinceCode)
    {
        try {
            // Validate province code
            if (empty($provinceCode)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode provinsi tidak valid'
                ], 400);
            }

            // Cache the regencies data for 24 hours
            $regencies = Cache::remember("wilayah_regencies_{$provinceCode}", 24 * 60 * 60, function () use ($provinceCode) {
                $response = Http::timeout(30)->get("https://wilayah.id/api/regencies/{$provinceCode}.json");
                
                if ($response->successful()) {
                    return $response->json();
                }
                
                throw new \Exception('Failed to fetch regencies');
            });

            return response()->json($regencies['data'] ?? []);

        } catch (\Exception $e) {
            \Log::error("Error fetching regencies for province {$provinceCode}: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data kota/kabupaten',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get districts for a specific regency (optional - for future use)
     */
    public function getDistricts($regencyCode)
    {
        try {
            if (empty($regencyCode)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode kota/kabupaten tidak valid'
                ], 400);
            }

            $districts = Cache::remember("wilayah_districts_{$regencyCode}", 24 * 60 * 60, function () use ($regencyCode) {
                $response = Http::timeout(30)->get("https://wilayah.id/api/districts/{$regencyCode}.json");
                
                if ($response->successful()) {
                    return $response->json();
                }
                
                throw new \Exception('Failed to fetch districts');
            });

            return response()->json($districts['data'] ?? []);

        } catch (\Exception $e) {
            \Log::error("Error fetching districts for regency {$regencyCode}: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data kecamatan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear wilayah cache (admin function)
     */
    public function clearCache()
    {
        try {
            // Clear all wilayah-related cache
            Cache::forget('wilayah_provinces');
            
            // Clear regency caches (this is a simplified approach)
            // In production, you might want to keep track of cached keys
            for ($i = 11; $i <= 94; $i++) {
                Cache::forget("wilayah_regencies_{$i}");
            }

            return response()->json([
                'success' => true,
                'message' => 'Cache wilayah berhasil dibersihkan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membersihkan cache',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
