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

class DonationController extends Controller
{
    // get all donations
    public function index(Request $request)
    {
 
        $query = DB::connection('donasi')->table('campaign');

        // validate query parameters
        $validator = Validator::make($request->all(), [
            'search' => 'string|nullable',
            'category_id' => 'integer|exists:donasi.categories,id|nullable',
        ]);

        // 1) Searching: cari pada kolom title atau description
        if ($request->filled('search')) {
        $search = $request->get('search');
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });

        // 2) Filter by category
        if ($request->filled('category_id')) {
        $query->where('category_id', $request->get('category_id'));
        }

        $campaign = $query->get()->paginate(4);
        return response()->json([
            'status' => 'success',
            'data'   => $campaign
        ], 200);
    }
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

    public function showDonationsByUser(Request $request)
    {
        // Ambil user dari koneksi 'mysql'
        $user = DB::connection('mysql')->table('users')
            ->where('id', Auth::id())
            ->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Ambil semua data donation dari koneksi 'donasi' berdasarkan user_id
        $donations = DB::connection('donasi')->table('donations')
            ->where('user_id', $user->id)
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $donations
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

        $validated = $request->validate([
            'amount'      => 'required|numeric',
            'proof_image' => 'required|',
            'campaign_id' => 'required|exists:donasi.campaigns,id', // Sesuaikan koneksi donasi
        ]);

        // image upload
        $proofImage = $request->file('proof_image');
        $proofImage->storeAs('public/donations', $proofImage->hashName());


        // Ambil user dari koneksi 'mysql'
        $user = DB::connection('mysql')->table('users')->where('id', Auth::id())->first();

        
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Simpan data ke dalam tabel 'donations' di koneksi 'donasi'
        $donationId = DB::connection('donasi')->table('donations')->insertGetId([
            'user_id'     => $user->id,
            'campaign_id' => $validated['campaign_id'],
            'amount'      => $validated['amount'],
            'proof_image' => $proofImage->hashName(),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // Ambil data donation yang baru dibuat
        $donation = DB::connection('donasi')->table('donations')->where('id', $donationId)->first();

        // mengembalikan response 2 variable pertama $donationId dan $donation
        return response()->json([
            'status' => 'success',
            'data'   => $donation
        ], 201);
    }
}
