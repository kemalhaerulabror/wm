<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VerificationThrottle;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Notification;
use App\Notifications\VerifyEmailNotification;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class VerificationController extends Controller
{
    public function notice()
    {
        return view('user.auth.verify');
    }

    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->email))) {
            return redirect()->route('login')
                ->with('error', 'Link verifikasi tidak valid');
        }

        if ($user->email_verified_at !== null) {
            return redirect()->route('login')
                ->with('info', 'Email sudah diverifikasi sebelumnya. Silakan login.');
        }

        $user->email_verified_at = now();
        $user->save();

        event(new Verified($user));

        // Tidak otomatis login, redirect ke halaman login dengan pesan sukses
        return redirect()->route('login')
            ->with('success', 'Email berhasil diverifikasi. Silakan login untuk masuk ke akun Anda.');
    }

    public function resend(Request $request)
    {
        $user = Auth::user();

        if ($user->email_verified_at !== null) {
            return redirect()->route('home')
                ->with('info', 'Email Anda sudah terverifikasi');
        }

        // Periksa throttling untuk pengguna yang login
        $canSend = $this->canSendVerificationEmail($user->id, $user->email);
        
        if ($canSend !== true) {
            return back()->with('error', 'Anda baru saja mengirim email verifikasi. Silakan tunggu sebelum mencoba kembali.');
        }

        $this->sendVerificationEmail($user);
        $this->recordEmailSent($user->id, $user->email);

        return back()->with('success', 'Link verifikasi telah dikirim ulang ke email Anda');
    }

    public function resendGuest(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Email tidak ditemukan');
        }

        if ($user->email_verified_at !== null) {
            return redirect()->route('login')
                ->with('success', 'Email sudah terverifikasi. Silakan login.');
        }

        // Periksa throttling
        $canSend = $this->canSendVerificationEmail($user->id, $user->email);
        
        if ($canSend !== true) {
            $nextAvailableTimestamp = VerificationThrottle::where('user_id', $user->id)
                ->where('email', $user->email)
                ->first()
                ->next_available_at
                ->timestamp;
            
            return redirect()->route('login')
                ->with('error', 'Anda baru saja mengirim email verifikasi. Silakan tunggu sebelum mencoba kembali.')
                ->with('next_available', $nextAvailableTimestamp);
        }

        $this->sendVerificationEmail($user);
        $this->recordEmailSent($user->id, $user->email);

        return redirect()->route('login')
            ->with('verification_sent', 'Link verifikasi telah dikirim ulang ke email Anda. Silakan cek inbox atau folder spam Anda.');
    }

    protected function sendVerificationEmail($user)
    {
        // Generate link verifikasi secara manual
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email)
            ]
        );

        // Kirim email verifikasi
        Notification::route('mail', $user->email)
            ->notify(new VerifyEmailNotification($user, $verificationUrl));
    }

    /**
     * Memeriksa apakah pengguna bisa mengirim email verifikasi lagi
     * 
     * @param int $userId
     * @param string $email
     * @return bool|string True jika bisa mengirim, string waktu tunggu jika tidak
     */
    protected function canSendVerificationEmail($userId, $email)
    {
        $throttle = VerificationThrottle::where('user_id', $userId)
            ->where('email', $email)
            ->first();

        if (!$throttle) {
            return true;
        }

        $now = Carbon::now();
        if ($now->lt($throttle->next_available_at)) {
            // Pengguna masih harus menunggu, tetapi kita tidak lagi menampilkan waktu
            // dalam pesan error, hanya mengembalikan string untuk menunjukkan bahwa
            // pengguna tidak bisa mengirim lagi
            return "waktu tunggu";
        }

        return true;
    }

    /**
     * Mencatat email verifikasi yang dikirim dan update waktu tunggu
     * 
     * @param int $userId
     * @param string $email
     * @return void
     */
    protected function recordEmailSent($userId, $email)
    {
        $throttle = VerificationThrottle::where('user_id', $userId)
            ->where('email', $email)
            ->first();

        $now = Carbon::now();
        
        if (!$throttle) {
            // Percobaan pertama - tunggu 1 menit
            VerificationThrottle::create([
                'user_id' => $userId,
                'email' => $email,
                'attempts' => 1,
                'last_sent_at' => $now,
                'next_available_at' => $now->copy()->addMinute(),
            ]);
            return;
        }

        // Tingkatkan jumlah percobaan dan kali lipatkan waktu tunggu
        $attempts = $throttle->attempts + 1;
        $waitMinutes = pow(2, $attempts - 1); // 1, 2, 4, 8, 16, 32, ...
        
        // Batasi maksimal waktu tunggu ke 60 menit
        $waitMinutes = min($waitMinutes, 60);

        $throttle->update([
            'attempts' => $attempts,
            'last_sent_at' => $now,
            'next_available_at' => $now->copy()->addMinutes($waitMinutes),
        ]);
    }
} 