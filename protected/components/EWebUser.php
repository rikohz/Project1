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
    protected $scoreTruth;   
    protected $scoreDare;            
    
    public function getReturnUrl($defaultUrl=null)        
    {                
        if($defaultUrl===null)                         
            $defaultUrl=$this->defaultReturnUrl;                
        return parent::getReturnUrl($defaultUrl);        
    }
 
    function isAdmin(){
        $user = $this->loadUser();
        if ($user)
           return $user->username == 'Eric';
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
 
    function getScoreTruth(){
        if($this->scoreTruth === null)   
        {
            $user = $this->loadUser();
            if($user)
                $this->scoreTruth = $user->scoreTruth->score;
            else
                return false;
        }
        return $this->scoreTruth;  
    }
 
    function getScoreDare(){
        if($this->scoreDare === null)   
        {
            $user = $this->loadUser();
            if($user)
                $this->scoreDare = $user->scoreDare->score;
            else
                return false;
        }
        return $this->scoreDare; 
    }
 
    function getScoreVoteIdeas($type=null){
        $user = $this->loadUser();
        if($user)
            return $user->getScoreVoteIdeas($type);
        else
            return false;
    }
 
    function getScoreChallenges($type=null){
        $user = $this->loadUser();
        if($user)
            return $user->getScoreChallenges($type);
        else
            return false;
    }
 
    function getScoreVoteChallenges($type=null){
        $user = $this->loadUser();
        if($user)
            return $user->getScoreVoteChallenges($type);
        else
            return false;
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
                $this->_model = User::model()->with('scoreTruth','scoreDare')->findByPk($this->id);
        }
        return $this->_model;
    }
}

?>
