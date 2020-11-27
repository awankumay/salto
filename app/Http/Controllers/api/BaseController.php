<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use App\User;
use App\Grade;

class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
        if(!empty($result['id_user'])){
            $data = User::leftjoin('grade_table as grade', 'grade.id', '=', 'users.grade')
                    ->where('users.id', $result['id_user'])
                    ->select('users.stb', 'users.name', 'grade.grade', 'users.photo')
                    ->first();
            $data->photo = !empty($data->photo) ? \URL::to('/').'/storage/'.config('app.userImagePath').'/'. $data->photo : \URL::to('/').'/profile.png';
            $result['profile']=$data;
        }
    	$response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];


        return response()->json($response, 200);
    }

    public function sendResponseFalse($result, $message)
    {
    	$response = [
            'success' => false,
            'data'    => $result,
            'message' => $message,
        ];


        return response()->json($response, 422);
    }

    public function sendResponseError($result, $message)
    {
    	$response = [
            'success' => false,
            'data'    => $result,
            'message' => $message,
        ];


        return response()->json($response, 500);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
    	$response = [
            'success' => false,
            'message' => $error,
        ];


        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }


        return response()->json($response, $code);
    }
}