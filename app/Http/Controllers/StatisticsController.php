<?php

namespace App\Http\Controllers;

use App\Models\Donations;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Mail;

class StatisticsController extends Controller
{
    //
    public function getDonorNumber()
    {
        $donorNumber = Donations::distinct('user_id')->count();
        $user = User::where('role', '=', 0)->count();
        $response = [
            'donor_no' => $donorNumber,
            'user_no' => $user
        ];

        return response($response, 200);
    }
    public function getDonorType()
    {
        $donation = Donations::count();
        $onetime = Donations::where('recurring', '=', 0)->count();
        $recurring = Donations::where('recurring', '=', 1)->count();
        $response = [
            'donation' => $donation,
            'onetime' => $onetime,
            'recurring' => $recurring
        ];
        return response($response, 200);
    }

    public function getRecurringType()
    {
        $recurring_no = Donations::where('recurring', '=', 1)->count();
        $daily = Donations::where('interval', '=', 'day')->count();
        $weekly = Donations::where('interval', '=', 'week')->count();
        $monthly = Donations::where('interval', '=', 'month')->count();
        $yearly = Donations::where('interval', '=', 'year')->count();
        $response = [
            'donation' => $recurring_no,
            'daily' => $daily,
            'weekly' => $weekly,
            'monthly' => $monthly,
            'yearly' => $yearly
        ];
        return response($response, 200);
    }

    public function getUserRegistrationCountsLast7Days()
    {
        $end = Carbon::now();
        $start = Carbon::now()->subDays(6);

        //selecting user registered within the last 7 days
        $response = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->orderBy('date')
            ->get();


        //setting the date for the last 7 days and the count to 0 for each day
        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $start->copy()->addDays($i)->format('Y-m-d');
            $days[$day] = 0;
        }


        //for the days user have registered, set the count to the number of users registered on that day
        foreach ($response as $row) {
            $days[$row->date] = $row->count;
        }


        //mapping to desired format
        $days = array_map(function ($date, $count) {
            return [
                'date' => $date,
                'count' => $count,
            ];
        }, array_keys($days), $days);



        return response($days, 200);
    }


    public function getUserLoginData()
    {
        $end = Carbon::now();
        $start = Carbon::now()->subDays(6);


        //selecting user logged in within the last 7 days 
        // ( note : change the table name)
        $response = PersonalAccessToken::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->orderBy('date')
            ->get();


        //setting the date for the last 7 days and the count to 0 for each day
        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $start->copy()->addDays($i)->format('Y-m-d');
            $days[$day] = 0;
        }

        //for the days user have logged in, set the count to the number of users logged in on that day
        foreach ($response as $row) {
            $days[$row->date] = $row->count;
        }

        //mapping to desired format
        $days = array_map(function ($date, $count) {
            return [
                'date' => $date,
                'count' => $count,
            ];
        }, array_keys($days), $days);

        return response($days, 200);
    }

    public function mail(Request $request)
    {


        $htmlContent = '<!DOCTYPE html>
        <html>
        <head>
            <title>Donation Receipt</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                }
                .receipt {
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                    border: 1px solid #ccc;
                    border-radius: 5px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                }
                .thank-you {
                    text-align: center;
                    font-size: 24px;
                    margin-bottom: 10px;
                }
                .message {
                    margin-bottom: 30px;
                }
                .details {
                    border-top: 1px solid #ccc;
                    padding-top: 10px;
                    margin-top: 20px;
                }
                .details p {
                    margin: 5px 0;
                }
            </style>
        </head>
        <body>
            <div class="receipt">
                <div class="header">
                    <h1>Donation Receipt</h1>
                </div>
                <div class="thank-you">Thank you for your generous ' . $request["type"] .'!</div>
                <div class="message">
                    <p>We greatly appreciate your contribution to our cause. Your support helps us make a difference.</p>
                </div>
                <div class="details">
                    <p><strong>Donation Date:</strong> ' . $request["date"] .'</p>
                    <p><strong>Donation Amount:</strong> ' .(int)  $request["amount"] / 100 .'</p>
                    
                </div>
                <div class="footer">
                    <p>If you have any questions, please contact us at mbansw@gmail.com.</p>
                    <p>Thank you once again for your support!</p>
                </div>
            </div>
        </body>
        </html>
        ';
        $email = $request['email'];
        $name = $request['name'];

        Mail::html($htmlContent, function ($message) use ($email, $name) {
            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            $message->subject('Donation Receipt');
            $message->to($email, $name);
        });

        $response = [
            'to' => $email,
            'name' => $name,
            'amount' => (int) ($request['amount']) / 100
        ];


        return response($response, 200);

    }
}
