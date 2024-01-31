<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Commende;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Services\PaytechService;
use App\Http\Requests\PayementRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;


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





    public function initiatePayment(Commende $commende_id){
        dd($commende_id);
        // Récupérez les informations nécessaires de la requête $request
        //$amount = $request->input('price');
       // $commandeId = $request->input('commande_id');
        // $commande= DetailCommande::where('commande_id', $detailCommande->commande_id)->first();
        // $client=Client::where('id', $commande->client_id)->first();
        // $user = Client::where('user_id', Auth::user()->id)->first();
        // $montant = $detailCommande->montant;
        // $commandeId = $detailCommande->commande_id;
        // $userexist=DB::table('password_reset_tokens')->insert([
        //     'clientConnecter' =>$user->id,
        //     'token'=>1
        // ]);
     
        // Construisez l'URL de succès
       // $success_url = secure_url(route('payment.success', ['code' => $commandeId, 'data' => $request->all()]));
        // $success_url = secure_url(route('payment.success', ['code' => $commende_id, 'data' => $detailCommande->all()]));
    
        // Construisez l'URL d'annulation
        // $cancel_url = secure_url(route('payment.index'));
    
        // Instanciez le service de paiement
        // $paymentService = new PaytechService(config('paytech.PAYTECH_API_KEY'), config('paytech.PAYTECH_SECRET_KEY'));
    
        // Envoyez la requête de paiement
        // $jsonResponse = $paymentService->setQuery([
        //     'item_name' => "Paiement de la commande",
        //     'item_price' => $montant,
        //     'command_name' => "Paiement pour un don via PayTech",
        // ])
    //     ->setCustomeField([
    //         'item_id' => $collecteId,
    //         'time_command' => time(),
    //         'ip_user' => $request->ip(),
    //         'lang' => $request->server('HTTP_ACCEPT_LANGUAGE')
    //     ])
    //     ->setTestMode(true)
    //     ->setCurrency("xof")
    //     ->setRefCommand(uniqid())
    //     ->setNotificationUrl([
    //         'ipn_url' => 'https://urltowebsite.com/ipn',  
    //         'success_url' => $success_url,
    //         'cancel_url' => $cancel_url
    //     ])->send();
    
    //     // Traitez la réponse et retournez une réponse appropriée à votre application
    //     if ($jsonResponse['success'] < 0) {
    //         return response()->json(['error' => $jsonResponse['errors'][0]], 422);
    //     } elseif ($jsonResponse['success'] == 1) {
           
    //         return response()->json(['token' => $jsonResponse['token'], 'redirect_url' => $jsonResponse['redirect_url']]);
    //     }
       
    }

    
 

 public function savePayment($data = [])
{
  
  // Récupérez les informations nécessaires du tableau $data
  $token = $data['token'];
  $amount = $data['price'];
  $collecteId = $data['collecte_id'];

  $id= DB::table('password_reset_tokens')->first();
 
  $payment = Payment::firstOrCreate([
       'token' => $token,
      
  ], [
      'amount' => $amount,
      'user_id' =>   $id->donateurConnecter,
      'collecte_de_fonds_id' => $collecteId,
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







  public function paymentSuccessView(Request $request, $code)
  {
      // You can fetch data from db if you want to return the data to views

      /* $record = Payment::where([
          ['token', '=', $code],
          ['user_id', '=', auth()->user()->id]
      ])->first(); */

      return 'success Félicitation, Votre paiement est éffectué avec succès';
  }

  public function cancel()
  {
      # code...
  }
//   public function success(Request $request, $code)
//   {
//       $validated = $_GET['data'];
//       // $validated['token'] = session('token') ?? '';
//       $validated['token'] = Str::random(156);

//       // Call the save methods to save data to database using the Payment model

//       $payment = $this->savePayment($validated);

//       session()->forget('token');

//       return Redirect::to(route('payment.success.view', ['code' => $code]));
//   }
}
