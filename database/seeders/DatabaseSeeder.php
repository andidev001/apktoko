<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin toko',
            'email' => 'admin@toko.com',
            'password' => Hash::make('password'),
        ]);

        Product::create([
            'kode_barang' => 'BRG001',
            'nama_barang' => 'Kopi Kapal Api',
            'harga_beli' => 12000,
            'harga_jual' => 15000,
            'stok' => 100,
            'deskripsi' => 'Kopi hitam'
        ]);
        Product::create([
            'kode_barang' => 'BRG002',
            'nama_barang' => 'Indomie Goreng',
            'harga_beli' => 2500,
            'harga_jual' => 3000,
            'stok' => 50,
            'deskripsi' => 'Mie instan'
        ]);
    }
}
