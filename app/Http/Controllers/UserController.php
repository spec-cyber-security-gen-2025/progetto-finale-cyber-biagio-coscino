<?php
// app/Http/Controllers/UserController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function store(Request $request)
    {
        // Solo un amministratore può modificare `is_admin`
        if (!Auth::user()->is_admin) {
            // Rimuovi 'is_admin' dalla richiesta, se presente
            $request->merge(['is_admin' => false]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create($validated);  // Crea l'utente
    }
}
