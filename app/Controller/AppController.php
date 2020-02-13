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

        if ($this->params['prefix'] === 'admin') {
            $this->theme = 'BackEnd';
        } else {
            //Theme 設定
            $this->theme = 'FrontEnd';

            // 準備
            // $this->_prepare();
        }
    }
}
