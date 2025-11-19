<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;
use App\Mail\VerifyUserEmail;
use App\Models\User;
use App\Models\Role;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Notifications\CustomResetPasswordNotification;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{

	/**
     * @OA\Get(
     *     path="/api/roles",
     *     summary="Get available roles",
     *     tags={"Authentication"},
     *     @OA\Response(
     *         response=200,
     *         description="Roles fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Roles fetched successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="captain"),
     *                     @OA\Property(property="status", type="string", example="Active")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
	public function getRoles()
    {
        try {
            $roles = Role::where('status', 'Active')
                ->where('name', '!=', 'super_admin')
                ->where('name', '!=', 'user')
                ->select('id', 'name', 'status')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Roles fetched successfully',
                'data' => $roles
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/signup",
     *     summary="Sign up a new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name","last_name","email","password","role"},
     *             @OA\Property(property="first_name", type="string", maxLength=255, example="John"),
     *             @OA\Property(property="last_name", type="string", maxLength=255, example="Doe"),
     *             @OA\Property(property="email", type="string", format="email", maxLength=255, example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", minLength=8, example="SecurePass123!"),
     *             @OA\Property(property="role", type="string", example="captain", description="Must be an existing role name")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Signup successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Signup successful. Please check your email to verify your account."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *                 @OA\Property(property="profile_url", type="string", example="https://example.com/p/encrypted_id"),
     *                 @OA\Property(property="qrcode", type="string", example="/storage/qrcodes/user_1_abc123.png")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
	public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'password'   => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#^()_+\-=\[\]{};\':"\\|,.<>\/])[A-Za-z\d@$!%*?&#^()_+\-=\[\]{};\':"\\|,.<>\/]{8,}$/'
            ],
            'role'       => 'required|string|exists:roles,name',
        ], [
            'password.regex' => 'Password must contain at least 8 characters with uppercase, lowercase, number, and special character.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'is_active'  => false,
        ]);

        $user->assignRole($request->role);

        $expires = Carbon::now()->addMinutes(1440);
        $verificationUrl = URL::temporarySignedRoute('user.verify', $expires, ['id' => $user->id]);
        Mail::to($user->email)->send(new VerifyUserEmail($user, $verificationUrl));


        $encryptedId = Crypt::encryptString($user->id);
        $profileUrl = url('/p/' . urlencode($encryptedId));

        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel'   => QRCode::ECC_H,
            'scale'      => 7,
        ]);

        $qrcode = new QRCode($options);
        $qrImage = $qrcode->render($profileUrl);

        $manager = new ImageManager(new Driver());
        $qr = $manager->read($qrImage);


        $logoPath = public_path('images/ywc-logo.png');
        if (file_exists($logoPath)) {

            $logo = $manager->read($logoPath);
            $logo->scaleDown(intval($qr->width() * 0.28), intval($qr->height() * 0.28));

            $circleSize = max($logo->width(), $logo->height()) + 18;
            $circle = imagecreatetruecolor($circleSize, $circleSize);
            imagesavealpha($circle, true);
            $transparent = imagecolorallocatealpha($circle, 0, 0, 0, 127);
            imagefill($circle, 0, 0, $transparent);

            $white = imagecolorallocate($circle, 255, 255, 255);
            imagefilledellipse($circle, $circleSize / 2, $circleSize / 2, $circleSize, $circleSize, $white);

            $logoGd = imagecreatefromstring($logo->encode()->toString());
            imagesavealpha($logoGd, true);

            imagecopy(
                $circle,
                $logoGd,
                intval(($circleSize - $logo->width()) / 2),
                intval(($circleSize - $logo->height()) / 2),
                0, 0, $logo->width(), $logo->height()
            );

            ob_start();
            imagepng($circle);
            $circleBinary = ob_get_clean();
            imagedestroy($circle);
            imagedestroy($logoGd);

            $circleIntervention = $manager->read($circleBinary);
            $qr->place($circleIntervention, 'center');
        }

        $qrImage = $qr->encode()->toString();

        $filename = 'user_'.$user->id.'_'.Str::random(6).'.png';
        Storage::put('public/qrcodes/'.$filename, $qrImage);


        $user->qrcode = Storage::url('public/qrcodes/'.$filename);
        $user->profile_url = $profileUrl;
        $user->save();


        return response()->json([
            'status' => true,
            'message' => 'Signup successful. Please check your email to verify your account.',
            'data' => [
                'user_id' => $user->id,
                'email' => $user->email,
                'profile_url' => $user->profile_url,
                'qrcode' => $user->qrcode,
            ]
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user (alternative endpoint)",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name","last_name","email","password","password_confirmation"},
     *             @OA\Property(property="first_name", type="string", maxLength=100, example="John"),
     *             @OA\Property(property="last_name", type="string", maxLength=100, example="Doe"),
     *             @OA\Property(property="email", type="string", format="email", maxLength=100, example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", minLength=8, example="SecurePass123!"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="SecurePass123!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User registered successfully!"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|string|email|max:100|unique:users',
            'password'   => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#^()_+\-=\[\]{};\':"\\|,.<>\/])[A-Za-z\d@$!%*?&#^()_+\-=\[\]{};\':"\\|,.<>\/]{8,}$/',
                'confirmed'
            ],
        ], [
            'password.regex' => 'Password must contain at least 8 characters with uppercase, lowercase, number, and special character.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => bcrypt($request->password),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'User registered successfully!',
            'user'    => $user,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login and receive access tokens",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="Secret123!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="token", type="string", example="1|abcdef123456", description="Sanctum token for auth:sanctum routes"),
     *                 @OA\Property(property="token_type", type="string", example="Bearer"),
     *                 @OA\Property(property="jwt_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGc...", description="Legacy JWT token"),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="first_name", type="string", example="John"),
     *                 @OA\Property(property="last_name", type="string", example="Doe"),
     *                 @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *                 @OA\Property(property="role", type="string", example="captain"),
     *                 @OA\Property(property="profile_url", type="string", example="https://example.com/p/encrypted_id"),
     *                 @OA\Property(property="qrcode", type="string", example="/storage/qrcodes/user_1_abc123.png")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid email or password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Account not verified",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Please verify your email before login.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
	public function login(Request $request)
    {
        // 1️⃣ Validate Request
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // 2️⃣ Attempt Login with JWT (backward compatibility)
        if (!$jwtToken = auth('api')->attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        /** @var User $user */
        $user = auth('api')->user();

        // 3️⃣ Check if user is active
        if (!$user->is_active) {
            return response()->json([
                'status'  => false,
                'message' => 'Please verify your email before login.'
            ], 403);
        }

        // 🔄 Issue Sanctum token for new API flow
        // Remove previous tokens to avoid bloating table
        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }
        $sanctumToken = $user->createToken('api-token')->plainTextToken;

        // 4️⃣ Get user's role
        $role = $user->roles->pluck('name')->first();

        // 5️⃣ Response with Token
        return response()->json([
            'status'  => true,
            'message' => 'Login successful',
            'data' => [
                'token'       => $sanctumToken, // Sanctum token for auth:sanctum routes
                'token_type'  => 'Bearer',
                'jwt_token'   => $jwtToken, // Legacy JWT token (optional)
                'user_id'     => $user->id,
                'first_name'  => $user->first_name,
                'last_name'   => $user->last_name,
                'email'       => $user->email,
                'role'        => $role,
                'profile_url' => $user->profile_url,
                'qrcode'      => $user->qrcode
            ]
        ], 200);
    }

    /**
     * Login with Sanctum (for API routes using auth:sanctum)
     */
    public function loginSanctum(Request $request)
    {
        // Legacy endpoint now delegates to the main login method
        return $this->login($request);
    }
	
    public function loginOld(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User
     */
    public function me()
    {
        return response()->json([
            'status' => true,
            'user'   => auth('api')->user(),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout user and invalidate token",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User logged out successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid token"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Logout failed"
     *     )
     * )
     */
    public function logout()
    {
        try {
            auth('api')->logout();

            return response()->json([
                'status' => true,
                'message' => 'User logged out successfully'
            ], 200);

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid token'
            ], 401);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh a token
     */
    public function refresh()
    {
        return $this->respondWithToken(JWTAuth::refresh());
    }

    /**
     * Return token response
     */
    protected function respondWithToken($token)
    {
        $ttl = method_exists(JWTAuth::factory(), 'getTTL')
            ? JWTAuth::factory()->getTTL()
            : config('jwt.ttl', 60);

        return response()->json([
            'status'       => true,
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => $ttl * 60,
            'user'         => auth('api')->user(),
        ]);
    }

	/**
     * @OA\Post(
     *     path="/api/forgot-password",
     *     summary="Request password reset",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset link sent",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Password reset link sent to your email address.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Unable to send reset link"
     *     )
     * )
     */
	public function forgotPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();

            if (! $user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found.',
                ], 404);
            }

            $token = Password::createToken($user);
            $orgName = config('app.name', 'YWC');
            $user->notify(new CustomResetPasswordNotification($token, $orgName));

            return response()->json([
                'status'  => true,
                'message' => 'Password reset link sent to your email address.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('forgotPassword error: '.$e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Unable to send reset link. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/reset-password",
     *     summary="Reset password with token",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","token","password","password_confirmation"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="token", type="string", example="reset_token_here"),
     *             @OA\Property(property="password", type="string", format="password", minLength=8, example="NewSecurePass123!"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="NewSecurePass123!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Password has been reset successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid or expired token",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid or expired reset token.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'                 => 'required|email|exists:users,email',
            'token'                 => 'required|string',
            'password'              => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#^()_+\-=\[\]{};\':"\\|,.<>\/])[A-Za-z\d@$!%*?&#^()_+\-=\[\]{};\':"\\|,.<>\/]{8,}$/',
                'confirmed'
            ],
            'password_confirmation' => 'required'
        ], [
            'password.regex' => 'Password must contain at least 8 characters with uppercase, lowercase, number, and special character.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'status'  => true,
                'message' => 'Password has been reset successfully.',
            ], 200);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Invalid or expired reset token.',
        ], 400);
    }


}

