<?php

namespace App\Services;
use App\Traits\ConsumesExternalServices;
use Illuminate\Http\Request;

class CurrencyConversionService
{
    use ConsumesExternalServices;

    protected $baseUri;
    protected $apikey;
 

    public function __construct()
    {
        $this->baseUri = config('services.currency_conversion.base_uri');
        $this->apiKey = config('services.currency_conversion.api_Key');
       
    }

    public function resolveAuthorization(&$queryParams, &$formParams, &$headers)
    {
        $queryParams['apiKey'] = $this->resolverAccessToken();
    }

    public function decodeResponse($response)
    {   
        return json_decode($response);
    }

    public function resolverAccessToken(){
       return $this->apiKey;
        
    }

    public function convertCurrency($from, $to)
    {
        $response = $this->makeRequest(
            'GET',
            '/api/v7/convert',
            [
                'q'=> "{$from}_{$to}",
                'compact'=> 'ultra',
            ],
        );

        return $response->{strtoupper("{$from}_{$to}")};
    }
   

}