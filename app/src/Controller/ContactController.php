<?php
namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\Utility\Inflector;

use Cake\Mailer\Mailer;

class ContactController extends AppController
{
    public function beforeFilter(EventInterface $event) 
    {
        $this->modelName = Inflector::camelize($this->modelClass);
        $this->set('ModelName', $this->modelName);
        parent::beforeFilter($event);
        
    }

    public function index()
    {
        if ($this->request->is('post')) {
            $data = $this->request->getData();
        
            try {
                // 管理者へメール
                $email = new Mailer('contact_local');
                $email->setViewVars(['_' => $data]);             
                $r = $email->deliver();
                unset($email);

                // ユーザーへメール
                if (!empty($data['email'])) {

                    // 管理者へメール
                    $email = new Mailer('contact_local');
                    $email->setSubject('Test class send mail of Cakephp 4.x to User');

                    $email->setTo($data['email']);

                    $email->setViewVars(['_' => $data]);
                    $email->viewBuilder()->setTemplate('contact');
                    
                    $r = $email->deliver();
                    unset($email);
                }
                if ($r) {
                    $this->render('complete');
                } else {
                    throw new Exception("Error Processing Contact", 1);
                }
            } catch (Exception $e) {
                throw new Exception('メール送信失敗'.$e);
                exit;
            }
        }
        $this->set('data', [$this->modelName => []]);
    }
}

?>