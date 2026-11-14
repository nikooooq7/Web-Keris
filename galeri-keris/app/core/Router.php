<?php
/**
 * ROUTER
 * ---------------------------------------------------------------
 * Mencocokkan alamat (URL) dengan Controller yang harus dijalankan.
 * Daftar alamatnya ada di file app/routes.php
 */
class Router
{
    private $routes = [];

    public function get(string $pattern, string $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    public function post(string $pattern, string $handler): void
    {
        $this->add('POST', $pattern, $handler);
    }

    private function add(string $method, string $pattern, string $handler): void
    {
        $regex = preg_replace('#\{id\}#', '([0-9]+)', $pattern);
        $regex = preg_replace('#\{[a-z_]+\}#i', '([^/]+)', $regex);
        $this->routes[] = ['method' => $method, 'regex' => '#^' . $regex . '$#', 'handler' => $handler];
    }

    public function dispatch(string $method, string $path): void
    {
        if ($method === 'HEAD') {
            $method = 'GET';
        }
        if ($method === 'POST') {
            $this->guardPost();
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            if (preg_match($route['regex'], $path, $m)) {
                array_shift($m);
                list($class, $action) = explode('@', $route['handler']);
                $controller = new $class();
                call_user_func_array([$controller, $action], array_map('urldecode', $m));
                return;
            }
        }

        Controller::error(404);
    }

    /** Pemeriksaan keamanan untuk semua form (POST) */
    private function guardPost(): void
    {
        $len = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
        if ($len > 0 && empty($_POST) && empty($_FILES)) {
            Flash::add('danger', 'Ukuran total file yang Anda kirim melebihi batas server (' . upload_limit_text() . '). '
                . 'Kecilkan ukuran foto/video, kirim sedikit demi sedikit, atau untuk video besar gunakan tautan YouTube.');
            redirect_back();
        }
        if (!Csrf::verify()) {
            Flash::add('danger', 'Sesi formulir sudah kedaluwarsa. Silakan coba kirim ulang.');
            redirect_back();
        }
    }
}
