<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OtpVerification;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function login()
    {
        return view('auth.login');
    }

    public function loginPost(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);

            $email = $request->input('email');
            $user = User::where('email', $email)->first();

            if(!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email belum terdaftar. Silakan daftar terlebih dahulu.'
                ], 422);
            }

            // Check if user registered via Google (no password)
            if ($user->provider === 'google' && !$user->password) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun ini terdaftar melalui Google. Silakan gunakan login Google.'
                ], 422);
            }

            if(Auth::attempt($request->only('email', 'password'))) {
                $redirectUrl = Auth::user()->email === 'admin@wifa.com' ? '/admin/dashboard' : '/';
                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil! Selamat datang kembali.',
                    'redirect_url' => $redirectUrl
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Password tidak benar. Silakan coba lagi.'
                ], 422);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.'
            ], 500);
        }
    }

    public function register()
    {
        return view('auth.register');
    }

    public function postRegister(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'whatsapp' => 'required|string|max:15',
            ]);

            // Check if email already exists in OTP verification table
            $existingOtp = OtpVerification::where('email', $request->email)->first();
            if ($existingOtp) {
                $existingOtp->delete();
            }

            // Generate 6-digit OTP
            $otpCode = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

            // Store registration data with OTP
            $otpVerification = OtpVerification::create([
                'name' => $request->name,
                'email' => $request->email,
                'whatsapp' => $request->whatsapp,
                'password' => Hash::make($request->password),
                'otp_code' => $otpCode,
                'expires_at' => Carbon::now()->addMinutes(5),
                'is_verified' => false
            ]);

            // Send OTP via WhatsApp
            $otpSent = $this->whatsappService->sendOTP($request->whatsapp, $otpCode, $request->name);

            if ($otpSent) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kode OTP telah dikirim ke WhatsApp Anda',
                    'verification_id' => $otpVerification->id,
                    'redirect_url' => route('otp.verify', ['id' => $otpVerification->id])
                ]);
            } else {
                $otpVerification->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim OTP. Silakan coba lagi.'
                ], 422);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.'
            ], 500);
        }
    }

    public function showOtpForm($id)
    {
        $otpVerification = OtpVerification::findOrFail($id);
        
        if ($otpVerification->isExpired()) {
            return redirect()->route('register')->with('error', 'Kode OTP telah kadaluarsa. Silakan daftar ulang.');
        }

        return view('auth.otp-verify', compact('otpVerification'));
    }

    public function verifyOtp(Request $request, $id)
    {
        try {
            $request->validate([
                'otp_code' => 'required|string|size:6'
            ]);

            $otpVerification = OtpVerification::findOrFail($id);

            if ($otpVerification->isValid($request->otp_code)) {
                // Create user account
                $user = User::create([
                    'name' => $otpVerification->name,
                    'email' => $otpVerification->email,
                    'password' => $otpVerification->password, // Already hashed
                    'telp' => $otpVerification->whatsapp
                ]);

                // Mark OTP as verified
                $otpVerification->update(['is_verified' => true]);

                // Send welcome message
                $this->whatsappService->sendWelcomeMessage($otpVerification->whatsapp, $otpVerification->name);

                // Delete OTP record
                $otpVerification->delete();

                // Auto login
                Auth::login($user);

                return response()->json([
                    'success' => true,
                    'message' => 'Registrasi berhasil! Selamat datang di WIFA Sport Center.',
                    'redirect_url' => '/'
                ]);

            } else {
                return response()->json([
                    'success' => false,
                    'message' => $otpVerification->isExpired() ? 'Kode OTP telah kadaluarsa' : 'Kode OTP tidak valid'
                ], 422);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.'
            ], 500);
        }
    }

    public function resendOtp($id)
    {
        try {
            $otpVerification = OtpVerification::findOrFail($id);

            // Generate new OTP
            $newOtpCode = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

            // Update OTP record
            $otpVerification->update([
                'otp_code' => $newOtpCode,
                'expires_at' => Carbon::now()->addMinutes(5),
                'is_verified' => false
            ]);

            // Send new OTP
            $otpSent = $this->whatsappService->sendOTP($otpVerification->whatsapp, $newOtpCode, $otpVerification->name);

            if ($otpSent) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kode OTP baru telah dikirim ke WhatsApp Anda'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim OTP. Silakan coba lagi.'
                ], 422);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.'
            ], 500);
        }
    }

    // Google OAuth Methods
    public function redirectToGoogle()
    {
        try {
            Log::info('Redirecting to Google OAuth');
            return Socialite::driver('google')->redirect();
        } catch (\Exception $e) {
            Log::error('Error redirecting to Google:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/login')->with('error', 'Gagal mengarahkan ke Google. Error: ' . $e->getMessage());
        }
    }

    public function handleGoogleCallback()
    {
        // Force error display untuk debugging
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        
        try {
            Log::info('Google OAuth Callback started');
            
            // Check if we have error from Google
            if (request()->has('error')) {
                $error = request()->get('error');
                $errorDescription = request()->get('error_description', 'No description');
                
                Log::error('Google OAuth returned error:', [
                    'error' => $error,
                    'description' => $errorDescription
                ]);
                
                return redirect('/login')->with('error', 'Google OAuth Error: ' . $error . ' - ' . $errorDescription);
            }
            
            // Check if we have code parameter
            if (!request()->has('code')) {
                Log::error('No authorization code received from Google');
                return redirect('/login')->with('error', 'No authorization code received from Google');
            }
            
            Log::info('Authorization code received, getting user data');
            
            $googleUser = Socialite::driver('google')->user();
            
            if (!$googleUser) {
                Log::error('Failed to get user data from Google');
                return redirect('/login')->with('error', 'Failed to get user data from Google');
            }
            
            // Log for debugging
            Log::info('Google OAuth Callback - User data received:', [
                'google_user_id' => $googleUser->id ?? 'null',
                'email' => $googleUser->email ?? 'null',
                'name' => $googleUser->name ?? 'null',
                'avatar' => $googleUser->avatar ?? 'null'
            ]);
            
            // Validate required fields
            if (!$googleUser->email || !$googleUser->name) {
                Log::error('Missing required fields from Google user data');
                return redirect('/login')->with('error', 'Missing required user information from Google');
            }
            
            // Check if user already exists
            $user = User::where('email', $googleUser->email)->first();
            
            if ($user) {
                Log::info('Existing user found', ['user_id' => $user->id, 'email' => $user->email]);
                
                // Update existing user with Google info if not already set
                if (!$user->google_id) {
                    $updateData = [
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->avatar,
                        'provider' => 'google'
                    ];
                    
                    Log::info('Updating existing user with Google data', $updateData);
                    
                    $updateResult = $user->update($updateData);
                    Log::info('Existing user update result', ['success' => $updateResult, 'user_id' => $user->id]);
                    
                    if (!$updateResult) {
                        Log::error('Failed to update existing user');
                        return redirect('/login')->with('error', 'Failed to update user account');
                    }
                }
                Log::info('Existing user will be logged in', ['user_id' => $user->id]);
            } else {
                Log::info('No existing user found, creating new user');
                
                // Create new user with role field
                $userData = [
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'provider' => 'google',
                    'email_verified_at' => now(),
                    'role' => 'user',
                ];
                
                Log::info('Creating user with data:', $userData);
                
                try {
                    $user = User::create($userData);
                    
                    if (!$user) {
                        Log::error('User::create returned null');
                        return redirect('/login')->with('error', 'Failed to create user account');
                    }
                    
                    Log::info('New user created successfully', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'name' => $user->name
                    ]);
                    
                } catch (\Illuminate\Database\QueryException $dbError) {
                    Log::error('Database error creating user:', [
                        'error' => $dbError->getMessage(),
                        'code' => $dbError->getCode()
                    ]);
                    return redirect('/login')->with('error', 'Database error: ' . $dbError->getMessage());
                    
                } catch (\Exception $createError) {
                    Log::error('General error creating user:', [
                        'error' => $createError->getMessage(),
                        'line' => $createError->getLine(),
                        'file' => $createError->getFile()
                    ]);
                    return redirect('/login')->with('error', 'Error creating user: ' . $createError->getMessage());
                }

                // Send welcome message if WhatsApp is available
                if ($user->telp) {
                    Log::info('Sending WhatsApp welcome message', ['telp' => $user->telp]);
                    try {
                        $this->whatsappService->sendWelcomeMessage($user->telp, $user->name);
                    } catch (\Exception $whatsappError) {
                        Log::warning('WhatsApp message failed but continuing:', ['error' => $whatsappError->getMessage()]);
                    }
                }
            }

            // Login user
            Log::info('Attempting to login user', ['user_id' => $user->id]);
            
            try {
                Auth::login($user);
                
                Log::info('User logged in successfully', [
                    'user_id' => $user->id,
                    'authenticated' => Auth::check(),
                    'auth_user_id' => Auth::id()
                ]);
                
                if (!Auth::check()) {
                    Log::error('Auth::login succeeded but Auth::check failed');
                    return redirect('/login')->with('error', 'Authentication failed after login');
                }
                
            } catch (\Exception $authError) {
                Log::error('Error during authentication:', [
                    'error' => $authError->getMessage(),
                    'line' => $authError->getLine()
                ]);
                return redirect('/login')->with('error', 'Authentication error: ' . $authError->getMessage());
            }

            Log::info('OAuth flow completed successfully, redirecting to home');
            return redirect('/')->with('success', 'Login dengan Google berhasil! Selamat datang di WIFA Sport Center.');

        } catch (\Laravel\Socialite\Two\InvalidStateException $stateError) {
            Log::error('OAuth state error:', ['error' => $stateError->getMessage()]);
            return redirect('/login')->with('error', 'OAuth session expired. Please try again.');
            
        } catch (\Exception $e) {
            Log::error('Google OAuth Callback Error:', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/login')->with('error', 'Login dengan Google gagal: ' . $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('success', 'Anda telah berhasil logout.');
    }
}
