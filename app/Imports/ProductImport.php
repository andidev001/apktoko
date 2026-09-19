<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class ProductImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Pastikan ada kode_barang dan nama_barang
        if (!isset($row['kode_barang']) || trim($row['kode_barang']) === '') {
            return null; // Lewati baris kosong
        }

        // Cek jika produk dengan kode ini sudah ada (update atau skip) -> Kita update saja
        $product = Product::where('kode_barang', $row['kode_barang'])->first();

        $data = [
            'nama_barang' => $row['nama_barang'],
            'satuan' => $row['satuan'] ?? 'Pcs',
            'harga_beli' => $row['harga_beli'] ?? 0,
            'harga_jual' => $row['harga_jual'] ?? 0,
            'harga_grosir' => $row['harga_grosir'] ?? 0,
            'minimal_grosir' => $row['minimal_grosir'] ?? 0,
            'stok' => $row['stok'] ?? 0,
            'deskripsi' => $row['deskripsi'] ?? null
        ];

        if ($product) {
            $product->update($data);
            return $product;
        }

        $data['kode_barang'] = $row['kode_barang'];
        return new Product($data);
    }
}
