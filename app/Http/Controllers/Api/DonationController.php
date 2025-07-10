<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Donation;
use App\Models\Campaign;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class DonationController extends Controller
{

    // get all donations
    public function index()
    {
        $campaign = DB::connection('donasi')->table('campaigns')->get();
        return response()->json([
            'status' => 'success',
            'data'   => $campaign
        ], 200);
    }


    // get image
    public function getImage($id)
    {
        $donation = Donation::find($id);
        if (!$donation) {
            return response()->json(['error' => 'Donation not found'], 404);
        }
        return response()->json([
            'status' => 'success',
            'data' => $donation
        ], 200);
    }
    

    // get campaign by id
    public function show($id)
    {
        $campaign = Campaign::find($id);
        if (!$campaign) {
            return response()->json(['error' => 'Campaign not found'], 404);
        }

        // echo config('app.timezone');

        return response()->json([
            'status' => 'success',
            'data' => $campaign
        ], 200);
    }


    public function store(Request $request)
    {
        // Validasi dasar untuk semua donasi
        $rules = [
            'amount'      => 'required|numeric|min:1',
            'proof_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'campaign_id' => 'required|exists:donasi.campaigns,id',
        ];

        // Tambah validasi untuk donasi umum
        if (!Auth::check()) {
            $rules['name'] = 'required|string|max:255';
            $rules['phone_number'] = 'required|string|max:20';
        }

        $validated = $request->validate($rules);

        // Simpan gambar bukti donasi
        $proofImage = $request->file('proof_image');
        $proofImageName = $proofImage->hashName();
        $proofImage->storeAs('donations', $proofImageName, 'donasi');

        // Data dasar untuk donasi
        $donationData = [
            'campaign_id'    => $validated['campaign_id'],
            'amount'        => $validated['amount'],
            'proof_image'   => $proofImageName,
            'donation_type' => Auth::check() ? 'terdaftar' : 'umum',
            'created_at'    => now(),
            'updated_at'    => now(),
        ];

        // Cek apakah user terautentikasi (terdaftar)
        if (Auth::check()) {
            $user = DB::connection('mysql')->table('users')->where('id', Auth::id())->first();
            if (!$user) {
                return response()->json(['error' => 'User tidak ditemukan'], 404);
            }
            $donationData['user_id'] = $user->id;
        } else {
            // Untuk donasi umum, simpan nama dan nomor telepon
            $donationData['name'] = $validated['name'];
            $donationData['phone_number'] = $validated['phone_number'];
        }

        // Simpan donasi ke database
        $donationId = DB::connection('donasi')->table('donations')->insertGetId($donationData);

        // Ambil data donasi yang baru dibuat
        $donation = DB::connection('donasi')->table('donations')->where('id', $donationId)->first();

        return response()->json([
            'status' => 'success',
            'data'   => $donation
        ], 201);
    }

    // get all donations by user
    public function donated()
    {
        $userId = DB::connection('mysql')->table('users')->where('id', Auth::id())->first();

    if (!$userId) {
        return response()->json(['error' => 'User not found'], 404);
    }

    $donations = DB::connection('donasi')->table('donations')
        ->where('user_id', $userId->id)
        ->get();

    // Menggunakan response()->json untuk langsung mengonversi data ke JSON
    return response()->json($donations);
    }
}
