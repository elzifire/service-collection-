<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FiqhController extends Controller
{
    public function index(Request $request)
    {
        // Validasi input
        $request->validate([
            "query" => "required|string",
            "top_k" => "nullable|integer",
            "rewrite" => "nullable|boolean",
            "limit" => "nullable|integer",
        ]);

        try {
            $payload = [
                "query"   => $request->input("query"),
                "top_k"   => $request->input("top_k", 3),
                "rewrite" => $request->input("rewrite", true),
                "limit"   => $request->input("limit", 0),
            ];

            $response = Http::post("http://127.0.0.1:8091/search-ai", $payload);

            if ($response->failed()) {
                return response()->json([
                    "status" => false,
                    "message" => "Gagal menghubungi AI server",
                    "error" => $response->json(),
                ], 500);
            }

            return response()->json([
                "status" => true,
                "data" => $response->json(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "Terjadi kesalahan.",
                "error" => $e->getMessage(),
            ], 500);
        }
    }
}
