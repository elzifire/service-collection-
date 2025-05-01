<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pendaftaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MualafController extends Controller
{
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'name' => 'required',
            'nik' => [
                'required',
                Rule::unique('mualaf.pendaftaran', 'nik'),
            ],
            'gender' => 'required',
            'tmptlahir' => 'required',
            'birthdate' => 'required|date',
            'pekerjaan' => 'required',
            'agama' => 'required',
            'kebangsaan' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('mualaf.pendaftaran', 'email'),
            ],
            'phone' => 'required',
            'address' => 'required',
            'alamatktp' => 'required',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Check for photo in the request and store it if exists
        $photoName = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = $photo->getClientOriginalName();
            $photo->storeAs('images', $photoName, 'mualaf');
        }

        // Prepare data for insertion
        $data = [
            'name' => $request->input('name'),
            'nik' => $request->input('nik'),
            'gender' => $request->input('gender'),
            'tmptlahir' => $request->input('tmptlahir'),
            'birthdate' => $request->input('birthdate'),
            'pekerjaan' => $request->input('pekerjaan'),
            'agama' => $request->input('agama'),
            'kebangsaan' => $request->input('kebangsaan'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'alamatktp' => $request->input('alamatktp'),
            'photo' => $photoName,
        ];

        // Insert data into database
        $store = DB::connection('mualaf')->table('pendaftaran')->insert($data);

        // Check if insertion was successful
        if ($store) {
            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil disimpan',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data gagal disimpan',
            ], 500);
        }
    }   
}
