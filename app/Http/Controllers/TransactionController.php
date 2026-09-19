<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockHistory;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        $products = Product::where('stok', '>', 0)->get();
        $transactions = Transaction::orderBy('created_at', 'desc')->get();
        return view('transactions.index', compact('products', 'transactions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|array',
            'product_id.*' => 'required|exists:products,id',
            'qty' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $total = 0;
            $transaction = Transaction::create([
                'no_faktur' => 'TRX-' . strtoupper(Str::random(6)),
                'nama_pelanggan' => $request->nama_pelanggan,
                'status' => 'pending'
            ]);

            foreach ($request->product_id as $key => $id) {
                $product = Product::find($id);
                $qty = $request->qty[$key];
                if ($qty > 0) {
                    $hargaJual = $product->harga_jual;

                    // Cek harga grosir
                    if (!empty($product->minimal_grosir) && !empty($product->harga_grosir) && $qty >= $product->minimal_grosir) {
                        $hargaJual = $product->harga_grosir;
                    }

                    $subtotal = $hargaJual * $qty;
                    $total += $subtotal;

                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $product->id,
                        'jumlah' => $qty,
                        'harga_beli' => $product->harga_beli,
                        'harga_jual' => $hargaJual,
                        'subtotal' => $subtotal
                    ]);

                    // Kurangi stok
                    $product->stok -= $qty;
                    $product->save();

                    StockHistory::create([
                        'product_id' => $product->id,
                        'type' => 'out',
                        'qty' => $qty,
                        'keterangan' => 'Penjualan ' . $transaction->no_faktur
                    ]);
                }
            }
            $transaction->total_harga = $total;
            $transaction->save();
            DB::commit();

            return redirect()->route('transactions.paymentForm', $transaction->id);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function paymentForm(Transaction $transaction)
    {
        return view('transactions.payment', compact('transaction'));
    }

    public function processPayment(Request $request, Transaction $transaction)
    {
        $request->validate([
            'jumlah_bayar' => 'required|numeric|min:' . $transaction->total_harga
        ]);

        $kembalian = $request->jumlah_bayar - $transaction->total_harga;

        Payment::create([
            'transaction_id' => $transaction->id,
            'jumlah_bayar' => $request->jumlah_bayar,
            'kembalian' => $kembalian
        ]);

        $transaction->status = 'lunas';
        $transaction->save();

        return redirect()->route('transactions.index')
            ->with('success', 'Pembayaran Berhasil! Transaksi lunas.')
            ->with('print_receipt_url', route('transactions.receipt', $transaction->id));
    }

    public function receipt(Transaction $transaction)
    {
        $transaction->load(['details.product', 'payment']);
        return view('transactions.receipt', compact('transaction'));
    }

    public function invoice(Transaction $transaction)
    {
        $transaction->load(['details.product']);
        return view('transactions.invoice', compact('transaction'));
    }

    public function updateName(Request $request, Transaction $transaction)
    {
        $transaction->update(['nama_pelanggan' => $request->nama_pelanggan]);
        return response()->json(['success' => true]);
    }

    public function cancel(Transaction $transaction)
    {
        DB::beginTransaction();
        try {
            foreach ($transaction->details as $detail) {
                // Restore stock
                $product = $detail->product;
                $product->stok += $detail->jumlah;
                $product->save();

                // Log stock return
                StockHistory::create([
                    'product_id' => $product->id,
                    'type' => 'in',
                    'qty' => $detail->jumlah,
                    'keterangan' => 'Pembatalan Transaksi ' . $transaction->no_faktur
                ]);

                $detail->delete();
            }

            $transaction->delete();

            DB::commit();
            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dibatalkan dan stok telah dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }
}
