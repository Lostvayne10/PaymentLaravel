<?php

namespace App\Services;
use App\Traits\ConsumesExternalServices;
use Illuminate\Http\Request;

class MercadoPagoService
{
    use ConsumesExternalServices;

    protected $baseUri;
    protected $key;
    protected $secret;
    protected $base_currency;

    public function __construct()
    {
        $this->baseUri = config('services.mercadopago.base_uri');
        $this->key = config('services.mercadopago.key');
        $this->secret = config('services.mercadopago.secret');
        $this->baseCurrency = config('services.mercadopago.base_currency');
    }

    public function resolveAuthorization(&$queryParams, &$formParams, &$headers)
    {
        
    }

    public function decodeResponse($response)
    {   
        return json_decode($response);
    }

    public function resolverAccessToken(){
       
    }

    public function handlePayment(Request $request)
    {
       
    }

    public function handleApproval()
    {
       
    }
   
    public function resolveFactor($currency)
    {
        
    }

}