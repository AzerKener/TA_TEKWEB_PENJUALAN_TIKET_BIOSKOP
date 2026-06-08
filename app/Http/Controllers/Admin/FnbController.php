<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FnbItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FnbController extends Controller
{
    public function index(Request $request)
    {
        $query = FnbItem::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $fnbItems = $query->orderBy('category')->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.fnb.index', compact('fnbItems'));
    }

    public function create()
    {
        return view('admin.fnb.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateFnb($request);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('fnb', 'public');
        }

        FnbItem::create($validated);

        return redirect()->route('admin.fnb.index')
            ->with('success', 'Item F&B berhasil ditambahkan!');
    }

    public function edit(FnbItem $fnb)
    {
        return view('admin.fnb.edit', compact('fnb'));
    }

    public function update(Request $request, FnbItem $fnb)
    {
        $validated = $this->validateFnb($request);

        if ($request->hasFile('image')) {
            if ($fnb->image) {
                Storage::disk('public')->delete($fnb->image);
            }
            $validated['image'] = $request->file('image')->store('fnb', 'public');
        }

        $fnb->update($validated);

        return redirect()->route('admin.fnb.index')
            ->with('success', 'Item F&B berhasil diperbarui!');
    }

    public function destroy(FnbItem $fnb)
    {
        if ($fnb->image) {
            Storage::disk('public')->delete($fnb->image);
        }

        $fnb->delete();

        return redirect()->route('admin.fnb.index')
            ->with('success', 'Item F&B berhasil dihapus.');
    }

    private function validateFnb(Request $request): array
    {
        return $request->validate([
            'name'         => ['required', 'string', 'max:200'],
            'description'  => ['nullable', 'string'],
            'category'     => ['required', 'in:food,drink,combo,snack'],
            'price'        => ['required', 'numeric', 'min:0'],
            'image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
            'stock'        => ['required', 'integer', 'min:0'],
            'is_available' => ['boolean'],
        ]);
    }
}
