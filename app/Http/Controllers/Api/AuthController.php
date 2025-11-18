<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
     * Register a new user
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
        ], 201);
    }

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
        ], 201);
    }

    /**
     * Login and get token (JWT - for backward compatibility)
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

        // 2️⃣ Attempt Login with JWT
        if (!$token = auth('api')->attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        $user = auth('api')->user();

        // 3️⃣ Check if user is active
        if (!$user->is_active) {
            return response()->json([
                'status'  => false,
                'message' => 'Please verify your email before login.'
            ], 403);
        }

        // 4️⃣ Get user's role
        $role = $user->roles->pluck('name')->first();

        // 5️⃣ Response with Token
        return response()->json([
            'status'  => true,
            'message' => 'Login successful',
            'data' => [
                'token'       => $token,
                'token_type'  => 'Bearer',
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
        // 1️⃣ Validate Request
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 422);
        }

        // 2️⃣ Attempt Login
        if (!auth()->attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        $user = auth()->user();

        // 3️⃣ Check if user is active
        if (!$user->is_active) {
            auth()->logout();
            return response()->json([
                'status'  => false,
                'message' => 'Please verify your email before login.'
            ], 403);
        }

        // 4️⃣ Create Sanctum Token
        $token = $user->createToken('api-token')->plainTextToken;

        // 5️⃣ Get user's role
        $role = $user->roles->pluck('name')->first();

        // 6️⃣ Response with Token
        return response()->json([
            'status'  => true,
            'message' => 'Login successful',
            'data' => [
                'token'       => $token,
                'token_type'  => 'Bearer',
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
     * Log the user out (Invalidate the token)
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
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Return token response
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'status'       => true,
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60,
            'user'         => auth('api')->user(),
        ]);
    }

	
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
            \Log::error('forgotPassword error: '.$e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Unable to send reset link. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Reset Password API
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

