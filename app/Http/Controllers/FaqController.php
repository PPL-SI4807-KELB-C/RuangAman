<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    /**
     * Menampilkan halaman Pusat Bantuan (FAQ publik) yang dapat diakses tanpa login.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $faqs = Faq::query()
            ->published()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('pertanyaan', 'like', "%{$search}%")
                        ->orWhere('jawaban', 'like', "%{$search}%");
                });
            })
            ->ordered()
            ->get();

        return view('faq.index', [
            'title' => 'Pusat Bantuan (FAQ) - RuangAman',
            'faqs' => $faqs,
            'search' => $search,
        ]);
    }
}
