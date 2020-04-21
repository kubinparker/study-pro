<?php
    namespace App\Controller\Admin;

    use App\Controller\AppController;
    use Cake\Event\EventInterface;

    class AdminController extends AppController
    {
        public function initialize(): void
        {
            parent::initialize();
            $this->loadModel('Admins');   
        }

        public function index()
        {
            $layout = "plain";
            $view = "login";
            $r = [];
            if ($this->request->is('post') || $this->request->is('put')) {
                $data = $this->request->getData();
                if (!empty($data['username']) && !empty($data['password'])) {
                    $r = $this->Admins
                        ->find()
                        ->where(['username' => $data['username'], 'password' => $data['password']])
                        ->first();
                    if ($r) {
                        $this->session->write(
                            array(
                                'uid' => $r->id,
                                'data' => $r,
                                'role' => $r->role
                            )
                        );
                    }
                }
                if (empty($r)) {
                    $this->Flash->set('アカウント名またはパスワードが違います');
                }
            }
            if ($this->isLogin()) {
                if($this->session->read('role') != 0 || $this->session->read('role') != 100){
                    return $this->redirect('/admin/soxo/');
                }
                $layout = "admin";
                $view = "index";
            }
            $this->set('data', $r);
            $this->viewBuilder()->setLayout($layout);
            $this->render($view);
        }

        public function logout() 
        {
            if ($this->isLogin()) {
                $this->session->delete('uid');
                $this->session->delete('role');
                $this->session->destroy();
            }
            $this->redirect('/admin/');
        }
    }


?>