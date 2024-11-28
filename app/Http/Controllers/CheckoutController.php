<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function recurring(Request $request)
    {
        abort_unless(
            $plan = collect(config('subscriptions.plans'))->where('type', 'recurring')->get($request->plan),
            404
        );

        return $request->user()->newSubscription('default', $plan['price_id'])
            //->trialDays(4)
            ->allowPromotionCodes()
            ->checkout([
                'success_url' => route('dashboard'),
                'cancel_url' => route('plans')
            ], [
                'email' => $request->user()->email
            ]);
    }

    public function lifetime(Request $request)
    {
        $plan = collect(config('subscriptions.plans'))->where('type', 'lifetime')->get('lifetime');

        return $request->user()
            ->allowPromotionCodes()
            ->checkout($plan['price_id'], [
                'success_url' => route('dashboard'),
                'cancel_url' => route('plans'),
                'invoice_creation' => [
                    'enabled' => true,
                ],
            ]);
    }
}
