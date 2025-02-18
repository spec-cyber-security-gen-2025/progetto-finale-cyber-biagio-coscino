<?php



namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Verifica se l'utente è amministratore
        if (!auth()->user()->is_admin) {
            return redirect()->route('home');  // Reindirizza se non è admin
        }

        return $next($request);
    }
}
