<?php
class FriendsOverview extends CWidget
{
    public $idUser;
    
    public function run()
    {  
        $friendsFrom = Friend::model()->with('userTo')->findAllByAttributes(array('idUserFrom'=>$this->idUser,'accepted'=>1)); 
        $friendsTo = Friend::model()->with('userFrom')->findAllByAttributes(array('idUserTo'=>$this->idUser,'accepted'=>1));
        $datas = array_merge($friendsFrom,$friendsTo);
        shuffle($datas);
        $this->render('friendsOverview',array('datas'=>$datas));
    }
}
?>