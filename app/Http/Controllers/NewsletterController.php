<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Validator;
use App\Mail\NewsletterSubscriptionMail;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:newsletter_subscribers,email'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $subscriber = NewsletterSubscriber::create([
            'email' => $request->email
        ]);

        // Envoi de l'email de confirmation
        Mail::to($request->email)->send(new NewsletterSubscriptionMail($request->email));

        return redirect()->back()
            ->with('success', 'Félicitations ! Votre inscription à notre newsletter a été confirmée. Vous recevrez bientôt un email de bienvenue à l\'adresse ' . $request->email . '. Merci de votre confiance !');
    }
}
