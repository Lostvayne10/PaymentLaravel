<?php

namespace App\Services;
use App\Traits\ConsumesExternalServices;
use Illuminate\Http\Request;
use App\Services\CurrencyConversionService;
use Illuminate\Support\Str;

class PayUService 
{
    use ConsumesExternalServices;

    protected $baseUri;
    protected $accountid;
    protected $merchantid;
    protected $key;
    protected $secret;
    protected $base_currency;
    protected $converter;

    public function __construct( CurrencyConversionService $converter)
    {
        
        $this->baseUri = config('services.payu.base_uri');
        $this->key = config('services.payu.key');
        $this->secret = config('services.payu.secret');
        $this->baseCurrency = strtoupper(config('services.payu.base_currency'));
        $this->accountid = config('services.payu.account_id');
        $this->merchantid =config('services.payu.merchant_id');
        $this->converter = $converter;
    }

    public function resolveAuthorization(&$queryParams, &$formParams, &$headers)
    {
        $formParams['merchant']['apiKey'] = $this->key;
        $formParams['merchant']['apiLogin'] = $this->secret;

    }

    public function decodeResponse($response)
    {   
        return json_decode($response);
    }

    public function resolverAccessToken(){
       
    }

    public function handlePayment(Request $request)
    {
       $request->validate([
            'payu_network' => 'required',
            'payu_card' => 'required',
            'payu_cvc' => 'required',
            'payu_month' => 'required',
            'payu_year' => 'required',
            'payu_email' => 'required',
            'payu_name' => 'required',
       ]);
       

        $payment = $this->createPayment(
            $request->value,
            $request->currency,
            $request->payu_name,
            $request->payu_email,
            $request->payu_card,
            $request->payu_cvc,
            $request->payu_year,
            $request->payu_month,
            $request->payu_network,
        );
        if($payment->transactionResponse->state === 'APPROVED'){

            $name = $request->payu_name;
                
            $currency = strtoupper($request->currency);

            $amount = $request->value;

            return redirect()
                        ->route('home')
                        ->withSuccess(['payment' => "Thanks, {$name}. We received your {$amount}{$currency} payment."]);
        }

        return redirect()
        ->route('home')
        ->withErrors('We were unable to proccess your payment. Check your details and try again, please.');
    }

    public function handleApproval()
    {
       
    }

    public function createPayment($value, $currency, $name, $email, $card, $cvc, $year, $month, $network, $installments = 1, $paymentCountry = 'MX')
    {
        return $this->makeRequest(
            'POST',
            '/payments-api/4.0/service.cgi',
            [],
            [
                'language' => $language = config('app.locale'),
                'command' => 'SUBMIT_TRANSACTION',
                'test' => false,
                'transaction' => [
                    'type' => 'AUTHORIZATION_AND_CAPTURE',
                    'paymentMethod' => strtoupper($network),
                    'paymentCountry' => strtoupper($paymentCountry),
                    'deviceSessionId' => session()->getId(),
                    'ipAddress' => request()->ip(),
                    'userAgent' => request()->header('User-Agent'),
                    'creditCard' => [
                        'number' => $card,
                        'securityCode' => $cvc,
                        'expirationDate' => "{$year}/{$month}",
                        'name' => "APPROVED",
                    ],
                    'extraParameters' => [
                        'INSTALLMENTS_NUMBER' => $installments,
                    ],
                    'payer' => [
                        'fullName' => $name,
                        'emailAddress' => $email,
                    ],
                    'order' => [
                        'accountId' => $this->accountid,
                        'referenceCode' => $reference = Str::random(12),
                        'description' => 'Testing PayU',
                        'language' => $language,
                        'signature' => $this->generateSignature($reference, $value = round($value * $this->resolveFactor($currency))),
                        'additionalValues' => [
                            'TX_VALUE' => [
                                'value' => $value,
                                'currency' => $this->baseCurrency,
                            ],
                        ],
                        'buyer' => [
                            'fullName' => $name,
                            'emailAddress' => $email,
                            'shippingAddress' => [
                                'street1' => '',
                                'city' => '',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'Accept' => 'application/json',
            ],
            $isJsonRequest = true,
        );
    }
   
    public function resolveFactor($currency)
    {
        return $this->converter->convertCurrency($currency, $this->baseCurrency);
    }

    public function generateSignature($referencecode, $value)
    {
        return md5("{$this->key}~{$this->merchantid}~{$referencecode}~{$value}~{$this->baseCurrency}");
    }

}