<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Validator;
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
        if(filter_var($username, FILTER_VALIDATE_EMAIL)) {
            //user sent their email 
            if(Auth::attempt(['email' => $username, 'password' => $password])){
                $user = Auth::user(); 
                $success['profile'] =  $user;
                $roles = $user->getRoleNames();
                $success['privilages'] = empty($roles[0]) ? '-' : $roles[0];
                $success['permission'] = [];
                foreach ($user->getAllPermissions() as $key => $vals) {
                    $success['permission'][]=$vals->name;
                }
                $success['user_id'] = $user->id;
                $success['token'] =  $user->createToken('MyApp')->accessToken; 
       
                return $this->sendResponse($success, 'User login successfully.');
            }else{
                $success['id'] =  $username;
                return $this->sendResponseFalse('Unauthorised.', ['error'=>'Unauthorised']);
            }
        } else {
            //they sent their username instead 
            $user = User::where('stb', $username)->whereRaw('stb is not null')->first();
            if(!empty($user->stb) && Auth::attempt(['stb' => $username, 'password' => $password])){
                $user = Auth::user(); 
                $success['profile'] =  $user;
                $roles = $user->getRoleNames();
                $success['privilages'] = empty($roles[0]) ? '-' : $roles[0];
                $success['permission'] = [];
                foreach ($user->getAllPermissions() as $key => $vals) {
                    $success['permission'][]=$vals->name;
                }
                $success['user_id'] = $user->id;
                $success['token'] =  $user->createToken('MyApp')->accessToken; 
       
                return $this->sendResponse($success, 'User login successfully.');
            }else{
                $success['id'] =   $username;
                return $this->sendResponseFalse('Unauthorised.', ['error'=>'Unauthorised']);
            }
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->token()->revoke();
        } catch (\Throwable $th) {   
            $success['messsage']='terjadi kesalahan';
            return $this->sendResponse($success, 'Force Logout.');
        }
        
        $success['messsage']='logout';
        return $this->sendResponse($success, 'Logout');
    }
}