<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiningTable;

class TableController extends Controller
{
    public function index()
    {
        $tables = DiningTable::orderBy('id')->get();
        return view('admin.tables', compact('tables'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:dining_tables,name'
        ]);

        DiningTable::create([
            'name' => $request->name,
            'status' => 'available'
        ]);

        return redirect()->route('tables.index')->with('success', 'Meja berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:dining_tables,name,' . $id
        ]);

        $table = DiningTable::findOrFail($id);
        $table->update([
            'name' => $request->name
        ]);

        return redirect()->route('tables.index')->with('success', 'Nama meja berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $table = DiningTable::findOrFail($id);
        
        if ($table->status === 'occupied') {
            return redirect()->route('tables.index')->with('error', 'Meja sedang terisi dan tidak dapat dihapus.');
        }

        $table->delete();
        return redirect()->route('tables.index')->with('success', 'Meja berhasil dihapus.');
    }
}
