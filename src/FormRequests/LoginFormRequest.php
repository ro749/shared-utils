<?php
namespace Ro749\SharedUtils\FormRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;
abstract class LoginFormRequest extends BaseFormRequest
{
    public string $guard;
    public string $column_status = '';

    public bool $plain_password = false;

    public function __construct(
        string $table, 
        array $fields = [], 
        string $redirect = '', 
        string $popup = '', 
        string $submit_text = 'Entrar', 
        string $submit_url = '', 
        string $callback = '', 
        string $guard = 'web', 
        bool $plain_password = false,
        string $column_status = ''
    )
    {
        parent::__construct( $table, $fields, $redirect, $popup, $submit_text, $submit_url, $callback);
        $this->guard = $guard;
        $this->plain_password = $plain_password;
        $this->column_status = $column_status;
    }
    
    function incorrect_credentials($key)
    {
        RateLimiter::hit($key, 60);
        throw ValidationException::withMessages([
            'password' => ['Las credenciales proporcionadas son incorrectas.'],
        ]);
    }

    public function prosses(Request $rawRequest): string
    {
        $credentials = $rawRequest->validate($this->rules($rawRequest));
        $user = array_values($credentials)[0];
        $key = "login-attempts:".$this->guard.$user;
        if (RateLimiter::tooManyAttempts($key, 6)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'password' => ['Demasiados intentos de inicio de sesión. Inténtalo en ' . $seconds . ' segundos.'],
            ]);
        }
        if ($this->plain_password) {
            $credentials['password'] = $user;
        }
        else{
            if (!Auth::guard($this->guard)->attempt($credentials)) {
                $this->incorrect_credentials($key);
            }
        }
        if($this->column_status != ''){
            if(Auth::guard($this->guard)->user()->{$this->column_status} != '0'){
                Auth::guard($this->guard)->logout();
                throw ValidationException::withMessages([
                    'password' => ['Usuario bloqueado.'],
                ]);
            }
        }
        RateLimiter::clear($key);
        $rawRequest->session()->regenerate();
        return $this->redirect;
    }
}