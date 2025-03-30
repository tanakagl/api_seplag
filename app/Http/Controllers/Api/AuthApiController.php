<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class AuthApiController extends Controller 
{
       //Autenticação de usuário
       public function login(Request $request)
       {
           $request->validate([
               'email' => 'required|email',
               'password' => 'required',
           ]);
   
           $user = User::where('email', $request->email)->first();
   
           if (!$user || !Hash::check($request->password, $user->password)) {
               return response()->json([
                   'message' => 'As credenciais fornecidas estão incorretas.'
               ], 401);
           }
   
           $abilities = $this->getAbilitiesForRole($user->role);
           $token = $user->createToken('api-token', $abilities)->plainTextToken;
   
           return response()->json([
               'user' => [
                   'id' => $user->id,
                   'name' => $user->name,
                   'email' => $user->email,
                   'role' => $user->role,
               ],
               'token' => $token,
               'abilities' => $abilities,
               'expires_at' => now()->addMinutes(5)->toDateTimeString(),
           ]);
       }
   
       // Logout do usuário
       public function logout(Request $request)
       {
           $request->user()->currentAccessToken()->delete();
   
           return response()->json([
               'message' => 'Logout realizado com sucesso.'
           ]);
       }
   
       // Retornar o usuario autenticado
       public function me(Request $request)
       {
           return response()->json([
               'user' => $request->user()->only(['id', 'name', 'email', 'role']),
               'token_expires_at' => $this->getTokenExpirationTime($request->user()->currentAccessToken())
           ]);
       }
   
       // Definir as habilidades com base na role do usuario
       private function getAbilitiesForRole(string $role)
       {
           return match ($role) {
               'admin' => ['*'],
               'manager' => ['read', 'create', 'update'],
               default => ['read'],
           };
       }

       // Atualizar token
       public function refreshToken(Request $request)
       {
           $request->user()->currentAccessToken()->delete();
           $abilities = $this->getAbilitiesForRole($request->user()->role);
           $token = $request->user()->createToken('api-token', $abilities)->plainTextToken;
   
           return response()->json([
               'token' => $token,
               'abilities' => $abilities,
           ]);
       }

       private function getTokenExpirationTime($token)
       {
          $createdAt = $token->created_at;
          $expirationTime = $createdAt->addMinutes(5);
          return $expirationTime->toDateTimeString();
       }
}