<?php

namespace common\components;

use Yii;
use yii\db\ActiveRecord as BaseActiveRecord;
use yii\web\ServerErrorHttpException;
use yii\base\Behavior;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class ActiveRecord extends BaseActiveRecord
{
    // SELECT FOR UPDATE 锁定
    public function lockForUpdate()
    {
        if ($this->getDb()->getTransaction() === null)
            throw new Exception('Running transaction is required');

        $pk = ArrayHelper::getValue(self::primaryKey(), 0);
        $this->getDb()->createCommand('SELECT 1 FROM `' . $this->tableName() . '` WHERE ' . $pk . ' = :pk FOR UPDATE', [
            ':pk' => $this->getPrimaryKey(),
        ])->execute();
    }

    /**
    * 抛出错误的保存方法
    */
    public function save($runValidation=true,$attributes=null, $throwException = true)
    {
        if($throwException)
        {
            if(!parent::save($runValidation, $attributes))
            {
                $ers = '';
                foreach($this->errors as $val)
                {
                    $ers[] = implode('', $val);
                }
                throw new \yii\base\UserException(implode('', $ers));
            }
            else
                return true;
        }
        else
            return parent::save($runValidation, $attributes);
    }

    /**
     * 后台操作日志记录
     * @param  boolean $newrecord 是否新增记录
     * @param  string  $attribute 名称字段
     * @param  string  $action    自定义操作
     * @param  array   $except    排除字段
     * @return boolean            总是成功
     */
    public function log($newrecord =true, $attribute = 'name', $action='', $except = [])
    {
        $except = array_combine($except, $except);
        $attributes = '';
        $changedAttributes = array_diff_key(array_intersect_key($this->oldAttributes, $this->dirtyAttributes), $except);
        if($changedAttributes) $attributes .= json_encode($changedAttributes, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        if($this->dirtyAttributes) $attributes .= "\n修改为\n".json_encode(array_diff_key($this->dirtyAttributes, $except), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

        if(!$this->hasErrors()){
            if(!$action){
                if($newrecord){
                    Yii::info("录入{$this->modelName}[{$this->id}] {$this->$attribute}", 'admin');
                }
                else{
                    Yii::info("修改{$this->modelName}[{$this->id}] {$this->$attribute} \n".$attributes, 'admin');
                }
            }
            else{
                if($newrecord){
                    Yii::info("{$action}{$this->modelName}[{$this->id}] {$this->$attribute}", 'admin');
                }
                else{
                    Yii::info("{$action}{$this->modelName}[{$this->id}] {$this->$attribute} \n".$attributes, 'admin');
                }
            }
        }
        return true;
    }
}
