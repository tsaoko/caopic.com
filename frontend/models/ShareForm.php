<?php
namespace frontend\models;

use yii\base\Model;
use common\models\Share;



class ShareForm extends Model
{
    public $title;
    public $desc;
    public $source;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title','desc','source'], 'filter', 'filter' => 'trim'],
            [['title'], 'required'],
            ['title', 'string', 'min' => 2, 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '分享标题',
            'desc' => '描述',
        ];
    }
    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();

        return $user->save() ? $user : null;
    }
}
