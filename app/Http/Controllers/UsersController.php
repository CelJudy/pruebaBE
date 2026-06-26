<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class UsersController extends Controller
{
    #[OA\Post(
        path: '/api/login',
        tags: ['Users'],
        summary: 'Authenticate user and return token',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string', format: 'password'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful authentication',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'user', type: 'object'),
                        new OA\Property(property: 'token', type: 'string'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Invalid credentials'),
        ]
    )]
    public function login(Request $request)
    {
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
    }

    #[OA\Get(
        path: '/api/users/registered-by-month',
        tags: ['Users'],
        summary: 'Get users registered in the last three months',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Users registered by month',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'label', type: 'string'),
                            new OA\Property(property: 'value', type: 'integer'),
                        ]
                    )
                )
            ),
        ]
    )]
    public function registeredByMonth()
    {
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
    }

    #[OA\Get(
        path: '/api/users/by-role',
        tags: ['Users'],
        summary: 'Get user counts grouped by role',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User counts by role',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'role', type: 'integer'),
                            new OA\Property(property: 'value', type: 'integer'),
                        ]
                    )
                )
            ),
        ]
    )]
    public function byRole()
    {
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
    }

    #[OA\Get(
        path: '/api/get_users',
        tags: ['Users'],
        summary: 'Get all users',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 201,
                description: 'List of users',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/User')
                )
            ),
        ]
    )]
    public function getUsers()
    {
        $users = User::all();
        return response()->json($users, 201);
    }

    #[OA\Post(
        path: '/api/create_user',
        tags: ['Users'],
        summary: 'Create a new user',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password', 'role'],
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string', format: 'password'),
                    new OA\Property(property: 'role', type: 'integer'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Created user',
                content: new OA\JsonContent(ref: '#/components/schemas/User')
            ),
        ]
    )]
    public function createUser(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|integer',
        ]);

        $user = User::create($data);

        return response()->json($user, 201);
    }

    #[OA\Put(
        path: '/api/update_user/{id}',
        tags: ['Users'],
        summary: 'Update an existing user',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string', format: 'password'),
                    new OA\Property(property: 'role', type: 'integer'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated user',
                content: new OA\JsonContent(ref: '#/components/schemas/User')
            ),
            new OA\Response(response: 404, description: 'User not found'),
        ]
    )]
    public function updateUser(Request $request, $id)
    {
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
    }

    #[OA\Delete(
        path: '/api/delete_user/{id}',
        tags: ['Users'],
        summary: 'Delete a user',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'User deleted successfully'),
            new OA\Response(response: 404, description: 'User not found'),
        ]
    )]
    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
