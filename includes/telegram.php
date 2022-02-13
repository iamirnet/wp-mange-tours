<?php
$token = 'tokenbot';
define('API_KEY',"$token");
function bot($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}
function SendMessageP($chatid,$text,$parsmde,$disable_web_page_preview,$keyboard){
    bot('sendMessage',[
        'chat_id'=>$chatid,
        'text'=>$text,
        'parse_mode'=>$parsmde,
        'disable_web_page_preview'=>$disable_web_page_preview,
        'reply_markup'=>$keyboard
    ]);
}
function SendMessage($chatid,$text){
    bot('sendMessage',[
        'chat_id'=>$chatid,
        'text'=>$text,
    ]);
}
/* Tabee Forward Message */
function ForwardMessage($chatid,$from_chat,$message_id){
    bot('ForwardMessage',[
        'chat_id'=>$chatid,
        'from_chat_id'=>$from_chat,
        'message_id'=>$message_id
    ]);
}
function EditMessageText($chat_id,$message_id,$text,$parse_mode,$disable_web_page_preview,$keyboard){
    bot('editMessagetext',[
        'chat_id'=>$chat_id,
        'message_id'=>$message_id,
        'text'=>$text,
        'parse_mode'=>$parse_mode,
        'disable_web_page_preview'=>$disable_web_page_preview,
        'reply_markup'=>$keyboard
    ]);
}