<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'kode_barang',
            'nama_barang',
            'satuan',
            'harga_beli',
            'harga_jual',
            'harga_grosir',
            'minimal_grosir',
            'stok',
            'deskripsi'
        ];
    }

    public function array(): array
    {
        return [
            ['BRG001', 'Sabun Mandi', 'Pcs', 2500, 3000, 2800, 12, 100, 'Sabun mandi contoh'],
            ['BRG002', 'Beras Murni', 'Kg', 13500, 15000, 14000, 25, 50.5, 'Beras curah per Kg'],
        ];
    }
}
