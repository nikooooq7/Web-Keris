<?php
/**
 * PageController — halaman Profil Kami dan Kontak.
 * Tampilan: app/views/pages/
 */
class PageController extends Controller
{
    public function about()
    {
        $this->view('pages/about');
    }

    public function contact()
    {
        $this->view('pages/contact');
    }

    /** Menerima pesan dari formulir Kontak */
    public function sendMessage()
    {
        // Jebakan untuk robot spam: kolom "website" disembunyikan, manusia tidak mengisinya
        if (!empty($_POST['website'])) {
            Flash::add('success', 'Terima kasih! Pesan Anda sudah kami terima.');
            redirect('kontak');
        }

        $name = post_str('name', 100);
        $email = post_str('email', 150);
        $phone = post_str('phone', 40);
        $subject = post_str('subject', 150);
        $body = post_str('body', 3000);

        $errors = [];
        if ($name === '') {
            $errors[] = 'Nama wajib diisi.';
        }
        if ($email === '' && $phone === '') {
            $errors[] = 'Isi email atau nomor telepon/WhatsApp agar kami bisa membalas pesan Anda.';
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid.';
        }
        if ($phone !== '' && !preg_match('/^[0-9+\-\s()]{5,30}$/', $phone)) {
            $errors[] = 'Nomor telepon hanya boleh berisi angka, spasi, +, -, dan tanda kurung.';
        }
        if (strlen($body) < 5) {
            $errors[] = 'Pesan wajib diisi.';
        }
        if (time() - (int) ($_SESSION['last_message'] ?? 0) < 30) {
            $errors[] = 'Mohon tunggu sebentar sebelum mengirim pesan lagi.';
        }
        if ($errors) {
            $this->backWithErrors($errors, 'kontak');
        }

        Message::create([
            'name'    => $name,
            'email'   => $email,
            'phone'   => $phone,
            'subject' => $subject,
            'body'    => $body,
        ]);
        $_SESSION['last_message'] = time();
        Flash::add('success', 'Terima kasih! Pesan Anda sudah kami terima dan akan segera kami balas.');
        redirect('kontak');
    }
}
