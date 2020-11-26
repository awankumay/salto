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
                $success['profile'] = [];
                $success['profile'] =  $user;
                $roles = $user->getRoleNames();
                $success['profile']['privilages'] = empty($roles[0]) ? '-' : $roles[0];
               /* 
                dUwhmH9iTmGA77QPHVOpCX:APA91bHdCYxP5zfxQzVb2XL6sOa0ILIC9BpwIabXsal0VWZXZgVzlZOqUSEHSOwFHbo93d7_ZL3R1OU5TRHYIAjoZ4lBpDs_kFCJemvPHCYXQTGyS6-f6iVsvrJZK_qxZ2vJJtwR5SME
               $success['permission'] = [];
                foreach ($user->getAllPermissions() as $key => $vals) {
                    $success['permission'][]=$vals->name;
                } */
                $success['profile']['keluarga_asuh'] = null;
                $success['user_id'] = $user->id;
                $success['token']   = $user->createToken('MyApp')->accessToken;
                
                if($roles['0']=='Taruna'){
                    $keluarga = TarunaKeluargaAsuh::join('keluarga_asuh', 'keluarga_asuh.id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                                        ->where('taruna_keluarga_asuh.taruna_id', $user->id)
                                        ->first();
                    $keluarga_asuh = !empty($keluarga) ? strtolower($keluarga->name) : null;
                    $success['profile']['keluarga_asuh'] = $keluarga_asuh;
                    $success['profile']['subscribe']  = [  'salto', 
                                                           'taruna', 
                                                            Str::slug($keluarga_asuh, '-'), 
                                                            'taruna-'.Str::slug($keluarga_asuh, '-'), 
                                                            'grade-'.$user->grade,
                                                            'taruna-'.$user->id
                                                            ];
                }else if ($roles['0']=='Orang Tua') {
                    $taruna = OrangTua::where('orangtua_id', $user->id)->first();
                    $success['profile']['keluarga_asuh'] = null;
                    $success['profile']['subscribe']   = Str::slug('salto', 'orangtua', 'taruna-'.$taruna->taruna_id);
                }else if ($roles['0']=='Pembina') {
                    $keluarga = PembinaKeluargaAsuh::join('keluarga_asuh', 'keluarga_asuh.id', '=', 'pembina_keluarga_asuh.keluarga_asuh_id')
                                        ->where('pembina_keluarga_asuh.pembina_id', $user->id)
                                        ->first();
                    $keluarga_asuh = !empty($keluarga) ? strtolower($keluarga->name) : null;
                    $success['profile']['keluarga_asuh'] = $keluarga_asuh;
                    $success['profile']['subscribe']   = [ 'salto',
                                                            Str::slug($keluarga_asuh, '-'),
                                                            'pembina', 
                                                            Str::slug('pembina-'.$keluarga_asuh, '-')];
                }else if ($roles['0']=='Wali Asuh') {
                    $keluarga = WaliasuhKeluargaAsuh::join('keluarga_asuh', 'keluarga_asuh.id', '=', 'waliasuh_keluarga_asuh.keluarga_asuh_id')
                                    ->where('waliasuh_keluarga_asuh.waliasuh_id', $user->id)
                                    ->first();
                    $keluarga_asuh = !empty($keluarga) ? strtolower($keluarga->name) : null;
                    $success['profile']['keluarga_asuh'] = $keluarga_asuh;
                    $success['profile']['subscribe']   = [ 'salto',
                                                            Str::slug($keluarga_asuh, '-'),
                                                            'waliasuh', 
                                                            Str::slug('waliasuh-'.$keluarga_asuh, '-')];
                }else if ($roles['0']=='Akademkik dan Ketarunaan') {
                    $success['profile']['subscribe']   = ['salto', 'akademik-dan-ketarunaan'];
                }else if ($roles['0']=='Direktur') {
                    $success['profile']['subscribe']   = ['salto', 'direktur'];
                }else if ($roles['0']=='Admin') {
                    $success['profile']['subscribe']   = ['admin'];
                }else if ($roles['0']=='Super Admin') {
                    $success['profile']['subscribe']   = ['super-admin'];
                }else{
                    $success['profile']['keluarga_asuh'] = null;
                }
                return $this->sendResponse($success, 'User login successfully.'); 
        }else{
                $success['id'] =  $username;
                return $this->sendResponseFalse('Unauthorised.', ['error'=>'Unauthorised']);
        }
       /*  if(filter_var($username, FILTER_VALIDATE_EMAIL)) {
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
        } */
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