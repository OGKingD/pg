<?php

use App\Models\Settings;
use App\Models\User;

use Illuminate\Support\Facades\Mail;
use JetBrains\PhpStorm\ArrayShape;


function send_email($to, $name, $subject, $message, $extras=[], $type=null) {
    $from=env('MAIL_FROM_ADDRESS');
    $template = 'emails/errormail';

    if ($type === "info"){
        $template = 'emails/infomail';
    }
    $site=config('app.name');
    $phone="09058639550";
    $details="Something Beautiful";
    $email=env('MAIL_FROM_ADDRESS');
    $logo=url('/').'/asset/img/saanapay.png';
    $data=array('name'=>$name,'subject'=>$subject,'content'=>$message,'website'=>$site,'phone'=>$phone,'details'=>$details,'email'=>$email,'logo'=>$logo);
    $data = array_merge($data,$extras);
    Mail::send(['html' => $template], $data, function($message) use($name, $to, $subject, $from, $site) {
    $message->to($to, $name);
    $message->subject($subject);
    $message->from($from, $site);
    });
}


/**
 * @return array ["quote" => "string", "author" => "string"]
 */
#[ArrayShape(["quote" => "string", "author" => "string"])] function inspirationalText()
{
    $text = \Illuminate\Foundation\Inspiring::quote();
    $array = explode('-',$text);
    $author = $array [count($array) - 1];
    array_pop($array);
    return ["quote" => implode(" ",$array), "author" => $author];

}



if (! function_exists('boomtime'))
{
    function boomtime($timestamp){
        //$time_now = mktime(date('h')+0,date('i')+30,date('s'));
        $datetime1=new DateTime("now");
        $datetime2=date_create($timestamp);
        $diff=date_diff($datetime1, $datetime2);
        $timemsg='';
        if($diff->h > 0){
            $timemsg = $diff->h * 1;
        }
        if($timemsg == "")
            $timemsg = 0;
        else

        return $timemsg;
    }
}


function encrypt3des($data,$secret){

    $key = md5(mb_convert_encoding($secret, 'UTF-16LE', 'UTF-8'), true);
    $key .= substr($key, 0, 8);
    $encData = openssl_encrypt($data, 'DES-EDE3-CBC', $key, OPENSSL_RAW_DATA, openssl_random_pseudo_bytes(8));
    return base64_encode($encData);

}


/**
 * @return User
 */
function company()
{
    return User::firstWhere('email', config('app.company_email'));

}

function errorResponseJson($message,$payload): array
{
    return [
        "message" => "$message",
        "errors" => $payload
    ];

}

function nairaSymbol()
{
    return '8358;';

}
