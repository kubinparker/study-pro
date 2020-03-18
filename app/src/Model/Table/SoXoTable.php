<?php
    namespace App\Model\Table;
    
    use Cake\Http\Exception\NotFoundException;
    use Cake\Validation\Validator;
    use Cake\ORM\TableRegistry;

    class SoXoTable extends AppTable
    {
        
        public $blackList = [];

        public $attaches = [];

        public function initialize(array $config): void
        {
            parent::initialize($config);
        }

        // validation
        public function render_number($date)
        {
            return $this->setTable('results')->find()->where(['date' => $date])->first();
        }

        // validation
        public function save_number()
        {
            $data = [];
            while(count($data) < 31) {
                $i = mt_rand(0, 99);
                if(!in_array($i, $data)){
                    $data[] = $i;
                }
            }
            asort($data);
            $results = TableRegistry::getTableLocator()->get('Results');
            $query = $results->newEntity(['result' => json_encode(array_values($data)), 'date' => date('Y-m-d')]);
            return $results->save($query);
        }

    }
    
?>