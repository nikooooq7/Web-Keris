<?php
/**
 * AdminDashboardController — halaman ringkasan di Panel Admin.
 */
class AdminDashboardController extends AdminController
{
    public function index()
    {
        $stats = Product::stats();

        // Daftar "yang perlu dilengkapi" untuk membantu pemula
        $checklist = [
            ['Ganti foto profil toko', Setting::stored('profile_photo') !== null, 'admin/profil'],
            ['Unggah foto latar belakang galeri', Setting::stored('gallery_background') !== null, 'admin/profil'],
            ['Isi nomor WhatsApp', trim((string) setting('whatsapp')) !== '', 'admin/kontak'],
            ['Isi alamat toko', trim((string) setting('address')) !== '', 'admin/kontak'],
            ['Tambahkan produk pertama Anda', $stats['total'] > 0, 'admin/produk/tambah'],
        ];

        $this->adminView('dashboard', [
            'active'    => 'dashboard',
            'pageTitle' => 'Dashboard',
            'stats'     => $stats,
            'checklist' => $checklist,
            'messages'  => Message::latest(5),
            'latest'    => Product::paginate(['active' => 0], 1, 5)['items'],
            'unread'    => Message::unreadCount(),
        ]);
    }
}
