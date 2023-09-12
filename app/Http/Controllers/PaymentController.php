<?php

namespace App\Http\Controllers;

use App\Models\Donations;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    //
    public function storeDonation(Request $request)
    {
        $donation = Donations::create(
            [
                'user_id' => $request->user_id,
                'amount' => (int)$request->amount/100,
                'recurring' => $request->recurring,
                'interval' => $request->interval
            ]

        );
        return response($donation, 201);
    }

    public function getDonations()
    {
        $donations = Donations::with('user')->get();

        return response($donations, 200);
    }

}
