<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->orderBy('name', 'asc')->paginate(20);
        $categories = Category::orderBy('name', 'asc')->get();
        return view('admin.products', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|max:3072',
            'image_url' => 'nullable|url|max:500',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        } elseif ($request->filled('image_url')) {
            $data['image'] = $request->input('image_url');
        }

        unset($data['image_url']);

        Product::create($data);
        return back()->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|max:3072',
            'image_url' => 'nullable|url|max:500',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        } elseif ($request->filled('image_url')) {
            $data['image'] = $request->input('image_url');
        }

        unset($data['image_url']);

        $product->update($data);
        return back()->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        try {
            $product->delete();
            return back()->with('success', 'Produk berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'Gagal: Produk tidak bisa dihapus karena sudah tercatat dalam riwayat transaksi. Silakan set Stok menjadi 0 untuk menonaktifkannya.');
        }
    }
}
