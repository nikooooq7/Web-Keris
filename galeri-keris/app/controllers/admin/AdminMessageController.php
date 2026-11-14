<?php
/**
 * AdminMessageController — pesan masuk dari formulir Kontak.
 * Tampilan: app/views/admin/messages/
 */
class AdminMessageController extends AdminController
{
    public function index()
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $this->adminView('messages/index', [
            'active'    => 'pesan',
            'pageTitle' => 'Pesan Masuk',
            'result'    => Message::paginate($page, 15),
        ]);
    }

    public function show($id)
    {
        $msg = Message::find((int) $id);
        if (!$msg) {
            Flash::add('danger', 'Pesan tidak ditemukan.');
            redirect('admin/pesan');
        }
        Message::markRead((int) $msg['id']);
        $this->adminView('messages/show', [
            'active'    => 'pesan',
            'pageTitle' => 'Detail Pesan',
            'msg'       => $msg,
        ]);
    }

    public function destroy($id)
    {
        Message::delete((int) $id);
        Flash::add('success', 'Pesan sudah dihapus.');
        redirect('admin/pesan');
    }
}
