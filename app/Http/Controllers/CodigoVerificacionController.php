<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\EnviarCorreo;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Models\Codigo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class CodigoVerificacionController extends Controller
{
    public function store(){
        $encryption_key = env('CRYPT_KEY');
        $codigoLogin = strval(mt_rand(100000, 999999));
        $codigoVerificación = strval(mt_rand(100000, 999999));

        $has_code = Codigo::where('user_id', Auth::user()->id)
            ->where('status',true)
            ->get();
            if(count($has_code)==0){
                $code_gen = new Codigo();
                $code_gen->user_id = Auth::user()->id;
                $code_gen->codigo_web = Hash::make($codigoLogin);
                $code_gen->codigo_Verificacion_web = Crypt::encryptString($codigoLogin, $encryption_key);
                $code_gen->codigo_movil = Hash::make($codigoVerificación);
                $code_gen->codigo_Verificacion_movil = Crypt::encryptString($codigoVerificación, $encryption_key);
                $code_gen->save();
        
                $signed_url = URL::temporarySignedRoute(
                    'enviar', now()->addMinutes(30), Auth::user()->id
                );
                $mail= new EnviarCorreo($signed_url);
                Mail::to(Auth::user()->email)->send($mail);
            }

            return view('layouts.Envio_Codigo');
    }

    public function show()
    {   
       
        $encryption_key = env('CRYPT_KEY');
        $code = Codigo::where('user_id', Auth::user()->id)->where('status',true)->first();
        return view('Vista_Codigo',['code'=>Crypt::decryptString($code->codigo_Verificacion_web, $encryption_key)]);
        
    }

    public function Validacion_Codigo_Movil(Request $request)
    {
        $encryption_key = env('CRYPT_KEY');
        $application_code = $request->input('input_codigo');
        $user_codes = Codigo::where('status', true)->get();
        
        foreach ($user_codes as $codes) {
            if(Hash::check($application_code, $codes->codigo_web)){
                return response()->json([
                    'login_code'=> Crypt::decryptString($codes->codigo_Verificacion_movil, $encryption_key)
                ],201);
            }else{
                return response()->json([
                    'message'=> "Invalido el Codigo"
                ], 406);
            }
        }

        return view('welcome');
    }

    public function Validar_codigo_login(Request $request)
    {
        $login_code = $request->input('inputLogin');
        $user_codes = codigo::where('user_id', Auth::user()->id)
        ->where('status',true)->get();

        foreach ($user_codes as $codes) {
            if(Hash::check($login_code, $codes->codigo_movil)){

                $trust_code = codigo::find($codes->id);
                $trust_code->status = false;
                $trust_code->save();
                Session::put('code', $codes->codigo_web);
                return redirect('dashboard');
            }else{
                return view('layouts.Envio_Codigo');
            }
        }
    }
}
