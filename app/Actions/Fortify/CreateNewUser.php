<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Mail\VerifyUserEmail;
use Carbon\Carbon;
use Laravel\Jetstream\Jetstream;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        Validator::make($input, [
            'first_name'        => ['required', 'string', 'max:255'],
            'last_name'         => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'          => $this->passwordRules(),
            'role'              => ['required', 'string', 'exists:roles,name'],
            'vessel_flag_state' => ['nullable', 'string', 'max:100'],
            'terms'             => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        $user = User::create([
            'first_name'        => $input['first_name'],
            'last_name'         => $input['last_name'],
            'email'             => $input['email'],
            'password'          => Hash::make($input['password']),
            'vessel_flag_state' => $input['vessel_flag_state'] ?? null,
            'is_active'         => false,
        ]);

        $user->assignRole($input['role']);

        // Auto-subscribe to main community thread
        if (config('forum.main_thread_id')) {
            try {
                $subscriptionService = app(\App\Services\Forum\ForumSubscriptionService::class);
                $subscriptionService->subscribeNewUser($user);
            } catch (\Exception $e) {
                // Log error but don't fail registration
                \Log::warning("Failed to auto-subscribe user to main thread: " . $e->getMessage());
            }
        }
        
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

            $logoMaxWidth = intval($qr->width() * 0.28);
            $logoMaxHeight = intval($qr->height() * 0.28);

            $logo->scaleDown($logoMaxWidth, $logoMaxHeight);

            $qr->place($logo, 'center');
        }

        $qrImage = $qr->encode()->toString();

        $filename = 'user_'.$user->id.'_'.Str::random(6).'.png';
        $path = 'public/qrcodes/' . $filename;
        Storage::put($path, $qrImage);

        $user->qrcode = Storage::url($path);
        $user->profile_url = $profileUrl;
        $user->save();

        return $user;
    }

    public function showRegistrationForm()
    {
        $roles = Role::whereNotIn('name', ['super_admin'])->pluck('name');
        return view('auth.register', compact('roles'));
    }
}