<?php

namespace App\Http\Controllers;

class UsersController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            [
                'uid' => 1,
                'id' => 1,
                'email' => 'gamer.fikri@gmail.com',
                'name' => 'Fikri Muhammad Iqbal',
            ],
            [
                'uid' => 2,
                'id' => 2,
                'email' => 'fikri.miqbal23@gmail.com',
                'name' => 'Fikri Muhammad Iqbal',
            ],
        ]);
    }
}
