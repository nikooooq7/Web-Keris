<?php
/**
 * AdminProductController — kelola produk: tambah, ubah, hapus,
 * unggah foto & video, harga, deskripsi.
 * Tampilan: app/views/admin/products/
 */
class AdminProductController extends AdminController
{
    public function index()
    {
        $q = get_str('q', 80);
        $status = get_str('status', 20);
        if (!isset(Product::STATUSES[$status])) {
            $status = '';
        }
        $cat = (int) ($_GET['kategori'] ?? 0);
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $result = Product::paginate(
            ['active' => 0, 'q' => $q, 'status' => $status, 'category_id' => $cat],
            $page,
            15
        );

        $this->adminView('products/index', [
            'active'     => 'produk',
            'pageTitle'  => 'Produk',
            'result'     => $result,
            'categories' => Category::all(),
            'q'          => $q,
            'status'     => $status,
            'cat'        => $cat,
        ]);
    }

    public function create()
    {
        $this->adminView('products/form', [
            'active'     => 'produk',
            'pageTitle'  => 'Tambah Produk',
            'product'    => null,
            'categories' => Category::all(),
            'media'      => ['images' => [], 'videos' => []],
        ]);
    }

    public function store()
    {
        $data = $this->collect();
        $errors = $this->validate($data);
        if ($errors) {
            $this->backWithErrors($errors, 'admin/produk/tambah');
        }

        $id = Product::create($data);
        $mediaErrors = $this->saveUploads($id);

        if ($mediaErrors) {
            Flash::add('success', 'Produk "' . $data['name'] . '" tersimpan, tetapi sebagian file gagal diunggah:');
            foreach ($mediaErrors as $err) {
                Flash::add('warning', $err);
            }
            redirect('admin/produk/' . $id . '/edit');
        }
        Flash::add('success', 'Produk "' . $data['name'] . '" berhasil ditambahkan.');
        redirect('admin/produk');
    }

    public function edit($id)
    {
        $product = $this->findOrBack((int) $id);
        $this->adminView('products/form', [
            'active'     => 'produk',
            'pageTitle'  => 'Ubah Produk',
            'product'    => $product,
            'categories' => Category::all(),
            'media'      => Media::grouped((int) $product['id']),
        ]);
    }

    public function update($id)
    {
        $id = (int) $id;
        $product = $this->findOrBack($id);

        $data = $this->collect();
        $errors = $this->validate($data);
        if ($errors) {
            $this->backWithErrors($errors, 'admin/produk/' . $id . '/edit');
        }

        Product::update($id, $data);

        // Hapus foto/video yang dicentang
        $toDelete = $_POST['delete_media'] ?? [];
        if (is_array($toDelete)) {
            foreach ($toDelete as $mid) {
                Media::deleteForProduct($id, (int) $mid);
            }
        }
        // Foto sampul
        $cover = (int) ($_POST['cover_media'] ?? 0);
        if ($cover > 0) {
            Media::setCover($id, $cover);
        }

        $mediaErrors = $this->saveUploads($id);
        if ($mediaErrors) {
            Flash::add('success', 'Perubahan produk tersimpan, tetapi sebagian file gagal diunggah:');
            foreach ($mediaErrors as $err) {
                Flash::add('warning', $err);
            }
            redirect('admin/produk/' . $id . '/edit');
        }
        Flash::add('success', 'Produk "' . $product['name'] . '" berhasil diperbarui.');
        redirect('admin/produk');
    }

    public function destroy($id)
    {
        $product = $this->findOrBack((int) $id);
        Product::delete((int) $product['id']);
        Flash::add('success', 'Produk "' . $product['name'] . '" beserta foto dan videonya sudah dihapus.');
        redirect('admin/produk');
    }

    /* -------------------------------------------------------------- */

    private function findOrBack(int $id): array
    {
        $product = Product::find($id);
        if (!$product) {
            Flash::add('danger', 'Produk tidak ditemukan.');
            redirect('admin/produk');
        }
        return $product;
    }

    /** Ambil isian form menjadi array siap simpan */
    private function collect(): array
    {
        $status = post_str('status', 20);
        return [
            'name'           => post_str('name', 200),
            'category_id'    => ((int) ($_POST['category_id'] ?? 0)) ?: null,
            'price'          => parse_price($_POST['price'] ?? ''),
            'status'         => isset(Product::STATUSES[$status]) ? $status : 'tersedia',
            'is_featured'    => isset($_POST['is_featured']) ? 1 : 0,
            'is_active'      => isset($_POST['is_active']) ? 1 : 0,
            'origin'         => post_str('origin', 150),
            'era'            => post_str('era', 150),
            'material'       => post_str('material', 150),
            'dimension'      => post_str('dimension', 150),
            'condition_note' => post_str('condition_note', 255),
            'description'    => post_str('description', 20000),
            'video_url'      => post_str('video_url', 255),
        ];
    }

    private function validate(array $d): array
    {
        $errors = [];
        if ($d['name'] === '') {
            $errors[] = 'Nama produk wajib diisi.';
        }
        if ($d['category_id'] !== null && !Category::find((int) $d['category_id'])) {
            $errors[] = 'Kategori yang dipilih tidak ditemukan.';
        }
        if ($d['video_url'] !== '' && youtube_id($d['video_url']) === null) {
            $errors[] = 'Tautan video harus dari YouTube, contoh: https://www.youtube.com/watch?v=xxxxxxxxxxx';
        }
        return $errors;
    }

    /** Simpan foto & video yang diunggah. Mengembalikan daftar pesan error (jika ada). */
    private function saveUploads(int $productId): array
    {
        $errors = [];

        foreach (Upload::multiple($_FILES['photos'] ?? []) as $file) {
            try {
                $path = Upload::image($file, 'products', 1600);
                if ($path) {
                    Media::add($productId, 'image', $path);
                }
            } catch (Throwable $e) {
                $errors[] = $e->getMessage();
            }
        }

        foreach (Upload::multiple($_FILES['videos'] ?? []) as $file) {
            try {
                $path = Upload::video($file, 'products');
                if ($path) {
                    Media::add($productId, 'video', $path);
                }
            } catch (Throwable $e) {
                $errors[] = $e->getMessage();
            }
        }
        return $errors;
    }
}
