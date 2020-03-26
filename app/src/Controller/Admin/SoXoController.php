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
            $date = ($date) && \DateTime::createFromFormat('Y-m-d', $date) ? $date : date('Y-m-d');
            $date = date('Y-m-d',strtotime($date));
            $data = $this->{$this->modelName}->render_number($date);

            if (!$data && $date == date('Y-m-d')) {
                $data = $this->{$this->modelName}->save_number();
            }
            
            if($this->request->is(['post', 'put'])){
                $check = 1;
            }
            $this->set('date', $date);
            $this->set('data', [$this->modelName => $data]);
        }

        public function getMonth($start = null, $end = null)
        {
            if(\DateTime::createFromFormat('Y-m-d', $start) && \DateTime::createFromFormat('Y-m-d', $end)){
                $data = $this->{$this->modelName}->get_data_of_range_date(date('Y-m-d',strtotime($start)), date('Y-m-d',strtotime($end)));
                echo json_encode($data);
            }else{
                echo json_encode([]);
            }
            exit();
        }
    }