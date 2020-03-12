<?php

namespace App\Resolvers;
use App\PaymentPlatform;
use Exception;

class PaymentPlatformResolver
{
    protected $paymentPlatform;

    public function __construct()
    {
        $this->paymentPlatform = PaymentPlatform::all();

    }

    public function resolverService($paymentPlatformId)
    {
        $name = strtolower($this->paymentPlatform->firstWhere('id', $paymentPlatformId)->name);
       
        $service = config("services.{$name}.class");

        if($service){
            return resolve($service);
        }

        throw new Exception('The selected payment platform is not configuration');
    }

}