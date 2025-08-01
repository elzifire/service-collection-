<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


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
        return response()->json([
            'total' => $total,
        ]);

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

    // zakat penghasilan

    // data yang di inputkan
    // 1. gaji perbulan
    // 2. penghasilan lain-lain perbulan
    // 3. jumlah penghasilan perbulan
    // 4. nisab pertahun (Rp. 85.685.972)
    // 5. nisab perbulan (Rp. 7.140.498)
    public function hitungZakatPenghasilanBulan(Request $request)
{
    $request->validate([
        'gaji' => 'required|numeric',
        'penghasilan_lain' => 'required|numeric',
    ]);

    $gaji = $request->gaji;
    $penghasilan_lain = $request->penghasilan_lain;

    // Hitung total penghasilan per bulan
    $total_penghasilan = $gaji + $penghasilan_lain;

    // Batas minimum penghasilan yang wajib zakat
    $batas_wajib_zakat = 7140498;

    if ($total_penghasilan >= $batas_wajib_zakat) {
        // Wajib zakat (karena total >= batas)
        $zakat = $total_penghasilan * 0.025; // 2.5%
        $status = "Wajib Zakat";
    } else {
        // Tidak wajib zakat (karena total < batas)
        $zakat = 0;
        $status = "Tidak Wajib Zakat";
    }

    return response()->json([
        'status' => $status,
        'total_penghasilan' => $total_penghasilan,
        'jumlah_zakat' => $zakat,
    ]);
}


    public function hitungZakatPenghasilanTahun(Request $request)
{
    $request->validate([
        'gaji' => 'required|numeric|min:0',
        'penghasilan_lain' => 'required|numeric|min:0',
    ]);

    $gaji = $request->gaji;
    $penghasilan_lain = $request->penghasilan_lain;

    // Hitung total penghasilan tahunan
    $total_penghasilan = ($gaji + $penghasilan_lain) * 12;

    // Nisab tahunan
    $nisab_tahunan = 85685972;

    if ($total_penghasilan >= $nisab_tahunan) {
        $zakat = $total_penghasilan * 0.025; // 2.5%
        return response()->json([
            'status' => 'Wajib Zakat',
            'total_penghasilan' => $total_penghasilan,
            'jumlah_zakat' => $zakat,
        ], 200);
    } else {
        return response()->json([
            'status' => 'Tidak Wajib Zakat',
            'total_penghasilan' => $total_penghasilan,
            'jumlah_zakat' => 0,
        ], 200);
    }
}

    public function ZakatMal(Request $request)
    {
        // cek user login
        if (!$request->user()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = DB::connection('mysql')->table('users')->where('id', Auth::id())->first();

        
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $request->validate([
            'nominal' => 'required|numeric',
        ]);

        $nominal = $request->nominal;
        $total = $nominal * 0.025;

        if ($nominal >= 85) {
            return response()->json([
                'message' => 'Zakat mal',
                'total' => $total,
            ]);
        } else {
            return response()->json([
                'message' => 'Zakat mal anda belum mencapai nisab',
                'total' => 0,
            ]);
        } 
        
    }

    // public function transaksi(Request $request)
    // {
    //     // cek user login
    //     if (!$request->user()) {
    //         return response()->json(['error' => 'Unauthorized'], 401);
    //     }

    //     $user = DB::connection('mysql')->table('users')->where('id', Auth::id())->first();

        
    //     if (!$user) {
    //         return response()->json(['error' => 'User not found'], 404);
    //     }

    //     $request->validate([
    //         'notransaksi' => 'required|numeric',
    //         'nama_user' => 'required|string',
    //         'jenis zakat' => 'required|string',
    //         'jumlah_zakat' => 'required|numeric',
    //         'metode_bayar' => 'required|string',
    //         'id_user' => 'required|numeric',
    //     ]);

    //     $file = $request->file('bukti_transfer');
    //     $filename = time() . '.' . $file->getClientOriginalExtension();
    //     $path = file()->storeAs('img/bukti_transfer', $filename, 'public');

    //     $no_transaksi = now()->format()

    //     $query = DB::connection('zakat')->table('transaksi')->insert([
    //         'notransaksi' => $request->notransaksi,
    //         'nama_user' => $request->nama_user,
    //         'jenis_zakat' => $request->jenis_zakat,
    //         'jumlah_zakat' => $request->jumlah_zakat,
    //         'metode_bayar' => $request->metode_bayar,
    //         'id_user' => $request->user,
    //     ]);

        

        
    
    // }
    
}
