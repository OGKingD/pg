<?php

use App\Lib\Services\Flutterwave;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Mail;


function send_email($to, $name, $subject, $message, $extras = [], $type = null)
{
    $from = config('mail.from.address');
    $template = 'emails/errormail';

    if ($type === "info") {
        $template = 'emails/infomail';
    }
    $site = config('app.name');
    $phone = "09058639550";
    $details = "Something Beautiful";
    $email = env('MAIL_FROM_ADDRESS');
    $logo = url('/') . '/asset/img/saanapay.png';
    $data = array('name' => $name, 'subject' => $subject, 'content' => $message, 'website' => $site, 'phone' => $phone, 'details' => $details, 'email' => $email, 'logo' => $logo);
    $data = array_merge($data, $extras);
    try {
        Mail::send(['html' => $template], $data, function ($message) use ($name, $to, $subject, $from, $site) {
            $message->to($to, $name);
            $message->subject($subject);
            $message->from($from, $site);
        });
    } catch (Exception $e) {
        logger($e->getMessage());
    }
}


/**
 * @return array ["quote" => "string", "author" => "string"]
 */
function inspirationalText()
{
    $text = Inspiring::quote();
    $array = explode('-', $text);
    $author = $array [count($array) - 1];
    array_pop($array);
    return ["quote" => implode(" ", $array), "author" => $author];

}


if (!function_exists('boomtime')) {
    function boomtime($timestamp)
    {
        //$time_now = mktime(date('h')+0,date('i')+30,date('s'));
        $datetime1 = new DateTime("now");
        $datetime2 = date_create($timestamp);
        $diff = date_diff($datetime1, $datetime2);
        $timemsg = '';
        if ($diff->h > 0) {
            $timemsg = $diff->h * 1;
        }
        if ($timemsg == "") {
            $timemsg = 0;
        } else {
            return $timemsg;
        }
    }
}


function encrypt3des($data, $secret)
{

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

function errorResponseJson($message, $payload): array
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

function queryWithDateRange($query, $builder)
{
    if ((array_key_exists('updated_at', $query) && !empty($query['updated_at'])) && (array_key_exists('end_date', $query) && !empty($query['end_date']))) {
        return $builder->whereBetween("updated_at", [$query['updated_at'], $query['end_date'] . " 23:59:59.999",]);
    }
    if (array_key_exists('updated_at', $query) && !empty($query['updated_at'])) {
        return $builder->whereDate('updated_at', $query['updated_at']);
    }
    if (array_key_exists('end_date', $query) && !empty($query['end_date'])) {
        return $builder->whereDate('updated_at', $query['end_date']);
    }
    return $builder;

}


/**
 * @param bool $percent
 * @return Flutterwave
 */
function getFlwave(bool $percent = false): Flutterwave
{
    if ($percent) {
        config()->set('flutterwave.encryption_key', config('flutterwave.percent_encryption_key'));
        config()->set('flutterwave.secret_key', config('flutterwave.percent_secret_key'));
        config()->set('flutterwave.public_key', config('flutterwave.percent_public_key'));
    }

    return new Flutterwave(config('flutterwave.secret_key'));
}
