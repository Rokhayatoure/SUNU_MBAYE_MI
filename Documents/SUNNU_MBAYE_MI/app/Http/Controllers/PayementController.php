<?php

namespace App\Http\Controllers;

use App\Models\User;

use App\Models\Payment;
use App\Models\Commende;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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





    public function initiatePayment($commende_id,Request $request){
        $commende = Commende::find($commende_id);
        $user = User::where('id', $commende->user_id)->first();
        
        if(!$user){
            return response()->json(['status' => 404, 'status_message' => 'Vous n\'etes pas l\'auteur de cette commande.']);
        }
        
        $montant = $commende->prix;

        $commende_id = $commende->commende_id;
        $userexist=DB::table('password_reset_tokens')->insert([
            'users_id' =>$user = Auth::guard('api')->user()->id,
            'token'=>2
        ]);
    
        return redirect()->route('payment.index');
    
        



        $paymentService = new PaytechService(config('paytech.PAYTECH_API_KEY'), config('paytech.PAYTECH_SECRET_KEY'));
    
        // Envoyez la requête de paiement
        $jsonResponse = $paymentService->setQuery([
            'commande_id' =>$commende_id  ,
            'item_price' => $montant,
            'command_name' => "Paiement pour un don via PayTech",
        ])
        ->setCustomeField([
            'detail_commande_id' => $commende->id,
            'time_command' => time(),
            'ip_user' => $request->ip(),
            'lang' => $request->server('HTTP_ACCEPT_LANGUAGE')
        ])
        ->setTestMode(true)
        ->setCurrency("xof")
        ->setRefCommand(uniqid())
        ->setNotificationUrl([
            'ipn_url' => 'https://urltowebsite.com/ipn',  
            'success_url' => $success_url,
            'cancel_url' => $cancel_url
        ])->send();
    
        // Traitez la réponse et retournez une réponse appropriée à votre application
        if ($jsonResponse['success'] < 0) {
            return response()->json(['error' => $jsonResponse['errors'][0]], 422);
        } elseif ($jsonResponse['success'] == 1) {
           
            return response()->json(['token' => $jsonResponse['token'], 'redirect_url' => $jsonResponse['redirect_url']]);
        }

    }

    
    public function success(Request $request, $code)
    {
        $validated = $_GET['data'];
        // $validated['token'] = session('token') ?? '';
        $validated['token'] = Str::random(156);

        // Call the save methods to save data to database using the Payment model

        $payment = $this->savePayment($validated);

        session()->forget('token');

        return Redirect::to(route('payment.success.view', ['code' => $code]));
    }

    public function savePayment($data = [])
    {
        
        // Récupérez les informations nécessaires du tableau $data
        $token = $data['token'];
        $montant = $data['prix'];
       $commende_id = $data['Commende_id'];
    
        $id= DB::table('password_reset_tokens')->first();
       
        $payment = Payment::firstOrCreate([
             'token' => $token,
            
        ], [
            'montant' => $montant,
            'user_id' =>   $id->user,
            'Commende_id' =>$commende_id,
        ]);
        
        DB::table('password_reset_tokens')->delete();
        
        if (!$payment) {
            // Redirection vers la page d'accueil si le paiement n'est pas enregistré
            return [
                'success' => false,
                'data' => $data
            ];
    
        }
    
        // Redirection vers la page de succès si le paiement est réussi
        $data['payment_id'] = $payment->id;
    
        return [
            'success' => true,
            'data' => $data
        ];
    }

    // public function savePayment($data = [])
    // {

    //     $payment = Payment::create([
    //         'commande_id' => $data['commande_id'],
    //         'montant' => $data['item_price'],
    //         'token' => $data['token'],
    //     ]);

    //     if (!$payment) {
    //         return $response = [
    //             'success' => false,
    //             'data' => $data
    //         ];
    //     }

    //     return $response = [
    //         'success' => true,
    //         'data' => $data
    //     ];

    //     # save payment database

    //     /* $payment = Payment::firstOrCreate([
    //         'token' => $data['token'],
    //     ], [
    //         'user_id' => auth()->user()->id,
    //         'product_name' => $data['product_name'],
    //         'amount' => $data['price'],
    //         'qty' => $data['qty']
    //     ]);

    //     if (!$payment) {
    //         # redirect to home page if payment not saved
    //         return $response = [
    //             'success' => false,
    //             'data' => $data
    //         ];
    //     } */


    //     # Redirect to Success page if payment success

    //     // $data['payment_id'] = $payment->id;

    //     /*
    //         You can continu to save onother records to database using Eloquant methods
    //         Exemple: Transaction::create($data);
    //     */

    //     return $response = [
    //         'success' => true, //
    //         'data' => $data
    //     ];
    // }

    public function paymentSuccessView(Request $request, $code)
    {
        // You can fetch data from db if you want to return the data to views

        /* $record = Payment::where([
            ['token', '=', $code],
            ['user_id', '=', auth()->user()->id]
        ])->first(); */

        return view('vendor.paytech.success'/* , compact('record') */)->with('success', 'Félicitation, Votre paiement est éffectué avec succès');
    }

    // public function cancel()
    // {
    //     # code...
    // }
 

}
