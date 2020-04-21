<?php
    namespace App\Controller\Admin;

    use App\Controller\AppController;

    use Cake\Event\EventInterface;
    use Cake\Http\Exception\NotFoundException;

    class SoXoController extends AppController
    {
        public function beforeFilter(EventInterface $event) 
        {
            // call function of parent => load layout admin
            parent::beforeFilter($event);
            return $this->checkLogin();
        }

        public function index($date = null)
        {
            $this->loadModel('Saved');
            $this->loadModel('ResultOfDate');
            $uid = $this->session->read('uid');
            $date = ($date) && \DateTime::createFromFormat('Y-m-d', $date) ? $date : date('Y-m-d');
            $date = date('Y-m-d',strtotime($date));
            // kiểm tra ngày $date có data đã được lưu không
            $data = $this->{$this->modelName}->render_number($date);
            // nếu không có data và ngày là ngày hiện tại thì lưu dãy số mới
            if (!$data && $date == date('Y-m-d')) {
                $data = $this->{$this->modelName}->save_number();
            }
            /**
             * nếu data vẫn bằng rỗng -> ngày quá khứ và không đánh -> check_button = false
             * ngược lại kiểm tra xem ngày đó đã đăng ký chưa -> exists / notexists
             * -> exists => check_button = true
             * -> notexists => check_button = false
             */
            /**
             * check ngày $date user có đăng kí hay không
             * 0: chưa đánh + ngày hiện tại     => nút đăng kí
             * 1: đã đánh                       => thông báo đã đánh
             * 2: chưa đánh ngày trong quá khứ  => không hiển thị gì 
             * */ 
            $check_button = 2;
            if($data){
                // kiểm tra xem đã đánh chưa vs user id đang đăng nhập
                $saved = $this->Saved->get_data_by_uid_and_date($uid, $data->id);
                // nếu đánh rồi
                if($saved){
                    $check_button = 1;
                }else{
                    // chưa đánh và là ngày hiện tại 
                    if($date == date('Y-m-d')){
                        $check_button = 0;
                    }
                }
            }

            /**
             * kiểm tra xem trong db có kết quả đúng ngày $date hay không
             * nếu không
             * --- sử dụng html dom để lấy kết quả của ngày $date trên trang ketqua.net
             * có rồi thì lấy kết quả ra
             * -> tránh request đến link ngoại nhiều lần
             */

            $resultOfDate = $this->ResultOfDate->get_data_of_date($date);
            /**
             * 
             * CONTINUE
             * 
             * 
             * 
             */
            if(empty($resultOfDate)){
                $html = file_get_html($this->ket_qua_net.date('d-m-Y',strtotime($date)));
            
                
                $result_date = $html->find('#result_date', 0)->plaintext;
    
                $pattern = '/[^0-9]+/';
    
                $preg_replace = preg_replace($pattern, '', $result_date);
                $curr_date = \DateTime::createFromFormat('dmY', $preg_replace);
    
                if($curr_date->format('Y-m-d') == $date){
                    $kq = $html->find('#rs_0_0', 0)->plaintext;
                    if($kq != '' || !empty($kq)){
                        $this->ResultOfDate->saved($date, $kq);
                    }
                }
            }

            


            if($this->request->is(['post', 'put']) && $data && $date == date('Y-m-d')){
                // nếu member bấm đăng ký (đã đánh rồi)
                // -> lưu id member và id ngày vào bảng đăng kí "saved"
                /** 
                 * bảng đăng kí có 5 trường 
                 * id
                 * id_user
                 * date
                 * status
                 * result_id
                 */
                
                $saved = $this->Saved->saved($uid, $data->id, $date);
                $check_button = $saved ? 1 : 0;
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