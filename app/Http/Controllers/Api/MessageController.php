<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'friend_id' => 'required|integer|exists:users,id',
        ]);

        $user = $request->user();
        $friend = $request->friend_id;

        $messages = Message::where(function ($q) use ($user, $friend) {
                $q->where('sender_id', $user->id)
                  ->where('receiver_id', $friend);
            })
            ->orWhere(function ($q) use ($user, $friend) {
                $q->where('sender_id', $friend)
                  ->where('receiver_id', $user->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'status' => 200,
            'messages' => $messages
        ]);
    }

   public function store(Request $request)
{
    $request->validate([
        'sender_id' => 'required|integer',
        'receiver_id' => 'required|integer|exists:users,id',
        'message' => 'required|string',
    ]);

    // if ($request->sender_id != $request->user()->id) {
    //     return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
    // }

    $result = DB::transaction(function () use ($request) {
        $msg = Message::create([
            'sender_id' => $request->sender_id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        try {
            // pakai curl untuk broadcast
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://masjid.uika-bogor.ac.id/chat-api/broadcast");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json"
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'sender_id'   => $msg->sender_id,
                'receiver_id' => $msg->receiver_id,
                'message'     => $msg->message,
                'created_at'  => $msg->created_at,
            ]));

            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                logger()->error("Curl error broadcast: " . curl_error($ch));
            }
            curl_close($ch);

            logger()->info("Broadcast response: " . $response);
        } catch (\Exception $e) {
            logger()->error("Gagal broadcast ke Socket.IO: " . $e->getMessage());
        }

        return $msg;
    });

    return response()->json([
        'status' => true,
        'data' => $result
    ]);
}

}