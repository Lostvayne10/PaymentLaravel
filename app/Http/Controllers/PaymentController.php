<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PaymentPlatform;
use App\Services\PayPalService;
use App\Resolvers\PaymentPlatformResolver;


class PaymentController extends Controller
{
    protected $paymentPlatformResolver;
    public function __construct(PaymentPlatformResolver $paymentPlatformResolver)
    {
        $this->middleware('auth');
        $this->paymentPlatformResolver = $paymentPlatformResolver;
    }

    public function pay(Request $request)
    {
        $rules = [
            'value' => ['required', 'numeric', 'min:5'],
            'currency' => ['required', 'exists:currencies,iso'],
            'payment_platform' => ['required', 'exists:payment_platforms,id'],
        ];

        $request->validate($rules);

        

        $paymentPlatform = $this->paymentPlatformResolver
                    ->resolverService($request->payment_platform);       
                    
        session()->put('paymentPlatformId', $request->payment_platform);

        return $paymentPlatform->handlePayment($request);
    }

    public function approval(Request $request)
    {
        if(session()->has('paymentPlatformId'))
        {
            $paymentPlatform = $this->paymentPlatformResolver
            ->resolverService(session()->get('paymentPlatformId'));   
    
    
            return $paymentPlatform->handleApproval();
        }

        return redirect()
        ->route('home')
        ->withErrors('We cannot retrieve you payment platform. Try again, please'); 
       
    }

    public function cancelled()
    {
        return redirect()
        ->route('home')
        ->withErrors('You cancelled the payment');
    }
}
