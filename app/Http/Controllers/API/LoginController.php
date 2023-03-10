<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\WaliasuhKeluargaAsuh;
use App\PembinaKeluargaAsuh;
use App\TarunaKeluargaAsuh;
use App\OrangTua;
use App\Traits\Firebase;
use DB;
   
class LoginController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['name'] =  $user->name;
   
        return $this->sendResponse($success, 'User register successfully.');
    }
   
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $username = $request->username;
        $password = $request->password;
        $input    = $request->all();

        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'stb';
        if(auth()->attempt(array($fieldType => $input['username'], 'password' => $input['password'], 'status' => 1))){
                $user = Auth::user(); 
                $roles = $user->getRoleNames();
                $success['profile'] = [];
                $success['profile'] =  $user;
                $success['profile']['privilages'] = empty($roles[0]) ? '-' : $roles[0];
                $success['profile']['info'] = User::setInfo($roles, $user);
                $success['user_id'] = $user->id;
                $success['token']   = $user->createToken('MyApp')->accessToken;
                
                return $this->sendResponse($success, 'User login successfully.'); 
        }else{
                $success['id'] =  $username;
                return $this->sendResponseFalse('Unauthorised.', ['error'=>'Unauthorised']);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            $user->fcm_id = null;
            $user->save();
            $request->user()->token()->revoke();
            
        } catch (\Throwable $th) {   
            $success['messsage']='terjadi kesalahan';
            return $this->sendResponse($success, 'Force Logout.');
        }
        
        $success['messsage']='logout';
        return $this->sendResponse($success, 'Logout');
    }
}