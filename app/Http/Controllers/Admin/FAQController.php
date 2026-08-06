<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FAQRequest;
use App\Models\Course;
use App\Models\FAQ;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class FAQController extends Controller
{
    public function index(Request $request)
    {
        $query = FAQ::withCount('courses');

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
        $courses = Course::where('status', 'active')->orderBy('name')->get();

        return view('admin.faq.create', compact('courses'));
    }

    public function store(FAQRequest $request)
    {
        $validated = $request->validated();
        $courseIds = $validated['courses'] ?? [];
        unset($validated['courses']);

        DB::transaction(function () use ($validated, $courseIds) {
            $faq = FAQ::create($validated);
            $faq->courses()->sync($courseIds);

            DB::afterCommit(fn () => $this->clearSiteCache());
        });

        Alert::success('Success', 'FAQ created successfully.');

        return redirect()->route('admin.faq.index');
    }

    public function show($id)
    {
        return redirect()->route('admin.faq.edit', $id);
    }

    public function edit($id)
    {
        $faq = FAQ::with('courses')->findOrFail($id);
        $assignedCourseIds = $faq->courses->pluck('id')->toArray();
        $courses = Course::where('status', 'active')
            ->orWhereIn('id', $assignedCourseIds)
            ->orderBy('name')
            ->get();

        return view('admin.faq.edit', compact('faq', 'courses'));
    }

    public function update(FAQRequest $request, $id)
    {
        $faq = FAQ::findOrFail($id);
        $validated = $request->validated();
        $courseIds = $validated['courses'] ?? [];
        unset($validated['courses']);

        DB::transaction(function () use ($faq, $validated, $courseIds) {
            $faq->update($validated);
            $faq->courses()->sync($courseIds);

            DB::afterCommit(fn () => $this->clearSiteCache());
        });

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
