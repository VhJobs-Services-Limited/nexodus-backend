<?php

namespace App\Http\Controllers\Api\V1;

use App\Jobs\ProcessClubConnectOrderJob;
use Illuminate\Http\Request;

class ClubConnectCallbackController
{
    /**
     *
     */
    public function __invoke(Request $request)
    {
        ProcessClubConnectOrderJob::dispatch(collect(["reference" => $request->get('requestid'), "status" => $request->get('orderstatus')]));

        return response()->json(['message' => 'Order received']);
    }
}
