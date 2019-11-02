<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index($roomId)
    {
        return view('index')->with('roomId', $roomId);
    }

    public function sendMessage(Request $request)
    {
        try {
            $data = [];
        
            $this->client = new Client(['base_uri' => env('nodeServer')]);
            
            $response = $this->client->request('POST', env('nodeServer')."send-message", [
                'form_params' => array_merge($data, array(
                    "room"          => "private:room:" . $request->roomId,
                    'message'       => $request->message,
                    'time'          => Carbon::now(),
                    'sender'        => request()->ip()
                ))
            ]);
            
            if ($response->getStatusCode() == 200) {
                // dd($response->getBody()->getContents());
                return Response::json('success');
            } else {
                return Response::json('error');
            }
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }
}
