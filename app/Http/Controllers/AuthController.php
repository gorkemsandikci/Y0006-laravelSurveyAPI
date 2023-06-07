<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        //@todo Validator eklenecek
        $credentials = $request->only(['email', 'password']);

        if (!$this->checkAndManageActiveStatus(['email' => $credentials['email']])) {
            return response()->json([
                'status' => false,
                'status_code' => 401,
                'message' => 'Giriş yapılamadı'
            ]);
        }

        if (!$token = auth('survey')->attempt($credentials)) {
            return response()->json([
                'status' => false,
                'status_code' => 401,
                'message' => 'Giriş yapılamadı'
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'token' =>  $token,
                'tokenType' => 'Bearer'
            ]
        ]);
    }


    public function logout(): JsonResponse
    {
        $this->logoutUser();
        return response()->json([
            'status' => true,
            'message' => 'Başarıyla çıkış yapıldı'
        ]);
    }

    private function logoutUser(): void
    {
        if (auth('survey')->check()){
            auth('survey')->logout();
        }
    }

    private function checkAndManageActiveStatus(array $params): bool
    {
        //@todo Validator eklenecek
        $user = DB::table('users');

        if (array_key_exists('id', $params)) {
            $user->where('id', $params['id']);
        } elseif (array_key_exists('email', $params)) {
            $user->where('email', $params['email']);
        } else {
            $user->where('id', '0');
        }

        $user = $user->whereNull('deleted_at')->first();

        if ($user === null) {
            if (!auth('survey')->check()) {
                $this->logoutUser();
            }
            return false;
        }

        return true;
    }

    public function register(Request $request): JsonResponse
    {
        //@todo Validator eklenecek
        $check = DB::table('users')
            ->where('email', '=', $request->get('email'))
            ->exists();

        if ($request->filled('email') && $check) {
            return response()->json([
                'status' => false,
                'status_code' => 421,
                'message' => 'E-posta sistemde kayıtlı',
            ]);
        }

        $now_date_time_string = Carbon::now()->format('Y-m-d H:i:s');

        $id = DB::table('users')
            ->insertGetId([
                'name' => $request->get('name'),
                'surname' => $request->get('surname'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
                'password' =>  Hash::make($request->get('password')),
                'birthday' => $request->get('birthday'),
                'province' => $request->get('province'),
                'gender' => $request->get('gender'),
                'marital_status' => $request->get('maritalStatus'),
                'profession' => $request->get('profession'),
                'user_reference' => $request->get('reference'),
                'created_at' => $now_date_time_string,
                'updated_at' => $now_date_time_string,
            ]);

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $id,
                'messages' => ['Kullanıcı başarıyla oluşturuldu']
            ]
        ]);
    }
}
