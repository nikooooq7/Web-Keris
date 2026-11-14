<?php
/**
 * AdminSettingsController — Profil Toko, Teks Beranda, Kontak & Sosial, Tampilan & SEO.
 *
 * Semua formulirnya dibuat otomatis dari daftar isian di app/settings_fields.php,
 * jadi menambah isian baru tidak perlu mengubah file ini.
 */
class AdminSettingsController extends AdminController
{
    public function profile()
    {
        $this->show('profile', 'admin/profil', 'profil');
    }

    public function saveProfile()
    {
        $this->save('profile', 'admin/profil');
    }

    public function home()
    {
        $this->show('home', 'admin/beranda', 'beranda');
    }

    public function saveHome()
    {
        $this->save('home', 'admin/beranda');
    }

    public function contact()
    {
        $this->show('contact', 'admin/kontak', 'kontak');
    }

    public function saveContact()
    {
        $this->save('contact', 'admin/kontak');
    }

    public function site()
    {
        $this->show('site', 'admin/tampilan', 'tampilan');
    }

    public function saveSite()
    {
        $this->save('site', 'admin/tampilan');
    }

    /* -------------------------------------------------------------- */

    private function show(string $groupKey, string $formPath, string $active): void
    {
        $group = SettingsManager::group($groupKey);
        $this->adminView('settings/form', [
            'active'    => $active,
            'pageTitle' => $group['title'],
            'group'     => $group,
            'formPath'  => $formPath,
        ]);
    }

    private function save(string $groupKey, string $formPath): void
    {
        $group = SettingsManager::group($groupKey);
        $errors = SettingsManager::save($group, $_POST, $_FILES);
        if ($errors) {
            $this->backWithErrors($errors, $formPath);
        }
        Flash::add('success', 'Pengaturan "' . $group['title'] . '" berhasil disimpan.');
        redirect($formPath);
    }
}
