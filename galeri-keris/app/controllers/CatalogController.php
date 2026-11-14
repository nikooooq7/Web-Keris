<?php
/**
 * CatalogController — halaman Koleksi (daftar produk) dan Detail Produk.
 * Tampilan: app/views/catalog/
 */
class CatalogController extends Controller
{
    /** Daftar koleksi + pencarian + filter kategori/status */
    public function index()
    {
        $q = get_str('q', 80);
        $catSlug = get_str('kategori', 120);
        $status = get_str('status', 20);
        if (!isset(Product::STATUSES[$status])) {
            $status = '';
        }
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = max(3, min(48, (int) setting('products_per_page', 12)));

        $result = Product::paginate(
            ['active' => 1, 'q' => $q, 'category' => $catSlug, 'status' => $status],
            $page,
            $perPage
        );

        $this->view('catalog/index', [
            'result'     => $result,
            'categories' => Category::withCounts(),
            'q'          => $q,
            'catSlug'    => $catSlug,
            'status'     => $status,
        ]);
    }

    /** Detail satu produk */
    public function show($slug)
    {
        $product = Product::findBySlug($slug);
        if (!$product) {
            Controller::error(404);
            return;
        }
        $id = (int) $product['id'];
        if (!Auth::check()) {
            Product::incrementViews($id);
        }
        $this->view('catalog/show', [
            'product' => $product,
            'media'   => Media::grouped($id),
            'related' => Product::related($product, 4),
        ]);
    }
}
