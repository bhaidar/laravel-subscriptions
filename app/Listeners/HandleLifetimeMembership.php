<?php

namespace App\Listeners;

use Laravel\Cashier\Cashier;
use Laravel\Cashier\Events\WebhookReceived;

class HandleLifetimeMembership
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(WebhookReceived $event): void
    {
        if (
            $event->payload['type'] !== 'checkout.session.completed' ||
            $event->payload['data']['object']['mode'] === 'subscription'
        ) {
            return;
        }

        $user = Cashier::findBillable($event->payload['data']['object']['customer']);

        if ($user->subscribed()) {
            $user->subscription('default')->cancelNow();
        }

        $user->update([
            'lifetime_membership' => true,
        ]);
        
    }
}
