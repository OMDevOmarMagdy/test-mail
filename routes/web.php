<?php

use App\Mail\ForgetPassMail;
use App\Mail\TestMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/send-test-email', function () {

    // Mail::to('test@example.com')
    //     ->send(new TestMail());

    Mail::to('test@example.com')
        ->send(new TestMail());

    return 'Email added to queue!';
});

Route::get('/forgot-password', function () {

    $email = 'test@example.com';

    $user = User::where('email', $email)->first();

    if (! $user) {
        return 'User not found';
    }

    $token = Password::createToken($user);

    $resetUrl = url("/reset-password?token={$token}&email={$user->email}");

    Mail::to($user->email)
        ->queue(new ForgetPassMail($resetUrl));

    return 'Password reset email added to queue!';
});

Route::get('/reset-password', function (\Illuminate\Http\Request $request) {

    return view('reset-password', [
        'token' => $request->token,
        'email' => $request->email,
    ]);

});

Route::post('/reset-password', function (\Illuminate\Http\Request $request) {

    $request->validate([
        'email'    => ['required', 'email'],
        'token'    => ['required'],
        'password' => ['required', 'confirmed', 'min:8'],
    ]);

    $status = \Illuminate\Support\Facades\Password::reset(
        $request->only(
            'email',
            'password',
            'password_confirmation',
            'token'
        ),
        function ($user, $password) {

            $user->forceFill([
                'password' => \Illuminate\Support\Facades\Hash::make($password),
            ])->save();
        }
    );

    if ($status === \Illuminate\Support\Facades\Password::PASSWORD_RESET) {
        return 'Password reset successfully!';
    }

    return 'Invalid or expired reset token.';
});