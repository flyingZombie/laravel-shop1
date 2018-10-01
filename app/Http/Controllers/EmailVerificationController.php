<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Cache;
use Illuminate\Http\Request;
use App\Notifications\EmailVerificationNotification;
use Mail;
use App\Exceptions\InvalidRequestException;

class EmailVerificationController extends Controller
{
    public function verify(Request $request)
    {
    	$email = $request->input('email');
    	$token = $request->input('token');

    	if (!$email || !$token) {
    		throw new InvalidRequestException('The verification link is incorrect!');
    	}

    	if ($token != Cache::get('email_verification_'.$email)) {
    		throw new InvalidRequestException('The verification link is incorrect or expired!');
    	}

    	if (!$user = User::where('email', $email)->first()) {
    		throw new InvalidRequestException("The user doesn't exist! ");
    	}

    	Cache::forget('email_verification_'.$email);

    	$user->update(['email_verified' => true]);

    	return view('pages.success', ['msg' => 'The email was verified successfully!']);
    }

    public function send(Request $request)
    {
    	$user = $request->user();

    	if ($user->email_verified) {
    		throw new InvalidRequestException('Your email already verified.');
    	}

    	$user->notify(new EmailVerificationNotification());

    	return view('pages.success', ['msg' => 'Email sent successfully!']);
    }
}
