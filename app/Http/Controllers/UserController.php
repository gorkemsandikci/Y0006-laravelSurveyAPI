<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {

        /** @var User $user */
        $user = auth('survey')->user();

        $data = DB::table('users AS u')
            ->select([
                'u.id', 'u.email', 'u.name', 'u.surname', 'u.phone',
                'u.birthday', 'u.gender', 'u.marital_status', 'u.province',
                'u.profession', 'u.foreign_reference',
                'u.user_reference', 'u.created_at', 'u.updated_at'
            ])
            ->where('u.id', '=', $user->id)
            ->whereNull('u.deleted_at')
            ->first();

        if ($data === null) {
            return response()->json([
                'status' => false,
                'status_code' => 421,
                'message' => 'Kullanıcı bulunamadı'
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $data->id,
                'name' => $data->name,
                'surname' => $data->surname,
                'email' => $data->email,
                'birthday' => Carbon::createFromFormat('d/m/Y', $data->birthday)->format('d-m-Y'),
                'gender' => $data->gender,
                'maritalStatus' => $data->marital_status,
                'phone' => $data->phone,
                'province' => $data->province,
                'profession' => $data->profession,
                'foreignReference' => $data->foreign_reference,
                'userReference' => $data->user_reference,
                'createdAt' => Carbon::parse($data->created_at)->format('Y-m-d H:i')
            ]
        ]);
    }
}
