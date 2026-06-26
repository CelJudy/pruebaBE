<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    if (!auth()->attempt($credentials)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $user = auth()->user();
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'user' => $user,
        'token' => $token,
    ]);
});

Route::get('/users/registered-by-month', function () {
    $data = [];
    $currentMonth = Carbon::now()->startOfMonth();

    for ($i = 2; $i >= 0; $i--) {
        $month = $currentMonth->copy()->subMonths($i);
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        $count = User::whereBetween('created_at', [$start, $end])->count();

        $data[] = [
            'label' => Str::ucfirst($month->locale('es')->translatedFormat('M')),
            'value' => $count,
        ];
    }

    return response()->json($data, 200);
})->middleware('auth:sanctum');

Route::get('/users/by-role', function () {
    $counts = User::select('role')
        ->selectRaw('COUNT(*) as value')
        ->groupBy('role')
        ->get()
        ->map(function ($item) {
            return [
                'role' => (int) $item->role,
                'value' => (int) $item->value,
            ];
        });

    return response()->json($counts, 200);
})->middleware('auth:sanctum');

Route::get('/get_users', function (Request $request) {
    $users = User::all();
    return response()->json($users, 201);
})->middleware('auth:sanctum');

Route::post('/create_user', function (Request $request) {
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email',
        'password' => 'required|string|min:8',
        'role' => 'required|integer',
    ]);

    $user = User::create($data);

    return response()->json($user, 201);
})->middleware('auth:sanctum');

Route::put('/update_user/{id}', function (Request $request, $id) {
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $data = $request->validate([
        'name' => 'sometimes|string|max:255',
        'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
        'password' => 'sometimes|string|min:8',
        'role' => 'sometimes|integer',
    ]);

    $user->update($data);

    return response()->json($user, 200);
})->middleware('auth:sanctum');

Route::delete('/delete_user/{id}', function (Request $request, $id) {
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $user->delete();

    return response()->json(['message' => 'User deleted successfully'], 200);
})->middleware('auth:sanctum');

