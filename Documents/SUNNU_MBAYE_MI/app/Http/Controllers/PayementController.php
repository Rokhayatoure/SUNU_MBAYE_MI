<?php

namespace App\Http\Controllers;

use App\Models\Payment;



use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Services\PaytechService;
use App\Http\Requests\PayementRequest;
use Illuminate\Support\Facades\Redirect;

class PayementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */

     

    public function index()
    {

        return view('index');
    }
    

    public function payment(PayementRequest $request){
        //  dd($request->all());
        # send info to api paytech

        $IPN_URL = 'https://urltowebsite.com';

        $amount = $request->input('price');
       $commende_id = $request->input('commende_id');
        $code = "47";

        $success_url = route('payment.success', [
            'code' => $code, 
            'data' => [
                'amount' => $request->price,
                'commende_id' =>$commende_id
            ],
        ]);
        $cancel_url = route('payment.index');
        $paymentService = new PaytechService(config('paytech.PAYTECH_API_KEY'), config('paytech.PAYTECH_SECRET_KEY'));

        $jsonResponse = $paymentService->setQuery([
            'commende_id' =>$commende_id,
            'item_price' => $amount,
            'command_name' => "Paiement pour l'achat de via PayTech",
        ])
        ->setCustomeField([
            'time_command' => time(),
            'ip_user' => $_SERVER['REMOTE_ADDR'],
            'lang' => $_SERVER['HTTP_ACCEPT_LANGUAGE']
        ])
        ->setTestMode(true)
        ->setCurrency("xof")
        ->setRefCommand(uniqid())
        ->setNotificationUrl([
            'ipn_url' => $IPN_URL . '/ipn',
            'success_url' => $success_url,
            'cancel_url' =>  $cancel_url
        ])->send();

        if ($jsonResponse['success'] < 0) {
            // return back()->withErrors($jsonResponse['errors'][0]);
            return 'error';
        } elseif ($jsonResponse['success'] == 1) {
            # Redirection to Paytech website for completing checkout
            $token = $jsonResponse['token'];
            session(['token' => $token]);

            // Move the redirection here
            return Redirect::to($jsonResponse['redirect_url']);
        }
    }

    public function success(Request $request, $code){
        // $token = session('token') ?? '';

        $token ='405gzppls4j9hke';
        $data = $request->input('data');

        if (!$token || !$data) {
            // return 'no token ou data';
           // dd($token);

            // Move the redirection here
            return redirect()->route('payment.index')->withErrors('Token ou données manquants');
        }

        $data['token'] = $token;

        $payment = Payment::firstOrCreate([
            'token' => rand(1,1000),
        ], 
        [
            'amount' => $data['amount'],
            
            'commende_id' => $data['commende_id'],
        ]);
        // dd($payment);

        if (!$payment) {
            //return 'no payment';
            // Move the redirection here
            return redirect()->route('payment.index')->withErrors('Échec de la sauvegarde du paiement');
        }

        session()->forget('token');

        // Move the redirection here
        return view('success');
    }

 
    public function paymentSuccessView(Request $request, $code)
    {
        // You can fetch data from db if you want to return the data to views

        /* $record = Payment::where([
            ['token', '=', $code],
            ['user_id', '=', auth()->user()->id]
        ])->first(); */

        return view('vendor.paytech.success'/* , compact('record') */)->with('success', 'Félicitation, Votre paiement est éffectué avec succès');
    }



    public function cancel()
    {
        # code...
    }
}