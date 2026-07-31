<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFaqRequest;
use App\Http\Requests\Admin\UpdateFaqRequest;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display a paginated, searchable listing of FAQs.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $faqs = Faq::query()
            ->when($search, function ($query, $search) {
                $query->where('question', 'like', "%{$search}%");
            })
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.faqs.index', compact('faqs', 'search'));
    }

    /**
     * Show the form for creating a new FAQ.
     */
    public function create()
    {
        return view('admin.faqs.create');
    }

    /**
     * Store a newly created FAQ in storage.
     */
    public function store(StoreFaqRequest $request)
    {
        $data = $request->validated();

        $data['sort_order'] = $request->filled('sort_order') ? (int) $request->sort_order : 0;
        $data['is_active'] = $request->boolean('is_active', true);

        Faq::create($data);

        return redirect()
            ->route('admin.faqs.index')
            ->with('success', 'FAQ "' . $data['question'] . '" berhasil ditambahkan.');
    }

    /**
     * Display the specified FAQ.
     */
    public function show(Faq $faq)
    {
        return view('admin.faqs.show', compact('faq'));
    }

    /**
     * Show the form for editing the specified FAQ.
     */
    public function edit(Faq $faq)
    {
        return view('admin.faqs.edit', compact('faq'));
    }

    /**
     * Update the specified FAQ in storage.
     */
    public function update(UpdateFaqRequest $request, Faq $faq)
    {
        $data = $request->validated();

        $data['sort_order'] = $request->filled('sort_order') ? (int) $request->sort_order : 0;
        $data['is_active'] = $request->boolean('is_active', true);

        $faq->update($data);

        return redirect()
            ->route('admin.faqs.index')
            ->with('success', 'FAQ "' . $data['question'] . '" berhasil diperbarui.');
    }

    /**
     * Remove the specified FAQ from storage.
     */
    public function destroy(Faq $faq)
    {
        $question = $faq->question;
        $faq->delete();

        return redirect()
            ->route('admin.faqs.index')
            ->with('success', 'FAQ "' . $question . '" berhasil dihapus.');
    }
}

