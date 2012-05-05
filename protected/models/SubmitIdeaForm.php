<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class SubmitIdeaForm extends CFormModel
{
	public $idCategory;
	public $truthOrDare;
	public $idea;
	public $username;
        public $verifyCode;
        public $anonymous;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('truthOrDare, idea, idCategory, anonymous', 'required'),
			array('verifyCode', 'captcha', 'allowEmpty'=>!extension_loaded('gd')),
                        array('truthOrDare','safe'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'username'=>'Username (check "Anonymous" if you don\'t want your Username to appear near the Truth/Dare - The points will still count)',
			'truthOrDare'=>'Truth or Dare?',
			'idea'=>'Your idea',
			'idCategory'=>'Category',
			'verifyCode'=>'Verification Code',
			'anonymous'=>'Anonymous',
		);
	}
}
?>