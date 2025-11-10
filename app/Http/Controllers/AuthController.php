<?php
namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;


class AuthController extends Controller
{
    /**
     * Register a new user with default role and permission, and return JWT token.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
      public function register(Request $request)
    {
        try {
            // Check for duplicate email manually
            if (User::where('email', $request->email)->exists()) {
                return response()->json([
                    'error' => 'Email already exists',
                ], 422);
            }

            $roleId       = $request->role_id ?? Role::where('name', 'admin')->value('_id');
            $permissionId = $request->permission_id ?? Permission::where('name', 'view_admin')->value('_id');

            if (! $roleId || ! $permissionId) {
                return response()->json([
                    'error' => 'Default role or permission not found.',
                ], 500);
            }

            $user = User::create([
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'email'      => $request->email,
              'password' => Hash::make($request->password),

                'is_active'  => false,
            ]);
    $expires = Carbon::now()->addMinutes(1440); // 24 hours
        $verificationUrl = URL::temporarySignedRoute(
            'user.verify', $expires, ['id' => $user->id]
        );

        // Send activation email
        Mail::to($user->email)->send(new \App\Mail\VerifyUserEmail($user, $verificationUrl));
            UserRolePermission::create([
                'user_id'       => $user->_id,
                'role_id'       => $roleId,
                'permission_id' => $permissionId,
            ]);

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'message' => 'User registered successfully',
                'user'    => $user,
                'token'   => $token,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Registration failed',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Authenticate user and return JWT token if credentials are valid.
     * Also validates role and permission.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

 public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    // Check credentials
    if (! $token = \JWTAuth::attempt($credentials)) {
        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    $user = auth()->user();

    // 🔒 Check if account is active
    if (! $user->is_active) {
        return response()->json(['error' => 'Your account is not activated. Please check your email to activate your account.'], 403);
    }

    // Check if user has a role and permission
    $userRolePermission = \App\Models\UserRolePermission::where('user_id', $user->_id)->first();

    if (! $userRolePermission) {
        return response()->json(['error' => 'No role/permission assigned to user'], 403);
    }

    $roleName = $userRolePermission->role ? $userRolePermission->role->name : 'No role assigned';

    // Return success response
    return response()->json([
        'success'    => true,
        'message'    => 'Login successful',
        'token'      => $token,
        'role_id'    => $roleName,
        "deviceType" => "",
        "deviceId"   => "",
        "fcmToken"   => "",
        'first_name' => $user->first_name,
        'last_name'  => $user->last_name,
    ]);
}

    /**
     * Logout the authenticated user by invalidating their JWT token.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            // Check for token manually
            $token = $request->bearerToken();

            if (! $token) {
                return response()->json(['error' => 'Token not provided'], 400);
            }

            JWTAuth::setToken($token)->invalidate();

            return response()->json([
                'message' => 'User logged out successfully',
            ], 200);

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['error' => 'Token is invalid'], 401);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Something went wrong',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    // Ye pehle se aapke paas hai:
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $otp = rand(100000, 999999);

        $user = User::firstOrCreate(
            ['email' => $request->email],
            [
                'first_name' => '',
                'last_name'  => '',
                'password'   => bcrypt(Str::random(16)),
            ]
        );

        $user->otp            = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        Mail::raw("Your OTP is: $otp", function ($message) use ($user) {
            $message->to($user->email)->subject('Your OTP');
        });

        return response()->json(['message' => 'OTP sent to your email!']);
    }


public function verifyOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'otp'   => 'required|digits:6',
    ]);

    $user = User::where('email', $request->email)->first();

    // Extra safety for expiry field
    $isExpired = true;
    if ($user && $user->otp_expires_at) {
        try {
            // $user->otp_expires_at already a Carbon instance due to $casts
            $isExpired = $user->otp_expires_at->isPast();
        } catch (\Exception $e) {
            $isExpired = true;
        }
    }

    if (
        ! $user ||
        ! $user->otp ||
        ! $user->otp_expires_at ||
        $user->otp != $request->otp ||
        $isExpired
    ) {
        return response()->json([
            'message' => 'Invalid or expired OTP.',
            'status'  => false,
        ], 401);
    }

    // OTP is valid, clear OTP fields
    $user->otp            = null;
    $user->otp_expires_at = null;
    $user->save();

    // JWT login
    $token = auth()->login($user);

    // Optional: Return user status as boolean
    $userData = $user->toArray();
    $userData['status'] = $user->status ? true : false;

    return response()->json([
        'message' => 'Login successful!',
        'user'    => $userData,
        'token'   => $token,
    ]);
}

}
