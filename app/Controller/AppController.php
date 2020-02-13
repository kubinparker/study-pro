<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		https://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
    public $components = [
        'Session',
        'Cookie',
        'RequestHandler',
        'Flash'
    ];

    public $uses = ['User'];
    public $helpers = [
        'Common', 
        'Html'
    ];

    public $error_messages = '';    
    public $role = [0 => 'S.Admin', 1 => 'Admin', 10 => 'Member'];

    public function beforeRender()
    {
        $this->set('error_messages', $this->error_messages);
    }

    public function beforeFilter()
    {
        //端末判定
        $this->request->addDetector(
            'mb',
            array(
                'env' => 'HTTP_USER_AGENT',
                'options' => array(
                    '^DoCoMo', 'UP\\.Browser', '^SoftBank', '^Vodafone', 'J-PHONE',
                    'NetFront', 'Symbian'
                )
            )
        );
        $this->request->addDetector(
            'sp',
            array(
                'env' => 'HTTP_USER_AGENT',
                'options' => array(
                    'Android.+Mobile', 'iPhone', 'iPod', 'Windows Phone'
                )
            )
        );
        $this->set('isMobile', $this->request->is('mb'));
        $this->set('isSp', $this->request->is('sp'));
        $this->set('list_role', $this->role);

        if ($this->params['prefix'] === 'admin') {
            $this->theme = 'BackEnd';
            $this->layout = "admin";
        } else {
            //Theme 設定
            $this->theme = 'FrontEnd';

            // 準備
            // $this->_prepare();
        }
    }

    protected function _lists($cond = array(), $options = array())
    {

        $primary_key = $this->{$this->modelName}->primaryKey;
        $this->paginate = array_merge(
            array(
                'order' => $this->modelName . '.' . $primary_key . ' DESC',
                'limit' => 30,
                'paramType' => 'querystring'
            ),
            $options
        );

        try {
            if ($this->paginate['limit'] === null) {
                unset($options['limit'],
                $options['paramType']);
                if ($cond) {
                    $options['conditions'] = $cond;
                }
                $datas = $this->{$this->modelName}->find('all', $options);
            } else {
                $datas = $this->paginate($this->{$this->modelName}, $cond);
            }
            $numrows = $this->{$this->modelName}->getNumRows();
        } catch (NotFoundException $e) {
            if (
                !empty($this->request->query['page'])
                && 1 < $this->request->query['page']
            ) {
                $this->redirect(array('action' => $this->request->action));
            }
        }
        $this->set(compact('datas', 'numrows'));
    }

    /**
     * 追加、編集
     *
     * */
    protected function _edit($id = 0, $option = array())
    {
        $option = array_merge(
            array(
                'saveAll' => false,
                'saveMany' => false,
                'create' => null,
                'callback' => null,
                'redirect' => array('action' => 'index')
            ),
            $option
        );
        extract($option);

        if (
            $this->request->is(array('post', 'put'))
            && $this->request->data //post_max_sizeを越えた場合の対応(空になる)
        ) {
            $this->{$this->modelName}->id = $id;
            $this->request->data[$this->modelName][$this->{$this->modelName}->primaryKey] = $id;

            /**
             * 添付ファイルのバリデーションを行うためここで実行
             */
            $this->{$this->modelName}->set($this->request->data);
            if ($saveMany) {
                if (!$this->{$this->modelName}->validateMany($this->request->data)) {
                    $isValid = false;
                }
            } else {
                $isValid = $this->{$this->modelName}->validates();
            }

            if ($isValid) {
                $id = $this->{$this->modelName}->id;

                $this->{$this->modelName}->create();
                if ($saveAll) {
                    $r = $this->{$this->modelName}->saveAll($this->request->data, $saveAll);
                } elseif ($saveMany) {
                    $r = $this->{$this->modelName}->saveMany($this->request->data, $saveMany);
                } else {
                    $trust = $this->{$this->modelName}->trustList();
                    $r = $this->{$this->modelName}->save($this->request->data, false, $trust);
                }
                if ($r) {
                    if ($callback) {
                        $callback($this->{$this->modelName}->id);
                    }
                    if ($redirect) {
                        $this->redirect($redirect);
                    }
                }
            } else {
                $this->set('data', $this->request->data);
                $this->Flash->set('正しく入力されていない項目があります');
            }
        } else {
            $this->{$this->modelName}->id = $id;
            if ($create) {
                $this->request->data = $create;
            } elseif ($this->{$this->modelName}->exists()) {
                $this->request->data = $this->{$this->modelName}->read(null, $id);
            } else {
                $this->request->data = $this->{$this->modelName}->create();
                if (!array_key_exists($this->{$this->modelName}->primaryKey, $this->request->data[$this->modelName])) {
                    $this->request->data[$this->modelName][$this->{$this->modelName}->primaryKey] = null;
                }
            }
            $this->set('data', $this->request->data);
        }
    }


    public function check_login()
    {
        $uid = $this->Session->read('uid');
        $role = $this->Session->read('role');
        
        if((int)$uid !== 0)
            if((int)$role !== 0)
                $this->redirect('/');
            elseif ((int)$role === 0)
                return true;
        

        $this->redirect('/login/');
    }
}
