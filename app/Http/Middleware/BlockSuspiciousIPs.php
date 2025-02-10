<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockSuspiciousIPs
{

    private $maxAttempts = 2;
    private $blockMinutes = 1;
    private $decayMinutes = 1;

    public function handle(Request $request, Closure $next)
    {

        $ip = $request->ip();
        $key = $this->throttleKey($ip);

        if (Cache::has($key . ':blocked')) {
            session()->flash('errors', 'You are sending too many requests. Please try again after ' . $this->blockMinutes . ' minute(s).');
            return response()->json(['error' => 'You are sending too many requests. Please try again after ' . $this->blockMinutes . ' minute(s).'], 429);
        }
        if (Cache::has($key)) {
            $attempts = Cache::increment($key);
            if ($attempts > $this->maxAttempts) {
                Cache::put($key . ':blocked', true, $this->blockMinutes * 60);
                Log::warning("IP  $ip has been blocked for $this->blockMinutes minute(s) due to too many attempts.");
                session()->flash('errors', 'Your IPs has been blocked. Please try again after ' . $this->blockMinutes . ' minute(s).');
                return response()->json(['error' => 'Your IPs has been blocked. Please try again after ' . $this->blockMinutes . ' minute(s).'], 429);
            }
        } else {
            Cache::put($key, 1, $this->decayMinutes * 60);
        }
        return $next($request);
    }

    protected function throttleKey($ip)
    {
        return 'throttle:' . sha1($ip);
    }
}