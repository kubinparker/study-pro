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
            $this->loadModel('Saved');
            $uid = $this->session->read('uid');
            $date = ($date) && \DateTime::createFromFormat('Y-m-d', $date) ? $date : date('Y-m-d');
            $date = date('Y-m-d',strtotime($date));
            $data = $this->{$this->modelName}->render_number($date);

            if (!$data && $date == date('Y-m-d')) {
                $data = $this->{$this->modelName}->save_number();
            }
            /**
             * nếu data vẫn bằng rỗng -> ngày quá khứ và không đánh -> check_button = false
             * ngược lại kiểm tra xem ngày đó đã đăng ký chưa -> exists / notexists
             * -> exists => check_button = true
             * -> notexists => check_button = false
             */
            $check_button = false;
            if($data){
                $saved = $this->Saved->get_data_by_uid_and_date($uid, $data->id);
                $check_button = $saved ? true : $check_button;
            }
            if($this->request->is(['post', 'put']) && $data){
                // nếu member bấm đăng ký (đã đánh rồi)
                // -> lưu id member và id ngày vào bảng đăng kí
                /** 
                 * bảng đăng kí có 5 trường 
                 * id
                 * id_user
                 * date
                 * status
                 * result_id
                 */
                
                $saved = $this->Saved->saved($uid, $data->id, $date);
                $check_button = $saved ? true : false;
            }
            $this->set('check_button', $check_button);
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