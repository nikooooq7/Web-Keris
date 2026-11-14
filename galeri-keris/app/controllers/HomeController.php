<?php
/**
 * HomeController — halaman Beranda.
 * Tampilan: app/views/home.php
 */
class HomeController extends Controller
{
    public function index()
    {
        $this->view('home', [
            'featured'   => Product::featured(8),
            'categories' => Category::withCounts(),
        ]);
    }
}
