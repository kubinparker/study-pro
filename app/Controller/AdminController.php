<?php
class AdminController extends AppController {
    public $uses = array('User');

    public function beforeFilter() {
        $this->theme = 'BackEnd';
    }

	public function index() {
        $this->layout = 'empty';
        $view = "login";
        $r = [];
        if ($this->request->is('post') || $this->request->is('put')) {
            $data = $this->data;
            if (!empty($data['username']) && !empty($data['password'])) {
                $r = $this->User
                    ->find('first', [
                        'conditions' => [
                            'username' => $data['username'],
                            'password' => $this->User->hash($data['password'])
                        ]
                    ]);
                if ($r) {
                    $this->Session
                    ->write(['uid' => $r['User']['id'],
                        'data' => $r['User'],
                        'role' => $r['User']['role']
                    ]);
                }
            }
            if (empty($r)) {
                $this->Flash->set('アカウント名またはパスワードが違います');
            }
        }
        if (0 < $this->Session->read('uid')) {
            $this->layout = "admin";
            $view = "index";
        }
        $this->render($view);
	}

    public function logout() {
        if (0 < $this->Session->read('uid')) {
            $this->Session->delete('uid');
            $this->Session->delete('role');
            $this->Session->destroy();
        }
        $this->redirect('/admin/');
    }
    
}
