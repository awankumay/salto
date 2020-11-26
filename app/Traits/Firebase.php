<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;

trait Firebase
{
    public $key='key=AAAAV5mIX3A:APA91bGD0intVaNDP-H9QxntyCp-eoX08P8oh-_5Rukb6jIFg58at1ebLMuTRlWg9cTfU_avsBcNURifYC3P13gep954DKGxiYnQF1EimLzEWYi_Om6JSdlE4DpfJ-0CfXdHyZ5yvQr2';
    public $url='https://fcm.googleapis.com/fcm/send';

    public function saveToken($data)
    {
        if (!empty($data['id_user'])) {
            $user  = User::find($data['id_user']);
            if(!empty($data['token'])){
                $user->fcm_id = $data['token'];
            }else{
                $user->fcm_id = NULL;
            }
            return true;
        }
        return false;
    }

    public function pushNotif($data)
    {
        $ch = curl_init();
        $headers  = [
                    'Authorization: '.$this->key.'',
                    'Content-Type: application/json'
                ];
        $postData = [
            'notification' => [
                'title'=>$data['title'],
                'body'=>$data['body'],
                'sound'=>'default',
                'click_action'=>"FCM_PLUGIN_ACTIVITY",
                'icon'=>"fcm_push_icon"
            ],
            'data' => [
                'title'=>$data['title'],
                'body'=>$data['body'],
                'image'=>!empty($data['image']) ? $data['image'] : null,
                'page'=>$data['page']
            ],
            'to'=>$data['token']
        ];
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));           
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result     = curl_exec ($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return $result;
    }
    
}
