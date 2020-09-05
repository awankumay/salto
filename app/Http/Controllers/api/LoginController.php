<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
   
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
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['name'] =  $user->name;
            $success['role'] = $roles = $user->roles()->pluck('name');
            if($success['role']['0']!='Pengunjung'){
                return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
            }
            $success['token'] =  $user->createToken('MyApp')->accessToken; 
   
            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{
            $success['name'] =   $request->email;
            return $this->sendResponseFalse($success, 'User login failed.');
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