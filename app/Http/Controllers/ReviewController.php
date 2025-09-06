<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'review' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        Review::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'review' => $request->review,
            'rating' => $request->rating,
        ]);

        return redirect()->back()->with('success', 'Review berhasil dikirim!');
    }

    public function update(Request $request, Review $review)
    {
        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'review' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $review->update([
            'review' => $request->review,
            'rating' => $request->rating,
        ]);

        return redirect()->back()->with('success', 'Review berhasil diupdate!');
    }

    public function destroy(Review $review)
    {
        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $review->delete();

        return redirect()->back()->with('success', 'Review berhasil dihapus!');
    }
}
