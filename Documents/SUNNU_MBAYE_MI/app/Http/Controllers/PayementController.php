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

     /**
     * Afficher la page d'accueil du processus de paiement.
     *
     * @OA\Get(
     *     path="/api/payment",
     *     summary="Page d'accueil du paiement",
     *     tags={"Payements"},
     *     @OA\Response(
     *         response=200,
     *         description="Affiche la page d'accueil du paiement"
     *     )
     * )
     */

    public function index()
    {

        return view('index');
    }
     /**
     * Effectuer un paiement via PayTech.
     *
     * @OA\Post(
     *     path="/api/checkout",
     *     summary="Effectuer un paiement",
     *     tags={"Payements"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"price", "commende_id"},
     *             @OA\Property(property="price", type="number", format="float", example=100.0),
     *             @OA\Property(property="commende_id", type="integer", example=123)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Redirige vers le site PayTech pour finaliser le paiement"
     *     ),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */

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
 /**
     * Gérer la réussite du paiement.
     *
     * @OA\Get(
     *     path="/api/payment-success/{code}",
     *     summary="Page de succès du paiement",
     *     tags={"Payements"},
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         required=true,
     *         description="Code de réussite du paiement",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Affiche la page de succès du paiement"
     *     ),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */
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

 /**
     * Afficher la page de succès du paiement.
     *
     * @OA\Get(
     *     path="/api/payment/{code}/success",
     *     summary="Page de succès du paiement",
     *     tags={"Payements"},
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         required=true,
     *         description="Code de réussite du paiement",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Affiche la page de succès du paiement"
     *     ),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */
    public function paymentSuccessView(Request $request, $code)
    {
        // You can fetch data from db if you want to return the data to views

        /* $record = Payment::where([
            ['token', '=', $code],
            ['user_id', '=', auth()->user()->id]
        ])->first(); */

        return view('vendor.paytech.success'/* , compact('record') */)->with('success', 'Félicitation, Votre paiement est éffectué avec succès');
    }



     /**
     * Annuler le paiement.
     *
     * @OA\Get(
     *     path="/api/payment-cancel",
     *     summary="Annuler le paiement",
     *     tags={"Payements"},
     *     @OA\Response(
     *         response=200,
     *         description="Affiche la page d'annulation du paiement"
     *     )
     * )
     */
    public function cancel()
    {
        # code...
    }
}