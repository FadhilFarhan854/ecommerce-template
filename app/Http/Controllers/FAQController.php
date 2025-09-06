<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\FAQ;

class FAQController extends Controller
{
    public function index()
    {
        $faqs = FAQ::all();
        return view('faq.index', compact('faqs'));
    }


    public function create()
    {
        return view('faq.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        FAQ::create($request->all());

        return redirect()->route('faqs.index')
                         ->with('success', 'FAQ created successfully.');
    }
    public function edit($id)
    {
        $faq = FAQ::findOrFail($id);
        return view('faq.edit', compact('faq'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        $faq = FAQ::findOrFail($id);
        $faq->update($request->all());

        return redirect()->route('faqs.index')
                         ->with('success', 'FAQ updated successfully.');
    }
    public function destroy($id)
    {
        $faq = FAQ::findOrFail($id);
        $faq->delete();

        return redirect()->route('faqs.index')
                         ->with('success', 'FAQ deleted successfully.');
    }
}
