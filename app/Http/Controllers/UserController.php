<?php


namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Role;
use App\Modules\Analytics\Models\CustomerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'preferences' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            $customerRole = Role::where('name', 'Customer')->first();
            $roleId = $customerRole ? $customerRole->id : 2; 
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $roleId,
            ]);

            $profile = CustomerProfile::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'preferences' => $request->preferences,
            ]);

            DB::commit();

            return response()->json([
                'result' => [
                    'status' => 'Success 201',
                    'message' => "User with {$user->email} registered successfully"
                ],
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'role' => 'Customer'
                    ],
                    'profile' => [
                        'customer_profile_id' => $profile->id,
                        'name' => $profile->name
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'result' => [
                    'status' => 'Error 500',
                    'message' => 'Registration failed: ' . $e->getMessage()
                ]
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = User::with(['role', 'customerProfile'])->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'result' => [
                    'status' => 'Error 401',
                    'message' => 'Unauthorized: Incorrect email or password'
                ]
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'result' => [
                'status' => 'Success 200',
                'message' => "User with {$user->email} logged in successfully"
            ],
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role ? $user->role->name : 'Customer'
                ],
                'profile' => $user->customerProfile ? [
                    'customer_profile_id' => $user->customerProfile->id,
                    'name' => $user->customerProfile->name,
                    'preferences' => $user->customerProfile->preferences
                ] : null
            ]
        ], 200);
    }

     public function logout(Request $request)
    {
        $user = $request->user();

        $request->user()->currentAccessToken()->delete();

        $profile = $user->customerProfile;
        $roleName = $user->role ? $user->role->name : 'Customer';

        return response()->json([
            'result' => [
                'status' => 'Success 200',
                'message' => "User with {$user->email} logged out successfully" // Pesan disesuaikan untuk logout
            ],
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'role' => $roleName
                ],
                'profile' => $profile ? [
                    'customer_profile_id' => $profile->id,
                    'name' => $profile->name
                ] : null
            ]
        ], 200);
    }


}