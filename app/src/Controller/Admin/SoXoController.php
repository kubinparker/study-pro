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

        public function getMonth($m = null, $y = null)
        {
            $t = \DateTime::createFromFormat('Y-m', $y.'-'.$m);
            if(!$t){
                $m = date('m');
                $y = date('Y');
            }
            $str = $y.'-'.$m.'-1';
            $end = $y.'-'.$m.'-'. cal_days_in_month(CAL_GREGORIAN, $m, $y);

            $str = date('Y-m-d',strtotime($str));
            $end = date('Y-m-d',strtotime($end));

            $data = $this->{$this->modelName}->get_date_of_time($str, $end);
            dd($data);
            return $m;
        }
    }