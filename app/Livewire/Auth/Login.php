<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $email        = '';
    public string $password     = '';
    public bool   $remember     = true;
    public string $errorMessage = '';

    public function login()
    {
        $this->errorMessage = '';

        $this->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            $user = Auth::user();
            session(['active_role' => $user->role ?? 'ADMIN']);

            return $this->redirectBasedOnRole($user->role);
        }

        $this->errorMessage = 'Email atau kata sandi tidak cocok.';
    }

    public function quickLogin(string $email)
    {
        $user = User::where('email', $email)->first();
        if ($user) {
            Auth::login($user, true);
            session()->regenerate();
            session(['active_role' => $user->role ?? 'ADMIN']);

            return $this->redirectBasedOnRole($user->role);
        }

        $this->errorMessage = 'Akun role tidak ditemukan.';
    }

    private function redirectBasedOnRole(?string $role)
    {
        return match (strtoupper($role ?? 'ADMIN')) {
            'KASIR'    => redirect()->to('/pos'),
            'PRODUKSI' => redirect()->to('/produksi'),
            'KURIR'    => redirect()->to('/antar-jemput'),
            default    => redirect()->to('/'),
        };
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('layouts.guest', ['title' => 'Login — Kee Laundry ERP']);
    }
}
