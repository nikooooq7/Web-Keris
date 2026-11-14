<?php
/**
 * AdminController (induk) — semua halaman admin mewarisi class ini.
 * Otomatis memaksa login sebelum halaman dibuka.
 */
abstract class AdminController extends Controller
{
    public function __construct()
    {
        Auth::guard();
    }

    /** Tampilkan file dari app/views/admin/ memakai layout admin */
    protected function adminView(string $view, array $data = []): void
    {
        $this->view('admin/' . $view, $data, 'layouts/admin');
    }
}
