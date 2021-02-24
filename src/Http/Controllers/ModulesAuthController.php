<?php


namespace Modullo\ModulesAuth\Http\Controllers;
use App\Http\Controllers\Hub\HubController;
use Hostville\Modullo\Sdk;
use Hostville\Modullo\UserLaravel\Auth\ModulloUserProvider;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class ModulesAuthController extends \App\Http\Controllers\Controller
{
  private $data;
  public function __construct() {
    $this->data = [
      'page' => ['title' => config('modules-auth.title')],
      'header' => ['title' => config('modules-auth.title')],
    ];
  }

  public function index(){
    $user = Auth::guard('web')->user();
    dd($user);
    return view('modules-auth::home',compact('user'));
  }


  public function showLoginForm(){
    return view('modules-auth::login',$this->data);
  }

  public function showRegisterForm(){
    return view('modules-auth::register',$this->data);
  }


  public function login(Request $request, Sdk $sdk){
    $provider = new ModulloUserProvider($sdk);
    //dd($provider);
    # get the provider
    $modulloUser = $provider->retrieveByCredentials(['email' => $request->email, 'password' => $request->password]);
    Auth::guard('web')->login($modulloUser);
    return redirect()->route('home');
  }



  public function register(){

  }

}