<?php


namespace Modullo\ModulesAuth\Http\Controllers;
use App\Http\Controllers\Hub\HubController;
use App\Models\User;
use Doctrine\DBAL\Exception;
use Hostville\Modullo\Sdk;
use Hostville\Modullo\UserLaravel\Auth\ModulloUser;
use Hostville\Modullo\UserLaravel\Auth\ModulloUserProvider;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ModulesAuthController extends \App\Http\Controllers\Controller
{
  private $data;
  public function __construct() {
    $this->data = [
      'page' => ['title' => config('modules-auth.title')],
      'header' => ['title' => config('modules-auth.title')],
      'company_name' =>  config('modules-auth.view.company_name'),
      'allow_registration' => config('modules-auth.view.allow_registration')
    ];
  }

  public function index(Request $request){
    $user = Auth::user();

    return view('modules-auth::home',compact('user'));
  }


  public function showLoginForm(){
    $this->data['company_logo'] = config('modules-auth.view.company_logo') ?? asset('vendor/modules-auth/logo/modullo.png');

    return view('modules-auth::login',$this->data);
  }

  public function showRegisterForm(){

    $this->data['company_logo'] = config('modules-auth.view.company_logo') ?? asset('vendor/modules-auth/logo/modullo.png');
    return view('modules-auth::register',$this->data);
  }


  public function login(Request $request, Sdk $sdk){
       $request->validate([
          'email' => 'required|email',
          'password' => 'required',
        ]);


     try {
        $user = null;
        DB::transaction(function () use ($sdk,$request,&$user){
        $provider = new ModulloUserProvider($sdk);
        $modulloUser = $provider->retrieveByCredentials(['email' => $request->email, 'password' => $request->password]);
        if ($modulloUser){
            $user = User::updateOrCreate(['uuid' => $modulloUser->id],
               [
                'uuid' => $modulloUser->id,
                'email' => $modulloUser->email,
                'first_name' => $modulloUser->first_name,
                'last_name' => $modulloUser->last_name,
                'password' => $modulloUser->password,
                'phone_number' => $modulloUser->phone_number,
              ]
          );
        }
     });
    if (!$user){
        return redirect()->route('login')->withErrors(['message' => 'account credentials could not be found']);
    }
    return $this->loginRedirect($user);
    }
    catch (\Throwable $e){
      Log::error($e->getMessage());
      throw new \Exception($e->getMessage());
    }
  }


  protected function loginRedirect(User $user){
    Auth::guard('web')->login($user);
    return redirect()->route('modullo.home');
  }



  public function register(Request  $request, Sdk $sdk){

    $request->validate([
      'email' => 'required|email|unique:users',
      'password' => 'required',
      'last_name' => 'required',
      'first_name' => 'required',
      'phone_number' => 'required',
    ]);
     try {
       $response = create_account($sdk, [
         'email' => $request->input('email'),
         'password' => $request->input('password'),
         'first_name' => $request->input('first_name'),
         'last_name' => $request->input('last_name'),
         'phone_number' => $request->input('phone_number'),
       ]);
       if (!$response->isSuccessful()) {
         return redirect()->route('register')->withErrors(
           [
             'message' => 'Error while creating the '. $this->data['company_name']. ' account  ' . $response->getErrors
               ()[0]['title'],
             'errors' => $response->getErrors
             ()[0]['source']
           ]);
       }
       $user = null;
       $user = $this->create($user,$sdk,$response);
       if (!$user){
        return redirect()->route('register')->withErrors(['message' => 'account credentials could not be created']);
       }
      return $this->loginRedirect($user);
    }
    catch (\Throwable $e){
      Log::error($e->getMessage());
      throw new \Exception($e->getMessage());
    }

  }


  protected function create($user,$sdk,$response){
    DB::transaction(function () use ($sdk,&$user,$response){
      $modulloUser = new ModulloUser($response->getData()['user'], $sdk);
      $user = User::create([
          'uuid' => $modulloUser->id,
          'email' => $modulloUser->email,
          'first_name' => $modulloUser->first_name,
          'last_name' => $modulloUser->last_name,
          'password' => $modulloUser->password,
          'phone_number' => $modulloUser->phone_number,
        ]
      );

    });
    return $user;
  }




  public function logout(Request  $request){
      Auth::logout();
      return redirect()->route('login');
  }

}