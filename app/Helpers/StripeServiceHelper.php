<?php

namespace App\Helpers;

class StripeServiceHelper
{
    private $apiKey;
    private $stripeService;
    public $currencyMultiplyingFactor = 100;

    public function __construct()
    {
        $this->apiKey = config('services.stripe.secret');
        $this->stripeService = new \Stripe\Stripe();
        $this->stripeService->setVerifySslCerts(false);
        $this->stripeService->setApiKey($this->apiKey);
    }

    public function createPlan($arr)
    {
        return \Stripe\Plan::create($arr);
        // [
        //     'nickname'=> "new nickname",
        //     'amount' => 4000*$this->currencyMultiplyingFactor,
        //     'currency' => 'inr',
        //     'interval' => 'month',
        //     'product' => ['name' => 'Gold test plan']
        // ]);
    }
    // <option value="day">Daily</option>
    // <option value="week">Weekly</option>
    // <option value="month">Monthly</option>
    // <option value="quarter">Every 3 months</option>
    // <option value="semiannual">Every 6 months</option>
    // <option value="year">Yearly</option>
    // public function editPlan($planId,$arr)
    // {
    //     return \Stripe\Plan::update(
    //         $planId,$arr
    //         //['metadata' => ['order_id' => '6735']]
    //       );
    // }
    public function retrievePlan($planId)
    {
        return \Stripe\Plan::retrieve($planId);
    }
    public function deletePlan($planId)
    {
        $plan = \Stripe\Plan::retrieve($planId);
        return  $plan->delete();
    }
    public function listPlan()
    {
        return \Stripe\Plan::all(); //['limit' => 3]
    }
    public function retrieveProduct($productId)
    {
        return \Stripe\Product::retrieve($productId);
    }
    public function deleteProduct($productId)
    {
        $product = \Stripe\Product::retrieve($productId);
        return  $product->delete();
    }

    public function charge($charge)
    {
        return \Stripe\Charge::create($charge);
    }
}
