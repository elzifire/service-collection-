<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ZakatController extends Controller
{
    public function hitungZakatEmas(Request $request)
    {
        $request->validate([
            'jumlahemas' => 'required|numeric',
            'hargaemas_per_gram' => 'required|numeric',
        ]);

        $jumlahemas = $request->jumlahemas;
        $hargaemas_per_gram = $request->hargaemas_per_gram;

        $hargaemas = $jumlahemas * $hargaemas_per_gram;

        $total = $hargaemas * 0.025;

        // jika total harga dengan kurun waktu setahun maka di bayar zakat 2.5%
        // jika kurang dari 85 gram maka tidak wajib membayar zakat
        if ($jumlahemas >= 85) {
            $total = $hargaemas * 0.025;
        } else {
            $total = 0;
        }

        return response()->json([
            'hargaemas' => $hargaemas,
            'total' => $total,
        ]);
    }

    public function hitungZakatPerak(Request $request)
    {
        $request->validate([
            'jumlahperak' => 'required|numeric',
            'hargaperak_per_gram' => 'required|numeric',
        ]);

        $jumlahperak = $request->jumlahperak;
        $hargaperak_per_gram = $request->hargaperak_per_gram;

        $hargaperak = $jumlahperak * $hargaperak_per_gram;

        $total = $hargaperak * 0.025;

        return response()->json([
            'hargaperak' => $hargaperak,
            'total' => $total,
        ]);
    }

    public function hitungZakatPerdagangan(Request $request)
    {
        $request->validate([
            'asset' => 'required|numeric',
            'laba' => 'required|numeric',
        ]);
        $asset = $request->asset;
        $laba = $request->laba;

        $total = $asset + $laba;

        if ($total >= 82312725) {
            $total = $total * 0.025;
        } else {
            $total = 0;
        }

        // $modal = $request->modal;
        // $keuntungan = $request->keuntungan;

        // $jumlah = $modal + $keuntungan;

        // if ($jumlah >= 82312725) {
        //     $total = $jumlah * 0.025;
        // } else {
        //     $total = 0;
        // }

        // return response()->json([
        //     'total' => $total,
        // ]);

    }

    public function hitungZakatPertanian(Request $request)
    {
        $request->validate([
            'hasilpanen' => 'required|numeric',
            'harga' => 'required|numeric',
        ]);

        $hasilpanen = $request->hasilpanen;
        $harga = $request->harga;

        $total = $hasilpanen * $harga * 0.025;

        return response()->json([
            'total' => $total,
        ]);
    }

    
}
