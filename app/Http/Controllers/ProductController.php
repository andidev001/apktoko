<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductImport;
use Illuminate\Support\Facades\Response;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    public function dataIndex()
    {
        $products = Product::query();
        return datatables()->of($products)
            ->addColumn('stok_satuan_fmt', function ($product) {
                return (float) $product->stok . ' ' . e($product->satuan);
            })
            ->addColumn('harga_jual_fmt', function ($product) {
                $html = 'Rp ' . number_format($product->harga_jual, 0, ',', '.');
                if ($product->harga_grosir > 0 && $product->minimal_grosir > 0) {
                    $html .= '<br><small class="text-success" style="font-size:0.75rem;"><i class="fas fa-tags"></i> Grosir Rp ' . number_format($product->harga_grosir, 0, ',', '.') . ' (Min ' . (float) $product->minimal_grosir . ')</small>';
                }
                return $html;
            })
            ->addColumn('harga_beli_fmt', function ($product) {
                return 'Rp ' . number_format($product->harga_beli, 0, ',', '.');
            })
            ->addColumn('action', function ($product) {
                $formAction = route('products.destroy', $product->id);
                $csrf = csrf_field();
                $method = method_field('DELETE');

                $grosir = (float) ($product->harga_grosir ?: 0);
                $minGrosir = (float) ($product->minimal_grosir ?: 0);

                return '
                <button class="btn btn-sm btn-info text-white" onclick="editProduct(' . $product->id . ', \'' . $product->kode_barang . '\', \'' . addslashes($product->nama_barang) . '\', \'' . addslashes($product->satuan) . '\', ' . $product->harga_beli . ', ' . $product->harga_jual . ', \'' . addslashes($product->deskripsi) . '\', ' . $grosir . ', ' . $minGrosir . ')"><i class="fas fa-edit"></i></button>
                <form action="' . $formAction . '" method="POST" class="d-inline form-confirm" data-confirm-message="Yakin hapus barang ini secara permanen?">
                    ' . $csrf . ' ' . $method . '
                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                </form>
                ';
            })
            ->rawColumns(['harga_jual_fmt', 'action'])
            ->make(true);
    }

    public function dataStock()
    {
        $products = Product::query();
        return datatables()->of($products)
            ->addColumn('stok_badge', function ($product) {
                $cls = $product->stok > 10 ? 'primary' : 'danger';
                $stokTxt = (float) $product->stok;
                return '<h5><span class="badge bg-' . $cls . '">' . $stokTxt . ' ' . e($product->satuan) . '</span></h5>';
            })
            ->addColumn('action', function ($product) {
                return '
                <button class="btn btn-sm btn-primary" onclick="updateStockModal(' . $product->id . ', \'' . addslashes($product->nama_barang) . '\', \'tambah\')"><i class="fas fa-plus"></i></button>
                <button class="btn btn-sm btn-danger" onclick="updateStockModal(' . $product->id . ', \'' . addslashes($product->nama_barang) . '\', \'kurang\')"><i class="fas fa-minus"></i></button>
                ';
            })
            ->rawColumns(['stok_badge', 'action'])
            ->make(true);
    }

    public function data()
    {
        $products = Product::select(['id', 'kode_barang', 'nama_barang', 'harga_jual', 'stok', 'satuan', 'harga_grosir', 'minimal_grosir'])->where('stok', '>', 0);

        return datatables()->of($products)
            ->addColumn('harga_formatted', function ($product) {
                $html = 'Rp ' . number_format($product->harga_jual, 0, ',', '.');
                if ($product->harga_grosir > 0 && $product->minimal_grosir > 0) {
                    $html .= '<br><small class="text-success" style="font-size:0.75rem;"><i class="fas fa-tags"></i> Grosir Rp ' . number_format($product->harga_grosir, 0, ',', '.') . ' (Min ' . (float) $product->minimal_grosir . ')</small>';
                }
                return $html;
            })
            ->addColumn('stok_satuan', function ($product) {
                $stok = (float) $product->stok;
                return $stok . ' ' . $product->satuan;
            })
            ->addColumn('action', function ($product) {
                $grosir = (float) ($product->harga_grosir ?: 0);
                $minGrosir = (float) ($product->minimal_grosir ?: 0);
                return '<button class="btn btn-sm btn-primary" onclick="addToCart(' . $product->id . ', \'' . addslashes($product->nama_barang) . '\', ' . $product->harga_jual . ', ' . (float) $product->stok . ', \'' . addslashes($product->satuan) . '\', ' . $grosir . ', ' . $minGrosir . ')"><i class="fas fa-plus"></i> Pilih</button>';
            })
            ->rawColumns(['harga_formatted', 'action'])
            ->make(true);
    }

    public function getByBarcode($kode)
    {
        $product = Product::where('kode_barang', $kode)->where('stok', '>', 0)->first();
        if ($product) {
            return response()->json(['success' => true, 'data' => $product]);
        }
        return response()->json(['success' => false, 'message' => 'Barang tidak ditemukan atau stok kosong!']);
    }

    public function downloadTemplate()
    {
        return Excel::download(new \App\Exports\TemplateExport, 'template_barang.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:csv,txt,xlsx,xls'
        ]);

        try {
            Excel::import(new ProductImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data barang berhasil diimpor!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal impor data: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|unique:products',
            'nama_barang' => 'required',
            'satuan' => 'nullable|string',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'harga_grosir' => 'nullable|numeric',
            'minimal_grosir' => 'nullable|numeric'
        ]);

        Product::create($request->all());
        return redirect()->route('products.index')->with('success', 'Barang berhasil ditambahkan');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'kode_barang' => 'required|unique:products,kode_barang,' . $product->id,
            'nama_barang' => 'required',
            'satuan' => 'nullable|string',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'harga_grosir' => 'nullable|numeric',
            'minimal_grosir' => 'nullable|numeric'
        ]);

        $product->update($request->all());
        return redirect()->route('products.index')->with('success', 'Barang berhasil diupdate');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Barang berhasil dihapus');
    }

    public function stock()
    {
        $products = Product::all();
        return view('products.stock', compact('products'));
    }

    public function updateStock(Request $request, Product $product)
    {
        $request->validate(['stok' => 'required|numeric']);
        if ($request->tipe == 'tambah') {
            $product->stok += $request->stok;
            StockHistory::create(['product_id' => $product->id, 'type' => 'in', 'qty' => $request->stok, 'keterangan' => $request->keterangan ?? 'Barang Masuk']);
        } else {
            $product->stok -= $request->stok;
            StockHistory::create(['product_id' => $product->id, 'type' => 'out', 'qty' => $request->stok, 'keterangan' => $request->keterangan ?? 'Barang Keluar']);
        }
        $product->save();
        return redirect()->route('products.stock')->with('success', 'Stok berhasil diupdate');
    }
}
