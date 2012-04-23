<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EWebUser
 *
 * @author Eric
 */
class EWebUser extends CWebUser{
 
    public $loginUrl=array('/user/login');
    public $defaultReturnUrl; 
    protected $_model;
    protected $level;             
    
    public function getReturnUrl($defaultUrl=null)        
    {                
        if($defaultUrl===null)                         
            $defaultUrl=$this->defaultReturnUrl;                
        return parent::getReturnUrl($defaultUrl);        
    }
 
    function isAdmin(){
        $user = $this->loadUser();
        if ($user)
           return $user->username=='admin';
        return false;
    }
 
    function getLevel(){
        if($this->level === null)   
        {
            $user = $this->loadUser();
            if($user)
                $this->level = $user->getLevel();
            else
                return false;
        }
        return $this->level;                  
    }
 
    function getScore($type=null){
        $user = $this->loadUser();
        if($user)
            return $user->getScore($type);
        return false;
    }
 
    function getTruthRankName($scoreTruth){
        if($scoreTruth < 10)
            return 'Little Angel';
        elseif($scoreTruth < 50)
            return 'Wise Angel';
        elseif($scoreTruth < 100)
            return 'White Angel';
        elseif($scoreTruth < 500)
            return 'Golden Angel';
    }
 
    function getDareRankName($scoreDare){
        if($scoreDare < 10)
            return 'Little Imp';
        elseif($scoreDare < 50)
            return 'Naughty Imp';
        elseif($scoreDare < 100)
            return 'Red Evil';
        elseif($scoreDare < 500)
            return 'Black Evil';
    }
 
    function email(){
        $user = $this->loadUser();
        if ($user)
           return $user->email;
        return false;
    }
  
    // Load user model.
    protected function loadUser()
    {
        if ( $this->_model === null ) {
                $this->_model = User::model()->findByPk( $this->id );
        }
        return $this->_model;
    }
}

?>
