<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BannerController extends Controller
{
    /**
     * Display a listing of banners.
     */
    public function index()
    {
        $banners = Banner::latest()->get();
        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Show the form for creating a new banner.
     */
    public function create()
    {
        return view('admin.banners.create');
    }

    /**
     * Store a newly created banner.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'status' => 'nullable'
        ], [
            'image.required' => 'Gambar banner wajib diupload.',
            'image.image' => 'File yang diupload harus berupa gambar.',
            'image.mimes' => 'Format gambar harus JPEG, PNG, JPG, GIF, atau WEBP.',
            'image.max' => 'Ukuran gambar maksimal 5MB.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengupload banner.');
        }

        try {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('banners', $imageName, 'public');
            }

            Banner::create([
                'image' => $imagePath,
                'status' => $request->has('status') ? 1 : 0
            ]);

            return redirect()->route('admin.banners.index')
                ->with('success', 'Banner berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan banner: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified banner.
     */
    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'status' => 'nullable'
        ], [
            'image.image' => 'File yang diupload harus berupa gambar.',
            'image.mimes' => 'Format gambar harus JPEG, PNG, JPG, GIF, atau WEBP.',
            'image.max' => 'Ukuran gambar maksimal 5MB.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui banner.');
        }

        try {
            DB::beginTransaction();
            
            // Update status berdasarkan checkbox
            if ($request->has('status')) {
                $banner->status = 1;
            } else {
                $banner->status = 0;
            }

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                    Storage::disk('public')->delete($banner->image);
                }
                
                $image = $request->file('image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $banner->image = $image->storeAs('banners', $imageName, 'public');
            }
            
            $banner->save();

            DB::commit();

            return redirect()->route('admin.banners.index')
                ->with('success', 'Banner berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui banner: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified banner.
     */
    public function destroy(Banner $banner)
    {
        // Delete image file if exists
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner berhasil dihapus.');
    }

    /**
     * Get active banners for frontend
     */
    public function getActiveBanners()
    {
        return Banner::active()->latest()->get();
    }
}
