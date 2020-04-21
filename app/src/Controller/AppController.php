<?php
declare(strict_types=0);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Controller\Controller;
use Cake\Http\Exception\NotFoundException;

use Cake\ORM\TableRegistry;
use Cake\ORM\Table;
use Cake\Utility\Inflector;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/4/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    public $session;

    public $ket_qua_net = 'https://ketqua.net/xo-so-truyen-thong.php?ngay=';
    /** 実行される順番 */
    // 1.Controllerのinitialize()
    // 2.ComponentのbeforeFilter()
    // 3.ControllerのbeforeFilter()
    // 4.Componentのstartup()
    // 5.Controllerのaction
    // 6.ComponentのbeforeRender()
    // 7.ControllerのbeforeRender()
    // 8.Componentのshutdown()
    // 9.ControllerのafterFilter()

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->modelName = Inflector::camelize($this->modelClass);
        $this->set('ModelName', $this->modelName);
        $this->set('date', date("Y-m-d"));
        
        // session
        $this->session = $this->getRequest()->getSession();
        
        $this->loadComponent('RequestHandler', [
            'enableBeforeRedirect' => false,
        ]);
        $this->loadComponent('Flash');
        $this->loadComponent('Paginator');
        /*
         * Enable the following component for recommended CakePHP form protection settings.
         * see https://book.cakephp.org/4/en/controllers/components/form-protection.html
         */
        //$this->loadComponent('FormProtection');
        
    }


    public $error_messages = '';

    public function beforeFilter(EventInterface $event)
    {
        
        //端末判定
        $this->request->addDetector(
            'mb',
            [
                'env' => 'HTTP_USER_AGENT',
                'options' => [
                    '^DoCoMo', 'UP\\.Browser', '^SoftBank', '^Vodafone', 'J-PHONE',
                    'NetFront', 'Symbian'
                ]
            ]
        );
        
        $this->request->addDetector(
            'sp',
            [
                'env' => 'HTTP_USER_AGENT',
                'options' => [
                    'Android.+Mobile', 'iPhone', 'iPod', 'Windows Phone'
                ]
            ]
        );
        $this->set('isMobile', $this->request->is('mb'));
        $this->set('isSp', $this->request->is('sp'));

        if ($this->request->getParam('prefix') === 'Admin') {
            /** 
             * Layout 設定
             * your layout file in the path templates/layout/
            */
            $this->viewBuilder()->setLayout('admin');
            /** 
             * Theme 設定
             * All file your themed need input the path plugins/Admin/templates/Admin/..
             * If want use themed, need create $this->addPlugin('PC') in function bootstrap()
             * of class src\Application
            */
            // $this->viewBuilder()->setTheme('Admin');
        } 
        
        else {
            /** 
             * Layout 設定
             * your layout file in the path templates/layout/
            */
            $this->viewBuilder()->setLayout('simple');
            /** 
             * Theme 設定
             * All file your themed need input the path plugins/PC/templates/..
             * If want use themed, need create $this->addPlugin('PC') in function bootstrap()
             * of class src\Application
             * 
             * Config path of themed in file config/app.php
             * App [
             *     ...
             *     paths [
             *         plugins: [
             *              'path/of/plugin1'
             *              'path/of/plugin1'
             *              ...
             *         ]
             *     ]
             * ]
             */
            // $this->viewBuilder()->setTheme('PC');
            // 準備
            $this->_prepare();
        }
    }

    private function _prepare()
    { }

    /**
     * Actionの後、実行される
     * @return [type] [description]
     */
    public function beforeRender(EventInterface $event)
    {
        $this->set('error_messages', $this->error_messages);
    }

    /**
     * 管理画面
     * */

    /**
     * 一覧
     *
     * */
    protected function _lists($cond = array(), $options = array())
    {

        $primary_key = $this->{$this->modelName}->getPrimaryKey();
    
        $this->paginate = array_merge(
            array(
                'order' => [$this->modelName . '.' . $primary_key => 'desc'],
                'limit' => 10,
                'paramType' => 'querystring'
            ),
            $options
        );

        try {
            if ($this->paginate['limit'] === null) {
                $query = $this->{$this->modelName}
                    ->find()
                    ->where($cond)
                    ->order($options['order'])
                    ->all();
            }else{
                $query = $this->paginate(
                    $this->{$this->modelName}
                        ->find()
                        ->where($cond)
                );
            }
            $datas = $query->toArray();
            $numrows = $query->count();
        } 
        
        catch (NotFoundException $e) {
            if (
                !empty($this->request->getQuery('page'))
                && 1 < $this->request->getQuery('page')
            ) {
                $this->redirect([
                    'controller' => $this->modelName,
                    'action' => $this->request->getParam('action')
                ]);
            }
        }
        $this->set(compact('datas', 'numrows'));
    }
    /**
     * ファイル/記事削除
     *
     * */
    protected function _delete($id, $type, $columns = null, $option = array())
    {
        $option = array_merge(
            array('redirect' => null),
            $option
        );
        extract($option);

        if (get_class($this->{$this->modelName}) !== \Cake\ORM\Table::class && in_array($type, array('image', 'file', 'content'))) {
            $data = $this->{$this->modelName}->get($id);
            // images削除
            if ($type === 'image' && isset($this->{$this->modelName}->attaches['images'][$columns])) {
                
                if($this->{$this->modelName}->hasField($columns)){
                    $name = $data->{$columns};
                    $updateImages = $this->{$this->modelName}->updateAll(
                        [$columns => ''],
                        [$this->{$this->modelName}->getPrimaryKey() => $id]
                    );

                    // アップ完了 -> images削除
                    if ($updateImages > 0){
                        $_file = UPLOAD_DIR . $this->modelName . DS . IMAGES_BASE_URL . DS . $name;
                        if (is_file($_file)) {
                            @unlink($_file);
                        }
                        foreach ($this->{$this->modelName}->attaches['images'][$columns]['thumbnails'] as $k_ => $_) {
                            $prefix_ = isset($_['prefix']) && $_['prefix'] != '' ? $_['prefix'] : $k_;
                            $_file = UPLOAD_DIR . $this->modelName . DS . IMAGES_BASE_URL . DS . $prefix_.$name;
                            if (is_file($_file)) {
                                @unlink($_file);
                            }
                        }
                    }
                }
            } 
            //　files削除
            else if ($type === 'file' && isset($this->{$this->modelName}->attaches['files'][$columns])) {
                
                if($this->{$this->modelName}->hasField($columns)){
                    $updateFiles = $this->{$this->modelName}->updateAll(
                        [
                            $columns => '',
                            $columns . '_name' => '',
                            $columns . '_size' => '0',
                        ],
                        [$this->{$this->modelName}->getPrimaryKey() => $id] 
                    );
                    // アップ完了 -> files削除
                    if ($updateFiles > 0){
                        $_file = UPLOAD_DIR . $this->modelName . DS . FILES_BASE_URL . DS . $data->{$columns};
                        if (is_file($_file)) {
                            @unlink($_file);
                        }
                    }
                }
            } 
            // Record削除
            else if ($type === 'content') {

                $image_index = array_keys($this->{$this->modelName}->attaches['images']);
                $file_index = array_keys($this->{$this->modelName}->attaches['files']);

                foreach ($image_index as $idx) {
                    if(!$this->{$this->modelName}->hasField($idx)) continue;

                    $_file = UPLOAD_DIR . $this->modelName . DS . IMAGES_BASE_URL . DS . $data->{$idx}; 
                    if (is_file($_file)) {
                        @unlink($_file);
                    }

                    foreach ($this->{$this->modelName}->attaches['images'][$idx]['thumbnails'] as $k_ => $_) {
                        $prefix_ = isset($_['prefix']) && $_['prefix'] != '' ? $_['prefix'] : $k_;
                        $_file = UPLOAD_DIR . $this->modelName . DS . IMAGES_BASE_URL . DS . $prefix_.$data->{$idx};
                        if (is_file($_file)) {
                            @unlink($_file);
                        }
                    }
                    
                }

                foreach ($file_index as $idx) {
                    if(!$this->{$this->modelName}->hasField($idx)) continue;
                    $_file = UPLOAD_DIR . $this->modelName . DS . FILES_BASE_URL . DS . $data->{$idx};
                    if (is_file($_file)) {
                        @unlink($_file);
                    }
                }

                $this->{$this->modelName}->delete($data);

                $id = 0;
            }
        }
        
        if ($redirect) {
            $this->redirect($redirect);
        }
        if ($redirect !== false) {
            if ($id) {
                $this->redirect(['controller' => $this->modelName, 'action' => 'edit', $id]);
            } else {
                $this->redirect(['controller' => $this->modelName, 'action' => 'index']);
            }
        }else{
            $this->redirect(['controller' => $this->modelName, 'action' => 'index']);
        } 
    }

    /** 
     * 詳細画面
     */
    protected function _detail($id = null)
    {
        $data = $this->{$this->modelName}->get($id);
        $this->set(compact('data'));
    }

    /**
     * 掲載中/下書き トグル
     * */
    protected function _enable($id, $options = array())
    {
        $options = array_merge([
            'redirect' => ['action' => 'index', '#' => 'content-' . $id]
        ], $options);
        extract($options);

        if (get_class($this->{$this->modelName}) !== \Cake\ORM\Table::class) {
            $data = $this->{$this->modelName}->get($id);

            $status = ($data->status == 'publish') ? 'draft' : 'publish';
            $update = $this->{$this->modelName}->updateAll(['status' =>  $status], [$this->{$this->modelName}->getPrimaryKey() => $id]);
            if($update > 0){
                if ($redirect) {
                    $this->redirect($redirect);
                }
            }
        }
    }

    /**
     * 順番並び替え
     * */
    protected function _position($id, $pos, $options = array())
    {
        $options = array_merge([
            'redirect' => ['action' => 'index', '#' => 'content-' . $id]
        ], $options);
        extract($options);

        if (get_class($this->{$this->modelName}) !== \Cake\ORM\Table::class) {
            $this->{$this->modelName}->movePosition($this->{$this->modelName}, $id, $pos);
        }
        if ($redirect) {
            $this->redirect($redirect);
        }
    }

    public function isLogin()
    {
        return $this->session->read('uid');
    }

    public function checkLogin()
    {
        if ( !$this->isLogin() || is_null($this->isLogin()) ) {
            return $this->redirect('/admin');
        }
    }

    /**
     * ファイルダウンロード　ファイル名が文字化けしないバージョン
     *
     * */
    public function file($id = 0, $columns = null)
    {
        // If columns is null then columns will value is key position 0 of $attaches
        if (!$columns) {
            $columns = key($this->{$this->modelName}->attaches['files']);
        }
    
        if (get_class($this->{$this->modelName}) !== \Cake\ORM\Table::class) {
            $data = $this->{$this->modelClass}->get($id);
            if ($data->{$columns}) {
                $file = UPLOAD_DIR . $this->modelName . DS . FILES_BASE_URL . DS . $data->{$columns};
                $name = $data->{$columns . '_name'};

                $content = 'attachment;';
                $content .= 'filename=' . $name . ';';
                $content .= 'filename*=UTF-8\'\'' . rawurlencode($name);

                if (file_exists($file)) {
                    $this->response->header('Content-Disposition', $content);
                    $this->response->file($file);
                    return $this->response;
                }
            }
        }
        throw new NotFoundException();
    }

    /**
     * 追加、編集
     *
     * */
    protected function _edit($id = 0, $option = array())
    {
        $option = array_merge(
            [
                'createMany' => false,
                'saveMany' => false,
                'create' => false,
                'callback' => null,
                'redirect' => ['controller' => $this->modelName, 'action' => 'index']
            ],
            $option
        );
        extract($option);

        // ビューにデータを出しる
        $data = [
            $this->modelName => []
        ];

        // Validateチェック変数
        $isValid = true;

        // $this->request->getData() => post_max_sizeを越えた場合の対応(空になる)
        if ($this->request->is(['post', 'put']) && $this->request->getData())
        {
            $data = $this->request->getData();
            if(!isset($data[$this->modelName])){
                $data = [
                    $this->modelName => $data
                ];
            }
            $this->{$this->modelName}->data = $data;

            $this->{$this->modelName}->{$this->{$this->modelName}->getPrimaryKey()} = $id;
            // is save many
            if ($saveMany){
                /**
                 * $data[modelname] = ['feild1' => 'value1', 'feild2' => 'value2', ...] all value in array need meger in type of column in table
                 * conditions -> $saveMany = ['condition 1' => 'value 1', 'condition 2' => 'value 2', ...]
                 */
                $r = $this->{$this->modelName}->updateAll($data[$this->modelName], $saveMany);                    
            }
            // is save
            else{
                // use trust
                $trust = $this->{$this->modelName}->trustList();
                // 編集
                if((int)$id > 0){
                    $item = $this->{$this->modelName}->get($id);

                    /**
                     * AfterSave functionが呼ばれるかどうかをチェック
                     * true: 呼ばれる
                     * false: 呼ばれない
                     */
                    $check_after_save = false;
                    foreach($trust as $col){
                        if(
                            isset($data[$this->modelName][$col]) && 
                            isset($item->{$col}) &&
                            $data[$this->modelName][$col] != $item->{$col}
                        ) $check_after_save = true;
                    }

                    $query = $this->{$this->modelName}->patchEntity($item, $data[$this->modelName], ['fields' => $trust, 'validate' => 'data']);
                    // validate error
                    if(empty($query->getErrors())){
                        $r = $this->{$this->modelName}->save($query);
                        if ($r) {
                            // AfterSave functionが呼ばれなかった場合
                            if(!$check_after_save){
                                $check_need_update_file = false;
                                foreach($this->{$this->modelName}->attaches as $key => $value) {
                                    if($check_need_update_file) break;
                                    foreach($value as $k => $v){
                                        if(isset($data[$this->modelName][$k]) && $data[$this->modelName][$k]['error'] === UPLOAD_ERR_OK && isset($item->{$k})){
                                            $check_need_update_file = true;
                                            break;
                                        }
                                    }
                                }
                                if($check_need_update_file) {
                                    $this->{$this->modelName}->_uploadAttaches($r);
                                }
                            }
                            
                            if ($callback) {
                                $callback($this->{$this->modelName}->id);
                            }
                            if ($redirect) {
                                $this->redirect($redirect);
                            }
                        }
                    }else{
                        $this->set('data', [$this->modelName => $query]);
                        $this->Flash->set('正しく入力されていない項目があります', ['key'=>'errors']);
                    }
                }
                // 追加
                else {
                    $query = $this->{$this->modelName}->newEntity($data[$this->modelName], ['fields' => $trust, 'validate' => 'data']);  
                    // validate error
                    if(empty($query->getErrors())){
                        $r = $this->{$this->modelName}->save($query);
                        if ($r) {
                            if ($callback) {
                                $callback($this->{$this->modelName}->id);
                            }
                            if ($redirect) {
                                $this->redirect($redirect);
                            }
                        }
                    }else{
                        $this->set('data', [$this->modelName => $query]);
                        $this->Flash->set('正しく入力されていない項目があります', ['key'=>'errors']);
                    }
                }
            }
            
        } else {
            if($create){
                $this->set('data', null);
            } 
            // モデルが存在かどうかチェック
            elseif (get_class($this->{$this->modelName}) !== \Cake\ORM\Table::class) {
                $data[$this->modelName] = $this->{$this->modelName}->findById($id)->first();
            }

            else {
                $data[$this->modelName][$this->{$this->modelName}->getPrimaryKey()] = null;
            }
            
            $this->set('data', $this->{$this->modelName}->converts_data_return($data));
        }
    }

    // 残っている

    protected function getCondition($query, $options = array())
    {
        $compare_map = array('>', '<', '<=', '>=');
        if (isset($options['method']) && $options['method'] === 'post') {
            $data = $this->request->data;
        } else {
            $data = $this->request->query;
        }

        $cond = array();
        foreach ($query as $colmun => $q) {
            if (!is_array($q)) {
                $syntax = $q;
                $columns = $this->modelName . '.' . $colmun;
            } else {
                /**
                 * $syntax
                 * $columns
                 * */
                extract(array_merge(
                    array(
                        'syntax' => '=',
                        'columns' => $this->modelName . '.' . $colmun,
                    ),
                    $q
                ));
            }

            $value = null;
            if (isset($data[$colmun]) && !is_array($data[$colmun]) && 0 < strlen($data[$colmun])) {
                $value = $data[$colmun];
            }
            if ($value) {
                if ($syntax === '=') {
                    $cond[$columns] = $value;
                } else if (in_array($syntax, $compare_map)) {
                    $cond[$colmun . ' ' . $syntax] = $value;
                }
                if (strtoupper($syntax) === 'LIKE') {
                    $cond[] = $this->condLike($columns, $value);
                }
            }
            unset($syntax, $columns);
        }
        return $cond;
    }

    protected function condLike($columns, $value)
    {
        return 'CONCAT_WS(" ",' . implode(',', (array) $columns) . ' ) LIKE "%' . Sanitize::escape($value) . '%"';
    }

    protected function getNumberList($start, $end, $step = 1)
    {
        $list = array();
        foreach (range($start, $end, $step) as $_) {
            $list[$_] = $_;
        }
        return $list;
    }

    protected function setRest($results = array())
    {
        $results = array_merge(
            array(
                'code' => 404,
                'name' => null,
                'message' => null
            ),
            $results
        );
        extract($results);
        if ($code == 404 && !$name) {
            $name = 'Not Found';
        }
        if ($code == 401 && !$name) {
            $name = 'Authorization Required';
        }
        if ($code == 200 && !$name) {
            $name = 'Success';
        }
        if (!$message) {
            $message = $name;
        }
        $this->set(compact('code', 'name', 'message', 'data'));
        $this->set('_serialize', array('code', 'name', 'message', 'data'));
    }

    protected function execShell($command, $async = true)
    {
        $results = array();
        $async = ($async) ? ' > /dev/null &' : '';
        chdir(APP . 'Console');
        exec('php cake.php ' . $command . $async, $output, $rvar);
        if (!$async) {
            $results['command'] = 'php cake.php ' . $command . $async;
            $results['output'] = $output;
            $results['status'] = $rvar;
            return $results;
        }
    }

    protected function _setView($lists)
    {
        $this->set(array_keys($lists), $lists);
    }

    protected function isRole($role_key)
    {

        $role = $this->session->read('role');

        if (intval($role) === 0) {
            $res = 'develop';
        } elseif ($role < 10) {
            $res = 'admin';
        }
        /** 必要に応じて追加 */
        // elseif ($role < 20) {
        //     $res = 'manager';
        // } else {
        //     $res = 'staff';
        // }

        if (in_array($res, (array) $role_key)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * パスワードの生成
     * @param  [type] $len            文字数
     * @param  [type] $password_chars [description]
     * @return [type]                 [description]
     */
    public function generate_password($len, $password_chars = null)
    {
        //$no_password_cars = 'I l 0 O 1';
        if (!$password_chars) {
            $password_chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';
        }
        $password_chars_count = strlen($password_chars);

        $data = false;
        if (function_exists('openssl_random_pseudo_bytes')) {
            $data = openssl_random_pseudo_bytes($len);
        } elseif (function_exists('mcrypt_create_iv')) {
            $data = mcrypt_create_iv($len, MCRYPT_DEV_URANDOM);
        }

        $password = '';
        if ($data) {
            for ($n = 0; $n < $len; $n++) {
                $password .= substr($password_chars, ord(substr($data, $n, 1)) % $password_chars_count, 1);
            }
        } else {
            $password = array_reduce(range(1, $len), function ($p) use ($password_chars) {
                $_c = str_shuffle($password_chars);
                return $p . $_c[0];
            });
        }

        return $password;
    }

    protected function getBackUrl($key, $isArray = true)
    {
        $back_query = '';
        if ($this->session->check('BackurlArg.' . $key)) {
            $back_query = $this->session->read('BackurlArg.' . $key);
        }

        if (!$isArray) {
            if (!empty($back_query)) {
                $_back_query = $back_query;
                $back_query = '';
                $prefix = '?';
                foreach ($_back_query as $k => $q) {
                    $back_query .= $prefix . $k . "=" . $q;
                    $prefix = '&';
                }
            } else {
                $back_query = '';
            }
        }

        return $back_query;
    }
    protected function setBackUrl($key, $query)
    {
        $this->session->write('BackurlArg.' . $key, $query);
        return $query;
    }
    protected function clearBackUrl($key)
    {
        $this->session->delete('BackurlArg.' . $key);
    }

    // 暗号化
    public function encrypt($str)
    {
        return openssl_encrypt($str, SECRET_METHOD, SECRET_KEY);
    }

    // 復号化
    public function decrypt($str)
    {
        return openssl_decrypt($str, SECRET_METHOD, SECRET_KEY);
    }
}
