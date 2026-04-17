<?php
namespace Ro749\SharedUtils\Forms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
abstract class LoginForm extends BaseForm
{
    public string $guard;
    public string $column_status = '';

    public bool $plain_password = false;
    public bool $blocked = false;

    public function __construct(
        string $model_class = '', 
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
        parent::__construct( 
            model_class: $model_class, 
            fields: $fields, 
            redirect: $redirect, 
            popup: $popup, 
            submit_text: $submit_text, 
            submit_url: $submit_url, 
            callback: $callback);
        $this->guard = $guard;
        $this->plain_password = $plain_password;
        $this->column_status = $column_status;
        $this->blocked = config('login.blocked')??false;
    }
    
    function incorrect_credentials($key)
    {
        RateLimiter::hit($key, 60);
        throw ValidationException::withMessages([
            'password' => ['Las credenciales proporcionadas son incorrectas.'],
        ]);
    }

    public function prosses(Request $request): string
    {
        if($this->blocked){
            throw ValidationException::withMessages([
                'password' => ['Acceso bloqueado, contacte al administrador.'],
            ]);
        }
        $credentials = $request->validate($this->rules($request));
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
            if(!empty(Auth::guard($this->guard)->user()->{$this->column_status})){
                Auth::guard($this->guard)->logout();
                throw ValidationException::withMessages([
                    'password' => ['Usuario bloqueado.'],
                ]);
            }
        }
        if(Auth::guard($this->guard)->user()->reset == '1'){
            return route('reset-password-view');
        }
        RateLimiter::clear($key);
        $request->session()->regenerate();
        Auth::guard($this->guard)->user()->update(['last_session_register' => now()]);
        return $this->redirect;
    }

    public function get_default_args(){
        return ['test' => 'test'];
    }
}