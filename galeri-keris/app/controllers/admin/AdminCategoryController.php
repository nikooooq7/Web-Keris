<?php
/**
 * AdminCategoryController — kelola kategori produk.
 * Tampilan: app/views/admin/categories/
 */
class AdminCategoryController extends AdminController
{
    public function index()
    {
        $this->adminView('categories/index', [
            'active'     => 'kategori',
            'pageTitle'  => 'Kategori',
            'categories' => Category::withTotalCounts(),
        ]);
    }

    public function store()
    {
        list($name, $desc, $sort, $errors) = $this->collect();
        if ($errors) {
            $this->backWithErrors($errors, 'admin/kategori');
        }
        Category::create($name, $desc, $sort);
        Flash::add('success', 'Kategori "' . $name . '" berhasil ditambahkan.');
        redirect('admin/kategori');
    }

    public function edit($id)
    {
        $cat = $this->findOrBack((int) $id);
        $this->adminView('categories/edit', [
            'active'    => 'kategori',
            'pageTitle' => 'Ubah Kategori',
            'cat'       => $cat,
        ]);
    }

    public function update($id)
    {
        $id = (int) $id;
        $this->findOrBack($id);
        list($name, $desc, $sort, $errors) = $this->collect();
        if ($errors) {
            $this->backWithErrors($errors, 'admin/kategori/' . $id . '/edit');
        }
        Category::update($id, $name, $desc, $sort);
        Flash::add('success', 'Kategori berhasil diperbarui.');
        redirect('admin/kategori');
    }

    public function destroy($id)
    {
        $cat = $this->findOrBack((int) $id);
        Category::delete((int) $cat['id']);
        Flash::add('success', 'Kategori "' . $cat['name'] . '" dihapus. Produk di dalamnya tetap ada (menjadi tanpa kategori).');
        redirect('admin/kategori');
    }

    private function findOrBack(int $id): array
    {
        $cat = Category::find($id);
        if (!$cat) {
            Flash::add('danger', 'Kategori tidak ditemukan.');
            redirect('admin/kategori');
        }
        return $cat;
    }

    private function collect(): array
    {
        $name = post_str('name', 100);
        $desc = post_str('description', 255);
        $sort = (int) ($_POST['sort_order'] ?? 0);
        $errors = [];
        if ($name === '') {
            $errors[] = 'Nama kategori wajib diisi.';
        }
        return [$name, $desc, $sort, $errors];
    }
}
