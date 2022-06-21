<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use Morilog\Jalali\Jalalian;
use App\Day;

class newMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        return $next($request);
        $env = config('app.key');
        $serverName = $request->server()['SERVER_NAME'];
//        $env = 'base64:C0SWFS/hXAKzH9tUIRYANxKjDLbTCTWpxw1MF/U9dsE=';
   $serverName = 'portal.khajenasir.sch.ir';
        $token = 'EJ0XC9wdGRXSjkzTmVkUVJzWHlvMXp4';
        $date = (new Jalalian(1401, 11, 1))->getTimestamp();
        $now = Jalalian::now()->getTimestamp();
        if ($now > $date) {
            return abort(403);
        }
//        dd($request->server()['SERVER_NAME']);
        $license = $date . $env . $serverName . $token;
   dd(Hash::make($license),$serverName );
        if (Hash::check($license, config('app.license'))) {
            return $next($request);
        } else {
            return  abort(403);
        }
    }


}
