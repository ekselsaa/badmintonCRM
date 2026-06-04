<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fasilitas;

class FasilitasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fasilitas = [
            [
                'nama' => 'Sewa Raket',
                'harga' => 25000,
                'stok' => 4,
                'icon' => 'bi-usb-c', // Using bi-usb-c as racket-like shape for now
                'is_active' => true,
            ],
            [
                'nama' => 'Kok Satuan',
                'harga' => 15000,
                'stok' => 50,
                'icon' => 'bi-circle', // ball shape
                'is_active' => true,
            ],
            [
                'nama' => 'Kok 1 Slop',
                'harga' => 150000,
                'stok' => 10,
                'icon' => 'bi-box-seam',
                'is_active' => true,
            ],
            [
                'nama' => 'Air Mineral',
                'harga' => 5000,
                'stok' => 100,
                'icon' => 'bi-droplet',
                'is_active' => true,
            ]
        ];

        foreach ($fasilitas as $f) {
            Fasilitas::create($f);
        }
    }
}
