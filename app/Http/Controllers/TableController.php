<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiningTable;
use Illuminate\Support\Str;

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
            'status' => 'available',
            'qr_token' => Str::random(32),
            'is_active' => true
        ]);

        return redirect()->route('tables.index')->with('success', 'Meja baru dan QR Code berhasil ditambahkan.');
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

    /**
     * Regenerate token QR unik meja
     */
    public function regenerateQr($id)
    {
        $table = DiningTable::findOrFail($id);
        $table->update([
            'qr_token' => Str::random(32)
        ]);

        return redirect()->route('tables.index')->with('success', "QR Code untuk {$table->name} berhasil diperbarui (token baru telah aktif).");
    }

    /**
     * Toggle status aktif meja
     */
    public function toggleActive($id)
    {
        $table = DiningTable::findOrFail($id);
        $table->update([
            'is_active' => !$table->is_active
        ]);

        $statusStr = $table->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('tables.index')->with('success', "Meja {$table->name} berhasil {$statusStr}.");
    }

    /**
     * Tampilkan halaman printable QR Meja Kulu Asri
     */
    public function printQr($id)
    {
        $table = DiningTable::findOrFail($id);
        return view('admin.print_qr', compact('table'));
    }
}
