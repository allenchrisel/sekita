<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'name' => trim(strip_tags((string) $request->input('name', ''))),
            'email' => mb_strtolower(trim((string) $request->input('email', ''))),
            'phone' => preg_replace('/[^0-9+().\s-]/', '', (string) $request->input('phone', '')),
            'account_type' => strtoupper((string) $request->input('account_type', 'CLIENT')),
        ]);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'required_if:account_type,PROVIDER', 'string', 'max:20', 'regex:/^[0-9+().\s-]{7,20}$/'],
            'account_type' => ['required', 'in:CLIENT,PROVIDER'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $role = UserRole::from($request->string('account_type')->toString());
        $user = DB::transaction(function () use ($request, $role): User {
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'password' => Hash::make($request->input('password')),
                'role' => $role,
                'is_verified' => false,
            ]);

            if ($role === UserRole::PROVIDER) {
                $user->providerProfile()->create([
                    'category_id' => Category::query()->value('id'),
                    'province_code' => null,
                    'regency_code' => null,
                    'district_code' => null,
                    'title' => 'Lengkapi profil jasa Anda',
                    'whatsapp_number' => $request->input('phone'),
                ]);
            }

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
