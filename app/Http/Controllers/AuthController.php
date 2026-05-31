<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use PragmaRX\Google2FALaravel\Facade as Google2FA;

class AuthController extends Controller
{
    // ============= صفحة تسجيل الدخول =============
    public function showLogin()
    {
        return view('auth.login');
    }

    // ============= تسجيل الدخول بالرقم الوظيفي =============
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|string|max:20',
            'password' => 'required|string',
        ], [
            'employee_id.required' => 'الرقم الوظيفي مطلوب',
            'password.required' => 'كلمة المرور مطلوبة',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $credentials = [
            'employee_id' => $request->input('employee_id'),
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // التحقق من تفعيل الحساب
            if (!$user->is_active) {
                Auth::logout();
                return redirect()->back()->with('error', 'الحساب معطل. تواصل مع مدير النظام.');
            }

            // تحديث بيانات آخر دخول
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            // ========== هل هذا أول دخول؟ يجب تغيير كلمة المرور ==========
            if ($user->isFirstLogin()) {
                // تسجيل خروج مؤقت وتحويل لصفحة تغيير كلمة المرور
                Session::put('force_password_change:user_id', $user->id);
                Session::put('force_password_change:employee_id', $user->employee_id);
                Auth::logout();
                return redirect()->route('password.force-change');
            }

            // تحقق ثنائي (إذا مفعل)
            if ($user->two_factor_secret && $user->two_factor_confirmed_at) {
                Session::put('2fa:user:id', $user->id);
                Auth::logout();
                return redirect()->route('2fa.verify');
            }

            // أول دخول ناجح - حفظ التاريخ
            if (is_null($user->first_login_at)) {
                $user->update(['first_login_at' => now()]);
            }

            return redirect()->intended(route('dashboard'));
        }

        return redirect()->back()
            ->with('error', 'الرقم الوظيفي أو كلمة المرور غير صحيحة')
            ->withInput($request->only('employee_id'));
    }

    // ============= صفحة إجبار تغيير كلمة المرور (أول دخول) =============
    public function showForcePasswordChange()
    {
        if (!Session::has('force_password_change:user_id')) {
            return redirect()->route('login');
        }

        $employeeId = Session::get('force_password_change:employee_id');
        $user = User::where('employee_id', $employeeId)->first();

        if (!$user) {
            Session::forget(['force_password_change:user_id', 'force_password_change:employee_id']);
            return redirect()->route('login');
        }

        return view('auth.force-password-change', ['user' => $user]);
    }

    // ============= حفظ كلمة المرور الجديدة (أول دخول) =============
    public function forcePasswordChange(Request $request)
    {
        $userId = Session::get('force_password_change:user_id');
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login');
        }

        $validator = Validator::make($request->all(), [
            'new_password' => 'required|string|min:8|confirmed|different:old_password_check',
            'new_password_confirmation' => 'required|string|min:8',
        ], [
            'new_password.required' => 'كلمة المرور الجديدة مطلوبة',
            'new_password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'new_password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
            'new_password.different' => 'يجب استخدام كلمة مرور مختلفة عن الافتراضية',
            'new_password_confirmation.required' => 'تأكيد كلمة المرور مطلوب',
        ]);

        // التحقق من أن كلمة المرور الجديدة مختلفة عن الافتراضية
        if (Hash::check($request->input('new_password'), $user->password)) {
            return redirect()->back()
                ->with('error', 'يجب استخدام كلمة مرور مختلفة عن كلمة المرور الافتراضية')
                ->withInput();
        }

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // تحديث كلمة المرور
        $user->update([
            'password' => Hash::make($request->input('new_password')),
            'password_changed' => true,
            'first_login_at' => now(),
        ]);

        // مسح الجلسة وإعادة تسجيل الدخول
        Session::forget(['force_password_change:user_id', 'force_password_change:employee_id']);
        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'تم تغيير كلمة المرور بنجاح! أهلاً بك ' . $user->name);
    }

    // ============= التحقق الثنائي 2FA =============
    public function show2FA()
    {
        if (!Session::has('2fa:user:id')) {
            return redirect()->route('login');
        }

        return view('auth.2fa');
    }

    public function verify2FA(Request $request)
    {
        $request->validate(['otp' => 'required|string|size:6']);

        $userId = Session::get('2fa:user:id');
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login');
        }

        $valid = Google2FA::verifyKey($user->two_factor_secret, $request->otp);

        if ($valid) {
            Auth::login($user);
            Session::forget('2fa:user:id');
            return redirect()->intended(route('dashboard'));
        }

        return redirect()->back()->with('error', 'رمز التحقق غير صحيح');
    }

    public function showSetup2FA()
    {
        $user = Auth::user();
        $secret = Google2FA::generateSecretKey();
        $qrCodeUrl = Google2FA::getQRCodeInline(
            config('app.name'),
            $user->employee_id,
            $secret
        );

        Session::put('2fa:secret', $secret);

        return view('auth.setup-2fa', compact('secret', 'qrCodeUrl'));
    }

    public function confirmSetup2FA(Request $request)
    {
        $request->validate(['otp' => 'required|string|size:6']);

        $user = Auth::user();
        $secret = Session::get('2fa:secret');

        $valid = Google2FA::verifyKey($secret, $request->otp);

        if ($valid) {
            $user->update([
                'two_factor_secret' => $secret,
                'two_factor_confirmed_at' => now(),
            ]);
            Session::forget('2fa:secret');
            return redirect()->route('dashboard')->with('success', 'تم تفعيل المصادقة الثنائية');
        }

        return redirect()->back()->with('error', 'رمز التحقق غير صحيح');
    }

    // ============= تسجيل الخروج =============
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // ============= الملف الشخصي =============
    public function profile()
    {
        return view('auth.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'current_password' => 'nullable|string|required_with:new_password',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
        ];

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->with('error', 'كلمة المرور الحالية غير صحيحة');
            }
            $data['password'] = Hash::make($request->new_password);
            $data['password_changed'] = true;
        }

        $user->update($data);

        return redirect()->back()->with('success', 'تم تحديث الملف الشخصي');
    }
}
