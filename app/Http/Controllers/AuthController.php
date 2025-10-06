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
                'telp' => 'required|string|max:15|regex:/^(0|62)\d{9,13}$/',
            ], [
                'telp.regex' => 'Format nomor WhatsApp tidak valid. Gunakan format 08xxx atau 62xxx.'
            ]);

            $uniqueEmail = User::where('email', $request->email)->first();

            if($uniqueEmail){
                return response()->json([
                    'success' => false,
                    'message' => 'Email sudah terdaftar. Silakan gunakan email lain.'
                ], 422);
            }

            // Normalize phone number
            $phoneNumber = $request->telp;
            if (substr($phoneNumber, 0, 1) === '0') {
                $phoneNumber = '62' . substr($phoneNumber, 1);
            }

            // Check if phone number already exists (check both formats)
            $existingPhone = User::where('telp', $phoneNumber)
                ->orWhere('telp', '0' . substr($phoneNumber, 2))
                ->first();

            if ($existingPhone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor WhatsApp sudah terdaftar. Silakan gunakan nomor lain.'
                ], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'user',
                'telp' => $phoneNumber,
            ]);
            
            $credentials = $request->only('email', 'password');
            if($user){
                if (Auth::attempt($credentials)) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Registrasi berhasil! Selamat datang di WIFA Sport Center.',
                        'redirect_url' => '/'
                    ]);
                }else{
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal melakukan login otomatis. Silakan login secara manual.'
                    ], 500);
                }
            }
        } 
        catch (\Exception $e) {
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

            Log::info('OAuth flow completed successfully, checking WhatsApp number');
            
            // Check if user needs to setup WhatsApp number
            if (!$user->telp) {
                Log::info('User needs to setup WhatsApp', ['user_id' => $user->id]);
                return redirect('/whatsapp-setup')->with('info', 'Silakan tambahkan nomor WhatsApp untuk notifikasi booking.');
            }
            
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

    // WhatsApp Setup Methods
    public function whatsappSetup()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // If user already has WhatsApp number, redirect to home
        if (Auth::user()->telp) {
            return redirect('/')->with('info', 'Nomor WhatsApp sudah terdaftar.');
        }

        return view('auth.whatsapp-setup');
    }

    public function whatsappSetupPost(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan login terlebih dahulu.'
                ], 401);
            }

            $user = User::find(Auth::id());

            // If user chooses to skip
            if ($request->has('skip') && $request->skip == 'true') {
                Log::info('User skipped WhatsApp setup', ['user_id' => $user->id]);
                return response()->json([
                    'success' => true,
                    'message' => 'Anda dapat menambahkan nomor WhatsApp nanti dari profil.',
                    'redirect_url' => '/'
                ]);
            }

            // Validate phone number
            $request->validate([
                'telp' => [
                    'required',
                    'string',
                    'regex:/^(0|62)\d{9,13}$/',
                    'max:15'
                ],
            ], [
                'telp.required' => 'Nomor WhatsApp wajib diisi.',
                'telp.regex' => 'Format nomor tidak valid. Gunakan format 08xxx atau 62xxx.',
                'telp.max' => 'Nomor terlalu panjang.'
            ]);

            $phoneNumber = $request->telp;

            // Normalize phone number (convert 08xx to 628xx)
            if (substr($phoneNumber, 0, 1) === '0') {
                $phoneNumber = '62' . substr($phoneNumber, 1);
            }

            // Check if phone number already exists (check both formats)
            $existingUser = User::where('telp', $phoneNumber)
                ->orWhere('telp', '0' . substr($phoneNumber, 2))
                ->where('id', '!=', $user->id)
                ->first();

            if ($existingUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor WhatsApp sudah terdaftar. Silakan gunakan nomor lain.'
                ], 422);
            }

            // Update user's phone number
            $user->telp = $phoneNumber;
            $user->save();

            Log::info('WhatsApp number added successfully', [
                'user_id' => $user->id,
                'telp' => $phoneNumber
            ]);

            // Send welcome message
            try {
                $this->whatsappService->sendWelcomeMessage($phoneNumber, $user->name);
                Log::info('Welcome WhatsApp message sent', ['telp' => $phoneNumber]);
            } catch (\Exception $whatsappError) {
                Log::warning('WhatsApp message failed but continuing', [
                    'error' => $whatsappError->getMessage()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Nomor WhatsApp berhasil ditambahkan! Selamat datang di WIFA Sport Center.',
                'redirect_url' => '/'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('WhatsApp setup error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.'
            ], 500);
        }
    }
}
