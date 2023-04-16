<?php

namespace App\Http\Middleware;

use App\Models\RequestLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class TerminatingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $created_at = Carbon::now();
        $created_at_withMilliseconds = $created_at->format("Y-m-d H:i:s:u A");
        $request->attributes->set('created_at',  $created_at);
        $request->attributes->set('created_at_withMilliseconds',  $created_at_withMilliseconds);
        return $next($request);
    }

    /**
     * @param Request $request
     * @param Response  $response
     *@throws \Exception
     */
    public function terminate(Request $request, $response): void
    {
        //Log to csv file


        if ($request->is("api*")) {
            //log only specific routes;
            $url = $request->path();

            if (preg_match("/".config('request_logs.routes')."/i",substr($url,4))) {
                $trnxRef = str_pad((int)preg_replace('/\D/', "", microtime(true)) . random_int(0, 99999), 16, random_int(0, 999999), STR_PAD_RIGHT);
                $payload = json_encode($request->all(), JSON_THROW_ON_ERROR);
                $url = $request->path();
                $method = $request->method();
                $request_response = json_encode($response->content(), JSON_THROW_ON_ERROR);
                $merchant_id = $request->user()->id ?? null;
                $request_id = $request->request_id ?? null;
                $created_at = $request->get('created_at');
                $created_at_withMilliseconds = $request->get('created_atwithMilliseconds');
                $updated_at = Carbon::now();
                $updated_at_withMilliseconds = $updated_at->format("Y-m-d H:i:s:u A");
                $response_time = Carbon::parse($updated_at)->diffInMilliseconds($created_at);
                Log::channel('merchant_request_log')->info("$trnxRef,$merchant_id,$request_id,$method,$url,$payload,$request_response,{$response->status()},$created_at_withMilliseconds,$updated_at_withMilliseconds,$response_time ");
                RequestLog::logRequest($request_id??$trnxRef,$url,$merchant_id, $request->all(), json_decode($request_response, false, 512, JSON_THROW_ON_ERROR));

            }
        }


    }
}
