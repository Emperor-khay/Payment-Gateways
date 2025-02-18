<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function pay() {
        return view("pay.index");
    }

    public function process_payment(Request $request) {
        $formData = [
            'email' => $request->email,
            'name' => $request->name,
            'amount' => $request->amount * 100, //from kobo to naira
            'callback_url' => config('services.paystack.callback_url'),
        ];
        $payment = $this->initiate_payment($formData);
        $paymentResponse = json_decode($payment, true);

        if($paymentResponse) {
            // dd($paymentResponse);
            return redirect($paymentResponse['data']['authorization_url']);
        }else{
            return redirect()->back()->with('error', 'Payment could not be initiated');
        }
    }

    public function initiate_payment($formData) {
        $secretKey = config('services.paystack.secret_key');

        $url = "https://api.paystack.co/transaction/initialize";

        $fields_string = http_build_query($formData);

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer " . $secretKey,
            "Cache-Control: no-cache",
        ));

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

        //execute post
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function verify_payment($reference) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer " . config('services.paystack.secret_key'),
            "Cache-Control: no-cache",
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          return "cURL Error #:" . $err;
        } else {
          return $response;
        }
    }

    public function payment_callback(Request $request) {
        $reference = $request->reference;
        $payment = $this->verify_payment($reference);
        $paymentResponse = json_decode($payment, true);

        if($paymentResponse) {
            if($paymentResponse['data']['status'] == 'success') {
                return redirect()->route('payment')->with('success', 'Payment was successful');
            }else{
                return redirect()->route('payment')->with('error', 'Payment was unsuccessful');
            }
        }else{
            return redirect()->route('payment')->with('error', 'Payment could not be verified');
        }
    }
}
