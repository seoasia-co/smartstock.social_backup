<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Log;

class IPMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        /* if ($request->ip() != "198.20.110.251" && $request->ip() != "103.241.66.206") {
            // here instead of checking a single ip address we can do collection of ips
            //address in constant file and check with in_array function
                return redirect('home');
            } */

        Log::debug('check IP IPMiddleware: ' . $request->ip());
            
        /* $allowed_ip_addresses = "198.20.110.251, 103.241.66.206"; // add IP's by comma separated
        $ipsAllow = explode(',', preg_replace('/\s+/', '', $allowed_ip_addresses));

        // check ip is allowed
        if (count($ipsAllow) >= 1) {

            if (!in_array(request()->ip(), $ipsAllow)) {
                // return response
                $response =(array(
                    'success' => false,
                    'message' => 'You are blocked to call API!'
                ));

                return response()->json( $response );

            }

        } */

        $allowedHosts = explode(',', env('ALLOWED_DOMAINS'));

        $requestHost = parse_url($request->headers->get('origin'),  PHP_URL_HOST);

        $message_res = '';

        if(!app()->runningUnitTests()) {
            if(!\in_array($requestHost, $allowedHosts, false)) {
                $requestInfo = [
                    'host' => $requestHost,
                    'ip' => $request->getClientIp(),
                    'url' => $request->getRequestUri(),
                    'agent' => $request->header('User-Agent'),
                ];
                //event(new UnauthorizedAccess($requestInfo));
                $message_res.=json_encode($requestInfo);


                //throw new SuspiciousOperationException('This host is not allowed');
                $response =(array(
                    'success' => false,
                    'message' => $message_res.' You .. are blocked to call API!'
                ));
        
                return response()->json( $response );
            
            }
        }
        


        return $next($request);
    }
}


