<?php
namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Table;

use Cake\Database\Expression\QueryExpression;
use Cake\Event\Event;
use Cake\Datasource\EntityInterface;
use ArrayObject;
/**
 * Position Behavior.
 *
 * データ並び順を設定
 *
 */
class PositionBehavior extends Behavior 
{
    public $settings;
    public $objectModel;
    /**
     * Defaults
     *
     * @var array
     */
	protected $_defaults = array(
        'field' => 'position',
        'group' => array(),
        'groupMove' => false,
        'order' => 'ASC',
        'recursive' => -1,
	);

    protected $_old_position = 0;
    protected $_old_group_conditions = [];


    public function initialize(array $config): void
    {
        $settings = $config + $this->_defaults;
        $this->settings[$this->_table->getAlias()] = $settings;
    }
    
    /**
     * The display position of data is changed.
     * 並び順を変更する
     *
     * @since 13/02/07 11:37
     * @param  Integer    $id  primary key
     * @param  String    $dir Moving direction
     *                   [top, bottom, up, down]
     * @return bool
     */
    public function movePosition(Table $Model, $id, $dir, $options = array()) 
    {
        $modelName = $Model->getAlias();
        extract($this->settings[$modelName]);

        if (!$this->enablePosition($Model)) {
            return false;
        }
        $conditions = $this->groupConditions($Model, $id);

        $model_name = $modelName;
        $primary_key = $Model->getPrimaryKey();

        if (get_class($Model) !== \Cake\ORM\Table::class) {
            $data = $Model->find()
                ->select([$primary_key, $field])
                ->where([$primary_key => $id])
                ->first();

            $position = $data->{$field};
            if(empty($position)) return false;

            if ($dir === 'top') {
                $expression = new QueryExpression($field.' = '.$field.' + 1');
                
                $Model->updateAll([$expression], array_merge([$field.' < ' => $position], $conditions));
                $Model->updateAll([$field => 1], [$primary_key => $id]);

            } 
            
            else if ($dir === 'bottom') {
                $count = $Model->find()->count();
                $expression = new QueryExpression($field.' = '.$field.' - 1');

                $Model->updateAll([$expression], array_merge([$field.' >' => $position], $conditions));
                $Model->updateAll([$field => $count], [$primary_key => $id]);

            } 
            
            else if ($dir === 'up') {
                if (1 < $position) {
                    $expression = new QueryExpression($field.' = '.$field.' + 1');

                    $Model->updateAll([$expression], array_merge([$field => $position - 1], $conditions));
                    $Model->updateAll([$field => $position - 1], [$primary_key => $id]);
                }

            } 
            
            else if ($dir === 'down') {
                $count = $Model->find()->count();
                if ($position < $count) {
                    $expression = new QueryExpression($field.' = '.$field.' - 1');
                    $Model->updateAll([$expression], array_merge([$field => $position + 1], $conditions));
                    $Model->updateAll([$field => $position + 1], [$primary_key => $id]);
                }
            } 
        }
    }

    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options) 
    {
        $Model = $this->_table;
        $modelName = $Model->getAlias();
        $id = $entity->id;
        //
        extract($this->settings[$modelName]);

        if ($this->enablePosition($Model) && !empty($group) && $this->enableGroupMove($Model)) {
            // 保存前のデータ取得
            $primary_key = $Model->getPrimaryKey(); 
            $current_check = $Model->exists([$primary_key => $id]);
            
            if ($current_check) {
                $data = $Model->get($id);
                $old_check = $Model->exists([$primary_key => $id]);

                if($old_check){
                    $old = $Model->get($id);
                    // グループ変更チェック
                    $_isGroupUpdated = false;
                    foreach ($group as $_col) {
                        if ($data->{$_col} && $data->{$_col} != $old->{$_col}) {
                            $_isGroupUpdated = true;
                            break;
                        }
                    }
                    if ($_isGroupUpdated) {
                        foreach ($group as $_col) {
                            $this->_old_group_conditions[$_col] = $old->{$_col};
                            $this->_old_position = $old->{$field};
                        }
                    }
                }
            }
        }

        return true;
    }
    public function afterSave(Event $event, EntityInterface $entity, ArrayObject $options) 
    {
        $Model = $this->_table;
        $primary_key = $Model->getPrimaryKey();
        extract($this->settings[$Model->getAlias()]);
        $created = $entity->isNew();
        $id = $entity->id;
        
		if ($created) {
            if ($this->enablePosition($Model)) {

                $cond = $this->groupConditions($Model, $id);

                $save = [];
                if (strtoupper($order) === 'DESC') {
                    $save = [$field => $Model->find()->count()];
                    $cond = [$primary_key => $id];
                } else {
                    $save = [new QueryExpression($field.' = '.$field.' + 1')];
                }  
                return $Model->updateAll($save, $cond);
            }
		} else {
            if ($this->enablePosition($Model) && !empty($group) && !empty($this->_old_group_conditions)) {
                
                // 保存前のグループの並び順
                $this->_old_group_conditions[$field.' >'] = $this->_old_position;
                $expression = new QueryExpression($field.' = '.$field.' - 1');

                $Model->updateAll([$expression], $this->_old_group_conditions);
                // 保存後のグループの並び順
                return $this->afterSave($event, $entity, $options);
            }
        }
	}

    public function beforeDelete(Event $event, EntityInterface $entity, $cascade = true) 
    {
        $modelName = $this->_table->getAlias();
        extract($this->settings[$modelName]);
        if ($this->enablePosition($this->_table)) {
            $r = $this->movePosition($this->_table, $entity->id, 'bottom');
        }
		return true;
	}
    /**
     * グループ設定ありの並び順変更の有無
     * @param  Model  $Model [description]
     * @return [type]        [description]
     */
    public function enableGroupMove($Model) 
    {
        extract($this->settings[$Model->getAlias()]);
        return $groupMove;
    }
    /**
     * 並び替えの有無
     * 
     * */
    public function enablePosition($Model) {
        $modelName = $Model->getAlias();
        extract($this->settings[$modelName]);
        return ($field && $Model->hasField($field));
    }

    /**
     * 並び順グループ設定
     *
     * */
    public function groupConditions($Model, $id) {
        $modelName = $Model->getAlias();
        extract($this->settings[$modelName]);
        $cond = [];
        if ($group && $id) {
            $group = (array) $group;
            if($Model->exists([$Model->getPrimaryKey() => $id])){
                $data = $Model->get($id);

                foreach ($group as $column) {
                    if (strpos($column, '.') !== false) {
                        $_model = explode('.', $column);
                        if (count($_model) == 2) {
                            $column = $_model[1];
                        }
                    }

                    if (isset($data->{$column})) {
                        $cond[$modelName . '.' . $column] = $data->{$column};
                    }
                }
            }
        }
        return $cond;
    }

    /**
     * 並び替えを再設定
     * */
    public function resetPosition(Table $Model, array $conditions = array()) {
        extract($this->settings[$Model->modelName]);
        if ($this->enablePosition($Model)) {
            $model_name = $Model->modelName;
            $position_field = $Model->escapeField($field);
            $primary_key = $Model->escapeField($Model->primaryKey);
            $conditions = $this->groupConditions($Model, $id);

            $position = 1;
            $data = $Model->find('all', array('order' => $position_field . ' ' . $order,
                                              'conditions' => $conditions,
                                              'recursive' => $recursive));
            foreach ($data as $key => $value) {
                if (!empty($value[$this->alias][$Model->primaryKey])) {
                    $conditions[$primary_key] = $value[$model_name][$Model->primaryKey];
                    $Model->updateAll(array($position_field => $position), $conditions);
                    ++ $position;
                }
            }
        }
    }

}
