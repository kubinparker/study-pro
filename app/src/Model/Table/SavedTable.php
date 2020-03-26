<?php
    namespace App\Model\Table;
    
    use Cake\Http\Exception\NotFoundException;
    use Cake\Validation\Validator;
    use Cake\ORM\TableRegistry;

    class SavedTable extends AppTable
    {
        
        public $blackList = [];

        public $attaches = [];

        public function initialize(array $config): void
        {
            parent::initialize($config);
        }

        public function get_data_by_uid_and_date($user_id, $result_id){
            return $this->find()->where(['user_id' => $user_id, 'result_id' => $result_id])->first();;
        }

        public function saved($user_id, $result_id, $date){
            $exits = $this->get_data_by_uid_and_date($user_id, $result_id);
            if($exits){
                $query = $this->patchEntity($exits, ['status' => 1]);
                return $this->save($query);
            }else{
                $query = $this->newEntity(['user_id' => $user_id, 'result_id'=> $result_id, 'date' => $date]);
                //// CONTINUE ////
                return $this->save($query);
                
            }
            
        }
    }
    
?>