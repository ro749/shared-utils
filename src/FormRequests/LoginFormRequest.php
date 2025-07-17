<?php
namespace Ro749\SharedUtils\FormRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;
abstract class LoginFormRequest extends BaseFormRequest
{
    public string $guard;

    public function __construct(string $id, string $table, array $formFields = [], string $redirect = '', string $popup = '', string $submit_text = 'Entrar', string $submit_url = '', string $callback = '', string $guard = 'web')
    {
        parent::__construct($id, $table, $formFields, $redirect, $popup, $submit_text, $submit_url, $callback);
        $this->guard = $guard;
    }
    

    public function prosses(Request $rawRequest): string
    {
        $credentials = $rawRequest->validate($this->rules());
        $user = array_values($credentials)[0];
        $key = "login-attempts:".$this->guard.$user;
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'password' => ['Demasiados intentos de inicio de sesión. Inténtalo en ' . $seconds . ' segundos.'],
            ]);
        }
        $credentials = $rawRequest->validate($this->rules());
        if (!Auth::guard($this->guard)->attempt($credentials)) {
            RateLimiter::hit($key, 60);
            throw ValidationException::withMessages([
                'password' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }
        RateLimiter::clear($key);
        //Auth::login($user);
        $rawRequest->session()->regenerate();
        return $this->redirect;
    }
}