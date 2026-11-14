<?php
/**
 * AdminAccountController — ganti username & password admin.
 * Tampilan: app/views/admin/account/
 */
class AdminAccountController extends AdminController
{
    public function form()
    {
        $this->adminView('account/form', [
            'active'    => 'akun',
            'pageTitle' => 'Akun Admin',
            'user'      => Auth::user(),
        ]);
    }

    public function update()
    {
        $user = Auth::user();
        $current = (string) ($_POST['current_password'] ?? '');
        $username = post_str('username', 40);
        $display = post_str('display_name', 100);
        $new = (string) ($_POST['new_password'] ?? '');
        $confirm = (string) ($_POST['new_password_confirm'] ?? '');

        $errors = [];
        if (!password_verify($current, $user['password_hash'])) {
            $errors[] = 'Password saat ini salah.';
        }
        if (!preg_match('/^[A-Za-z0-9_.-]{3,40}$/', $username)) {
            $errors[] = 'Username 3–40 karakter, hanya huruf, angka, titik, minus, dan garis bawah.';
        } else {
            $other = User::findByUsername($username);
            if ($other && (int) $other['id'] !== (int) $user['id']) {
                $errors[] = 'Username tersebut sudah dipakai.';
            }
        }
        if ($new !== '') {
            if (strlen($new) < 8) {
                $errors[] = 'Password baru minimal 8 karakter.';
            }
            if ($new !== $confirm) {
                $errors[] = 'Konfirmasi password baru tidak sama.';
            }
        }
        if ($errors) {
            $this->backWithErrors($errors, 'admin/akun');
        }

        User::updateProfile((int) $user['id'], $username, $display !== '' ? $display : $username);
        if ($new !== '') {
            User::setPassword((int) $user['id'], $new);
            session_regenerate_id(true);
        }
        Flash::add('success', 'Akun admin berhasil diperbarui.');
        redirect('admin/akun');
    }
}
