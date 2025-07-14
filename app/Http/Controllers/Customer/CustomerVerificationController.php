<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Controller;

class CustomerVerificationController extends Controller
{
    /**
     * Show the email verification notice.
     */
    public function show(Request $request)
    {
        \Log::debug('Verification Notice Access', [
            'user' => $request->user('web_customer'),
            'is_authenticated' => auth('web_customer')->check(),
        ]);
        return $request->user('web_customer')->hasVerifiedEmail()
            ? redirect()->route('customer.dashboard')
            : view('auth.customer.verify-email');
    }

    /**
     * Mark the authenticated customer's email address as verified.
     */
    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user('web_customer')->hasVerifiedEmail()) {
            return redirect()->route('customer.dashboard')->with('success', 'Email already verified.');
        }

        if ($request->user('web_customer')->markEmailAsVerified()) {
            event(new Verified($request->user('web_customer')));
        }

        return redirect()->route('customer.dashboard')->with('success', 'Email verified!');
    }

    /**
     * Resend the email verification notification.
     */
    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user('web_customer');
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('customer.dashboard');
        }
        $user->sendEmailVerificationNotification();
        return back()->with('status', 'Verification link sent!');
    }
} 