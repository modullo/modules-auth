<?php

namespace Modullo\ModulesAuth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Hostville\Modullo\Sdk;
use Hostville\Modullo\UserLaravel\Auth\ModulloUser;
use Hostville\Modullo\UserLaravel\Auth\ModulloUserProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Throwable;
use UnexpectedValueException;
use function PHPUnit\Framework\isEmpty;

class ModulesAuthController extends Controller
{
    protected array $data;
    protected string $tenantType,$tenantId;

    public function __construct()
    {
        $this->data = [
            'page' => ['title' => config('modules-auth.title')],
            'header' => ['title' => config('modules-auth.title')],
            'company_name' => config('modules-auth.view.company_name'),
            'allow_registration' => config('modules-auth.view.allow_registration'),
            'custom_form_fields' => config('modules-auth.view.custom_form_fields'),
        ];

        $this->tenantType = env("MODULLO_TENANT_TYPE", 'multi');
        $this->tenantId = env("MODULLO_TENANT_ID", null);

    }

    public function index(Request $request)
    {
        $user = Auth::user();

        return view('modules-auth::home', compact('user'));
    }

    public function showLoginForm()
    {
        $this->data['company_logo'] = config('modules-auth.view.company_logo') ?? asset('vendor/modules-auth/logo/smarttech-logo1.png');
        return view('modules-auth::login', $this->data);
    }

    public function showRegisterForm()
    {

        $this->data['company_logo'] = config('modules-auth.view.company_logo') ?? asset('vendor/modules-auth/logo/smarttech-logo1.png');
        $this->data['form_fields'] = config('modules-auth-form-fields.fields');
        return view('modules-auth::register', $this->data);
    }

    public function login(Request $request, Sdk $sdk)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        try {
            $user = null;
            $user_type = 'user';
            $modulloType = env("MODULLO_TYPE", null);
            $user = DB::transaction(function () use ($sdk, $request, &$user, &$user_type, $modulloType) {
                $provider = new ModulloUserProvider($sdk);
                $modulloUser = $provider->retrieveByCredentials(['email' => $request->email, 'password' => $request->password],$modulloType);
//                dd([$sdk,$provider,$modulloUser,$modulloUser->roles,$request]);
                if ($modulloUser) {
                    $roles = $modulloUser->roles;
                    $role = $roles[0]; // use the first role

                    if($this->tenantType === 'single'){
                        $checkAccess = $this->checkAccess($role['name'],$modulloUser);
                        if (!$checkAccess) {
                            return null;
                        }
                    }

                    $update = $this->updateUser($role,$modulloUser,$user_type);
                    $user = $update['user'];
                    $user_type = $update['type'];

                    $user->update([
                        'role' => $role["name"]
                    ]);

                    Session::put("modulloUserRole", $role["name"]);

                    return $user;

                }

            });

            if (!$user) {
                return redirect()->route('login')->withErrors(['message' => 'account credentials could not be found']);
            }

            return $this->loginRedirect($user, $user_type);

        } catch (Throwable $e) {
            Log::error($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    private function checkAccess($role,$modulloUser){
        dd($role,$this->tenantId,$modulloUser->tenant['id']);
        if($role === 'lms_tenant' && $this->tenantId === $modulloUser->tenant['id']){
            return true;
        }
        if($role === 'lms_learner' && $this->tenantId === $modulloUser->tenant_id){
            return true;
        }
        return false;
    }

    public function loginForTokenUsers(Request $request, Sdk $sdk)
    {
        if(!$request->filled('email')){
            $request->request->add(['email' => $request->user()->email]);
        }
        $request->validate([
            'email' => 'required|email',
        ]);
        try {
            $user = null;
            $user_type = 'user';
            $token = null;
            $modulloType = env("MODULLO_TYPE", null);
//            dd($clientId);
            $user = DB::transaction(function () use ($sdk, $request, &$user, &$user_type, &$token, $modulloType) {
                $clientId = config('modullo-api.personal_client.id');
                $clientSecret = config('modullo-api.personal_client.secret');
                $provider = new ModulloUserProvider($sdk);
                $modulloUser = $provider->retrieveByEmailOnly(['email' => $request->email,'client_id'=>$clientId,'client_secret'=>$clientSecret,'grant_type'=>'client_credentials']);
//                dd([$sdk,$provider,$modulloUser,$modulloUser->roles,$request]);
                if ($modulloUser) {
                    $roles = $modulloUser->roles;
                    $role = $roles[0]; // use the first role
                    $update = $this->updateUser($role,$modulloUser,$user_type);
                    $user = $update['user'];
                    $user_type = $update['type'];
                    $token = Cache::get('modullo.auth_token.'.$user['uuid'], null);

                    $user->update([
                        'role' => $role["name"]
                    ]);

//                    Session::put("modulloUserRole", $role["name"]);

                    return $user;

                }

            });

            if (!$user) {
                return response()->json(['error' => 'account credentials could not be found'],400);
            }

            return response()->json(['message' => 'login successfull','user' => $user,'user_type' => $user_type,'token' => $token],200);

        } catch (Throwable $e) {
            Log::error($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    protected function loginRedirect(User $user, $type)
    {

        switch ($type) {
            case 'overlord':
                Auth::guard('web')->login($user);
                return redirect()->route('dashboard');
                break;
            case 'admin':
                Auth::guard('web')->login($user);
                return redirect()->route('tenant-dashboard');
                break;
            case 'student':
                Auth::guard('web')->login($user);
                return redirect()->route('learner-dashboard');
                break;
            case 'user':
                Auth::guard('web')->login($user);
                return redirect()->route('dashboard');
                break;
            case 'eos-overlord':
                Auth::guard('web')->login($user);
                return redirect()->route('admin-dashboard');
                break;
            case 'eos-developer':
                Auth::guard('web')->login($user);
                return redirect()->route('developer-dashboard');
                break;
            default:
                Auth::guard('web')->login($user);
                return redirect()->route('dashboard');
                break;
        }

    }

    private function updateUser($role,$modulloUser,$user_type){
        switch ($role["name"]) {
            case 'lms_tenant':
                $user = User::updateOrCreate(['email' => $modulloUser->email],
                    [
                        'uuid' => $modulloUser->id,
                        'email' => $modulloUser->email,
//                                    'first_name' => $modulloUser->tenant['company'],
//                                    'last_name' => $modulloUser->tenant['company'],
                        'password' => $modulloUser->password,
                        'organization_details' => $modulloUser->tenant,
                    ]);
                $user_type = "admin";
                break;
            case 'lms_learner':
                $user = User::updateOrCreate(['email' => $modulloUser->email],
                    [
                        'uuid' => $modulloUser->id,
                        'email' => $modulloUser->email,
                        'first_name' => $modulloUser->learner['first_name'],
                        'last_name' => $modulloUser->learner['last_name'],
                        'password' => $modulloUser->password,
                        'phone_number' => $modulloUser->learner['phone_number'],
                        'learner_details' => $modulloUser->learner,
                    ]);
                $user_type = "student";
                break;
            case 'eos-overlord':
                $user = User::updateOrCreate(['email' => $modulloUser->email],
                    [
                        'uuid' => $modulloUser->id,
                        'email' => $modulloUser->email,
                        'first_name' => $modulloUser->first_name,
                        'last_name' => $modulloUser->last_name,
                        'password' => $modulloUser->password,
                    ]);
                $user_type = 'eos-overlord';
                break;
            case 'eos-developer':
                $user = User::updateOrCreate(['email' => $modulloUser->email],
                    [
                        'uuid' => $modulloUser->id,
                        'email' => $modulloUser->email,
                        'first_name' => $modulloUser->first_name,
                        'last_name' => $modulloUser->last_name,
                        'password' => $modulloUser->password,
                    ]);
                $user_type = 'eos-developer';
                break;
            default:
                $user = User::updateOrCreate(['email' => $modulloUser->email],
                    [
                        'uuid' => $modulloUser->id,
                        'email' => $modulloUser->email,
                        'first_name' => $modulloUser->first_name,
                        'last_name' => $modulloUser->last_name,
                        'password' => $modulloUser->password,
                    ]);
                break;
        }
        return [
            'user' => $user,
            'type' => $user_type,
            ];
    }
    /**
     * @throws Exception
     */
    public function register(Request $request, Sdk $sdk)
    {
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
                        'message' => 'Error while creating the ' . $this->data['company_name'] . ' account  ' . $response->getErrors
                            ()[0]['title'],
                        'errors' => $response->getErrors
                        ()[0]['source']
                    ]);
            }
            $user = null;
            $user = $this->create($user, $sdk, $response);
            if (!isEmpty($this->loadUpdatableFields())) {
                $this->addCustomFields($user, $request->all());

            }
            if (!$user) {
                return redirect()->route('register')->withErrors(['message' => 'account credentials could not be created']);
            }
            $overlord_email = getenv('OVERLORD_EMAIL') ?? 'overlord@modullo.io';
            if ($user->email === $overlord_email) {
                $type = 'overlord';
            } else {
                $type = 'user';
            }
            return $this->loginRedirect($user, $type);
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            throw new Exception($e->getMessage());
        }

    }

    protected function create($user, $sdk, $response)
    {
        DB::transaction(function () use ($sdk, &$user, $response) {
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

    protected function loadUpdatableFields(): array
    {
        $customFields = [];
        if ($this->data['custom_form_fields']) {
            $this->data['form_fields'] = config('modules-auth-form-fields.fields');
            foreach ($this->data['form_fields'] as $field) {
                $customFields[$field['field_name']] = $field['field_name'];
            }

        }
        return $customFields;
    }

    protected function addCustomFields(User $user, array $data): bool
    {

        $this->updateModelAttributes($user, $data, $this->loadUpdatableFields());
        return $user->save();
    }

    protected function updateModelAttributes(Model $model, array $data, array $updateFields)
    {
        if (empty($updateFields)) {
            throw new UnexpectedValueException('The update fields were not configured for the endpoint.');
        }
        foreach ($updateFields as $requestKey => $modelKey) {
            if (!array_key_exists($requestKey, $data)) {
                # doesn't have it, so we skip the key
                continue;
            }
            $model->{$modelKey} = $data[$requestKey];
        }
    }


    public function logout(Request $request)
    {
        Auth::logout();
        return redirect()->route('login');
    }

}
