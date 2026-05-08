<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FAQRequest;
use App\Models\FAQ;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class FAQController extends Controller
{
    public function index(Request $request)
    {
        $query = FAQ::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('question', 'like', "%{$search}%")
                ->orWhere('answer', 'like', "%{$search}%");
        }

        $faqs = $query->orderBy('order_priority', 'asc')->latest()->paginate(15)->withQueryString();

        return view('admin.faq.index', compact('faqs'));
    }

    public function toggleStatus($id)
    {
        $faq = FAQ::findOrFail($id);
        $faq->status = $faq->status === 'active' ? 'inactive' : 'active';
        $faq->save();
        $this->clearSiteCache();

        Alert::success('Success', 'FAQ visibility updated.');

        return back();
    }

    public function create()
    {
        return view('admin.faq.create');
    }

    public function store(FAQRequest $request)
    {
        $validated = $request->validated();

        FAQ::create($validated);
        $this->clearSiteCache();

        Alert::success('Success', 'FAQ created successfully.');

        return redirect()->route('admin.faq.index');
    }

    public function show($id)
    {
        return redirect()->route('admin.faq.edit', $id);
    }

    public function edit($id)
    {
        $faq = FAQ::findOrFail($id);

        return view('admin.faq.edit', compact('faq'));
    }

    public function update(FAQRequest $request, $id)
    {
        $faq = FAQ::findOrFail($id);
        $validated = $request->validated();

        $faq->update($validated);
        $this->clearSiteCache();

        Alert::success('Success', 'FAQ updated successfully.');

        return redirect()->route('admin.faq.index');
    }

    public function destroy($id)
    {
        $faq = FAQ::findOrFail($id);
        $faq->delete();
        $this->clearSiteCache();

        Alert::success('Success', 'FAQ deleted successfully.');

        return back();
    }
}
