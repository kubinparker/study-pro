<?php
    namespace App\Controller\Admin;

    use App\Controller\AppController;

    use Cake\Event\EventInterface;
    use Cake\Http\Exception\NotFoundException;

    class SoXoController extends AppController
    {
        public function beforeFilter(EventInterface $event) 
        {
            $this->checkLogin();
            // call function of parent => load layout admin
            parent::beforeFilter($event);
        }

        public function index($date = null)
        {
            $date = ($date) ? $date : date('Y-m-d');
            $date = date('Y-m-d',strtotime($date));
            $data = $this->{$this->modelName}->render_number($date);

            if (!$data && $date == date('Y-m-d')) {
                $data = $this->{$this->modelName}->save_number();
            }
            
            if($this->request->is(['post', 'put'])){
                $check = 1;
            }
            $this->set('data', [$this->modelName => $data]);
        }
    }