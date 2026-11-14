<?php
/**
 * CONTROLLER (induk)
 * ---------------------------------------------------------------
 * Semua controller mewarisi class ini.
 */
abstract class Controller
{
    /** Tampilkan file dari app/views/ memakai layout publik (default) */
    protected function view(string $view, array $data = [], ?string $layout = 'layouts/public'): void
    {
        render_view($view, $data, $layout);
    }

    /** Tampilkan halaman error (404, dll.) */
    public static function error(int $code): void
    {
        http_response_code($code);
        if (is_file(APP_PATH . '/views/errors/' . $code . '.php')) {
            render_view('errors/' . $code, [], 'layouts/public');
        } else {
            render_view('errors/500', [], null);
        }
    }

    /** Simpan daftar pesan error + isian form lalu kembali ke halaman form */
    protected function backWithErrors(array $errors, string $path): void
    {
        foreach ($errors as $err) {
            Flash::add('danger', $err);
        }
        Flash::keepOld($_POST);
        redirect($path);
    }
}
