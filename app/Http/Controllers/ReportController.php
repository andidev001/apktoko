<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $harian = Transaction::where('status', 'lunas')->whereDate('created_at', today())->sum('total_harga');
        $bulanan = Transaction::where('status', 'lunas')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->sum('total_harga');
        $omzet = Transaction::where('status', 'lunas')->sum('total_harga');

        return view('reports.index', compact('harian', 'bulanan', 'omzet'));
    }

    public function data(Request $request)
    {
        $reports = Transaction::with(['details.product', 'payment'])->orderBy('created_at', 'desc');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $reports->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        return datatables()->of($reports)
            ->addColumn('waktu', function ($trx) {
                return $trx->created_at->format('d M Y H:i');
            })
            ->editColumn('no_faktur', function ($trx) {
                $pembeli = $trx->nama_pelanggan ? '<br><small class="text-muted"><i class="fas fa-user"></i> ' . e($trx->nama_pelanggan) . '</small>' : '';
                return '<strong>' . $trx->no_faktur . '</strong>' . $pembeli;
            })
            ->addColumn('items', function ($trx) {
                $html = '<ul class="mb-0 ps-3 text-muted" style="font-size: 0.85rem">';
                foreach ($trx->details as $d) {
                    $satuan = $d->product->satuan ?? 'Pcs';
                    $jumlah = (float) $d->jumlah;
                    $html .= '<li>' . $d->product->nama_barang . ' (' . $jumlah . ' ' . $satuan . ' x Rp ' . number_format($d->harga_jual, 0, ',', '.') . ')</li>';
                }
                $html .= '</ul>';
                return $html;
            })
            ->addColumn('total', function ($trx) {
                return '<span class="text-primary fw-bold">Rp ' . number_format($trx->total_harga, 0, ',', '.') . '</span>';
            })
            ->addColumn('bayar_kembali', function ($trx) {
                if ($trx->status === 'pending') {
                    return '<span class="badge bg-warning text-dark">Belum Lunas</span>';
                }
                if ($trx->payment) {
                    return 'Bayar: Rp ' . number_format($trx->payment->jumlah_bayar, 0, ',', '.') . '<br><small class="text-success">Kembali: Rp ' . number_format($trx->payment->kembalian, 0, ',', '.') . '</small>';
                }
                return '<span class="badge bg-success">Lunas</span>';
            })
            ->addColumn('action', function ($trx) {
                if ($trx->status === 'pending') {
                    $cancelRoute = route('transactions.cancel', $trx->id);
                    $csrf = csrf_field();
                    return '
                        <a href="' . route('transactions.paymentForm', $trx->id) . '" class="btn btn-sm btn-success mb-1 w-100"><i class="fas fa-money-bill-wave"></i> Bayar Sekarang</a>
                        <button type="button" data-url="' . route('transactions.invoice', $trx->id) . '" class="btn btn-sm btn-outline-primary mb-1 w-100 btn-cetak-invoice"><i class="fas fa-file-invoice"></i> Cetak Invoice</button>
                        <form action="' . $cancelRoute . '" method="POST" class="form-confirm" data-confirm-message="Yakin ingin membatalkan transaksi ini? Stok barang akan dikembalikan otomatis.">
                            ' . $csrf . '
                            <button type="submit" class="btn btn-sm btn-outline-danger w-100"><i class="fas fa-trash"></i> Batal</button>
                        </form>
                    ';
                }
                $receiptUrl = route('transactions.receipt', $trx->id);
                return '<button type="button" data-url="' . $receiptUrl . '" class="btn btn-sm btn-secondary w-100 btn-cetak-struk"><i class="fas fa-print"></i> Cetak Struk</button>';
            })
            ->rawColumns(['no_faktur', 'items', 'total', 'bayar_kembali', 'action'])
            ->make(true);
    }

    public function rekap()
    {
        return view('reports.rekap');
    }

    public function printRekap(Request $request)
    {
        $query = Transaction::with(['details.product', 'payment'])->orderBy('created_at', 'asc');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        $transactions = $query->get();
        $setting = \App\Models\Setting::first();

        return view('reports.print-rekap', compact('transactions', 'setting', 'request'));
    }
}
