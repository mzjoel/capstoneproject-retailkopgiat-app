<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Analytics\Models\CustomerProfile; 
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register/Register');
    }

    /**
     * Validation Session Preferences
     */
    
    public function storeRegisterData(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $request->session()->put('register_data', $validated);
        return redirect()->route('register.preferences');
    }

    public function showPreferences(): Response|RedirectResponse
    {
        if(!session()->has('register_data')){
            return redirect()->route('register');
        }
        return Inertia::render('Auth/Register/Preferences');
    }

    public function storePreferences(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tastes' => 'required|array|min:1',
        ]);

        $data = $request->session()->get('register_data');
        $data['tastes'] = $validated['tastes'];
        $request->session()->put('register_data', $data);
        return redirect()->route('register.preferences.level');
    }

    public function showPreferencesLevel(): Response|RedirectResponse
    {
        if(!session()->has('register_data')){
            return redirect()->route('register');
        }
        $data = session()->get('register_data');
        return Inertia::render('Auth/Register/PreferencesLevel', [
            'tastes' => $data['tastes'] ?? []
        ]);
    } 
    

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'level' => 'required|array|min:1',
        ]);
        $data = $request->session()->get('register_data');

        if(!$data){
            return redirect()->route('register');
        }

        $preferences = [
            'tastes' => $data['tastes'] ?? [],
            'levels' => $validated['level'] ?? [],
        ];

        $user = DB::transaction(function () use ($data, $preferences){
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => 2,
            ]);

            CustomerProfile::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'preferences' => $preferences,
            ]);

            return $user;
        });


        $request->session()->forget('register_data');
        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
