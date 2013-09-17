<?php

/**
 * This is the model class for table "mac_user".
 *
 * The followings are the available columns in table 'mac_user':
 * @property integer $u_id
 * @property string $u_qid
 * @property string $u_name
 * @property integer $u_group
 * @property string $u_password
 * @property string $u_qq
 * @property string $u_email
 * @property string $u_phone
 * @property integer $u_status
 * @property string $u_question
 * @property string $u_answer
 * @property integer $u_points
 * @property string $u_regtime
 * @property string $u_logintime
 * @property integer $u_loginnum
 * @property integer $u_tj
 * @property string $u_ip
 * @property string $u_random
 * @property string $u_fav
 * @property string $u_plays
 * @property integer $u_flag
 * @property string $u_start
 * @property string $u_end
 */
class WebUser extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return WebUser the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'mac_user';
	}
	
    public function authUser($username,$pwd){
	    $record=WebUser::model()->find('(LOWER(u_name)=?) and u_status=?',array(strtolower($username),Constants::USER_APPROVAL));
        if($record===null){
            return false;
        }else if($record->u_password == md5($pwd)){
            return array('id'=>$record->u_id,
                         'name'=>$record->u_name,
                         'qid'=>$record->u_qid,
                         'group'=>$record->u_group
                         );
        } else {           
            return false;
        }       
	}
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('u_group, u_status, u_points, u_loginnum, u_tj, u_flag', 'numerical', 'integerOnly'=>true),
			array('u_qid, u_name, u_password, u_email, u_ip', 'length', 'max'=>32),
			array('u_qq, u_phone', 'length', 'max'=>16),
			array('u_question, u_answer', 'length', 'max'=>255),
			array('u_random, u_start, u_end', 'length', 'max'=>64),
			array('u_regtime, u_logintime, u_fav, u_plays', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('u_id, u_qid, u_name, u_group, u_password, u_qq, u_email, u_phone, u_status, u_question, u_answer, u_points, u_regtime, u_logintime, u_loginnum, u_tj, u_ip, u_random, u_fav, u_plays, u_flag, u_start, u_end', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'u_id' => 'U',
			'u_qid' => 'U Qid',
			'u_name' => 'U Name',
			'u_group' => 'U Group',
			'u_password' => 'U Password',
			'u_qq' => 'U Qq',
			'u_email' => 'U Email',
			'u_phone' => 'U Phone',
			'u_status' => 'U Status',
			'u_question' => 'U Question',
			'u_answer' => 'U Answer',
			'u_points' => 'U Points',
			'u_regtime' => 'U Regtime',
			'u_logintime' => 'U Logintime',
			'u_loginnum' => 'U Loginnum',
			'u_tj' => 'U Tj',
			'u_ip' => 'U Ip',
			'u_random' => 'U Random',
			'u_fav' => 'U Fav',
			'u_plays' => 'U Plays',
			'u_flag' => 'U Flag',
			'u_start' => 'U Start',
			'u_end' => 'U End',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('u_id',$this->u_id);
		$criteria->compare('u_qid',$this->u_qid,true);
		$criteria->compare('u_name',$this->u_name,true);
		$criteria->compare('u_group',$this->u_group);
		$criteria->compare('u_password',$this->u_password,true);
		$criteria->compare('u_qq',$this->u_qq,true);
		$criteria->compare('u_email',$this->u_email,true);
		$criteria->compare('u_phone',$this->u_phone,true);
		$criteria->compare('u_status',$this->u_status);
		$criteria->compare('u_question',$this->u_question,true);
		$criteria->compare('u_answer',$this->u_answer,true);
		$criteria->compare('u_points',$this->u_points);
		$criteria->compare('u_regtime',$this->u_regtime,true);
		$criteria->compare('u_logintime',$this->u_logintime,true);
		$criteria->compare('u_loginnum',$this->u_loginnum);
		$criteria->compare('u_tj',$this->u_tj);
		$criteria->compare('u_ip',$this->u_ip,true);
		$criteria->compare('u_random',$this->u_random,true);
		$criteria->compare('u_fav',$this->u_fav,true);
		$criteria->compare('u_plays',$this->u_plays,true);
		$criteria->compare('u_flag',$this->u_flag);
		$criteria->compare('u_start',$this->u_start,true);
		$criteria->compare('u_end',$this->u_end,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}