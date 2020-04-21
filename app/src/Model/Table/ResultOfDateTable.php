<?php 
    namespace App\Model\Table;

    use Cake\Http\Exception\NotFoundException;
    use Cake\Validation\Validator;
    use Cake\ORM\TableRegistry;

    class ResultOfDateTable extends AppTable
    {
        public $blackList = [];

        public $attaches = [];
        public function initialize(array $config): void
        {
            parent::initialize($config);
        }

        public function get_data_of_date($date)
        {
            return $this->find()->where(['date' => $date])->first();;
        }

        public function saved($date, $kq)
        {
            $query = $this->newEntity(['result' => (int)$kq, 'date' => $date]);
            return $this->save($query);
        }
    }
?>