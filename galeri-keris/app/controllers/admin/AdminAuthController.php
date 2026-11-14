<?php
/**
 * AdminAuthController — halaman login & logout admin.
 * (Tidak memakai AdminController karena halaman login tidak boleh meminta login.)
 */
class AdminAuthController extends Controller
{
    public function loginForm()
    {
        if (Auth::check()) {
            redirect('admin');
        }
        $this->view('admin/login', [], null);
    }

    public function login()
    {
        $locked = Auth::lockedSeconds();
        if ($locked > 0) {
            Flash::add('danger', 'Terlalu banyak percobaan gagal. Silakan coba lagi dalam ' . (int) ceil($locked / 60) . ' menit.');
            redirect('admin/login');
        }

        $username = post_str('username', 60);
        $password = (string) ($_POST['password'] ?? '');

        if ($username !== '' && Auth::attempt($username, $password)) {
            Flash::add('success', 'Selamat datang kembali!');
            redirect('admin');
        }

        Flash::add('danger', 'Username atau password salah.');
        Flash::keepOld(['username' => $username]);
        redirect('admin/login');
    }

    public function logout()
    {
        Auth::logout();
        Flash::add('info', 'Anda sudah keluar dari Panel Admin.');
        redirect('admin/login');
    }
}
