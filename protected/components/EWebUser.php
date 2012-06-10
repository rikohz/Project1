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
    
    public function getReturnUrl($defaultUrl=null)        
    {                
        if($defaultUrl===null)                         
            $defaultUrl=$this->defaultReturnUrl;                
        return parent::getReturnUrl($defaultUrl);        
    }
 
    function isAdmin(){
        $user = $this->loadUser();
        if ($user !== null)
           return $user->username == 'Eric';
        return false;
    }
 
    function getLevel(){
        if($this->hasState('userLevel') == 0)   
        {
            $user = $this->loadUser();
            if($user !== null)
                $this->setState('userLevel',$user->getLevel());
            else
                return 1;
        }
        return $this->getState('userLevel');                  
    }
 
    function getScoreTruth(){
        $user = $this->loadUser();
        if($user !== null)
            return $user->scoreTruth->score;
        else
            return false;
    }
 
    function getScoreDare(){
        $user = $this->loadUser();
        if($user !== null)
            return $user->scoreDare->score;
        else
            return false;
    }
 
    function getScoreVoteIdeas($type=null){
        $user = $this->loadUser();
        if($user !== null)
            return $user->getScoreVoteIdeas($type);
        else
            return false;
    }
 
    function getScoreChallenges($type=null){
        $user = $this->loadUser();
        if($user !== null)
            return $user->getScoreChallenges($type);
        else
            return false;
    }
 
    function getScoreVoteChallenges($type=null){
        $user = $this->loadUser();
        if($user !== null)
            return $user->getScoreVoteChallenges($type);
        else
            return false;
    }
 
    function email(){
        $user = $this->loadUser();
        if ($user !== null)
           return $user->email;
        return false;
    }
  
    // Load user model.
    protected function loadUser()
    {
        if ( $this->hasState('user') == 0 ) {
            $this->setState('user',User::model()->with('scoreTruth','scoreDare')->findByPk($this->id));
        }
        return $this->getState('user');
    }
}

?>
