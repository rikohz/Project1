<?php
class UserWallWidget extends CWidget
{
    //Int
    //idUser of the current User
    public $idCurrentUser;
    
    //Int
    //idUser of the owner of the wall
    public $idWallOwner;
    
    //Bit
    //Display the link to ass to Favourites
    public $withFavourites;
    
    //Bit
    //Display the number of votes and the thumbs links for voting
    public $withVotes;
    
    //Bit
    //Display the number of comments with a link
    public $withComments;
    
    //Int 
    //Maximum level of Truth and Dare we can display
    public $filterLevel;
    
    //Bit
    //Display or not the form to add Wall Messages
    public $withFormMessage = 1;
    
    public function run()
    {  
        $wallOwner = User::model()->findByPk($this->idWallOwner);
        
        //Add message to the Wall
        $model = new UserWall;
        if(isset($_POST['Userwall']))
        {
            $model->attributes = $_POST['Userwall'];
            $model->idUserFrom = $this->idCurrentUser;
            $model->idUserTo = $this->idWallOwner;
            $model->createDate = date('Y-m-d, H:i:s');
            $model->save();
            $model->content='';
        }

        //Get Wall Messages and add them to the Wall array()
        $criteria = new CDbCriteria;
        $criteria->addCondition('idUserTo = :idUser');
        $criteria->params = array(':idUser'=>$this->idWallOwner);
        $criteria->order = 'createDate DESC';
        $wallComments = UserWall::model()->with('userFrom')->findAll($criteria);

        $wall = array(); $i = 0;
        foreach($wallComments as $row)
        {
            $wall[$i]['type'] = "WallMessage";
            $wall[$i]['id'] = "$row->idUserWall";
            $wall[$i]['content'] = $row->content;
            $wall[$i]['createDate'] = $row->createDate;
            $wall[$i]['picture'] = $row->userFrom->profilePicture;
            $wall[$i]['pictureExtension'] = $row->userFrom->profilePictureExtension;
            $wall[$i]['category'] = 0;
            $wall[$i]['vote'] = 0;
            $wall[$i]['nbFavourite'] = 0;
            $wall[$i]['nbComment'] = 0;
            $wall[$i]['displayUsername'] = $row->userFrom->username;
            $i++;   
        }

        //Get Challenges Notifications and add them to the Wall array()
        $criteria = new CDbCriteria;
        $criteria->addCondition('success = 1');
        if(isset($this->filterLevel))
            $criteria->addCondition("(categoryTruth.level IS NOT NULL AND categoryTruth.level <= $this->filterLevel) OR (categoryDare.level IS NOT NULL AND categoryDare.level <= $this->filterLevel)");
        $criteria->order = 'createDate DESC';
        $challenges = Challenge::model()->with('truth','dare','truth.category','dare.category')->findAll($criteria);
        foreach($challenges as $row)
        {
            $wall[$i]['type'] = "Challenge";
            $wall[$i]['id'] = "$row->idChallenge";
            $wall[$i]['content'] = isset($row->truth) ? $row->truth->truth : $row->dare->dare;
            $wall[$i]['createDate'] = $row->createDate;
            $wall[$i]['picture'] = $wallOwner->profilePicture;
            $wall[$i]['pictureExtension'] = $wallOwner->profilePictureExtension;
            $wall[$i]['category'] = isset($row->truth) ? $row->truth->category->category : $row->dare->category->category;
            $wall[$i]['vote'] = $row->voteUp - $row->voteDown;
            $wall[$i]['nbFavourite'] = 0;
            $wall[$i]['nbComment'] = 0;
            $wall[$i]['displayUsername'] = $wallOwner->username;
            $i++;   
        }

        //Get Truths and add them to the Wall array()
        $truth = new Truth;
        $truth->idUser = $this->idWallOwner;  
        if(isset($this->filterLevel))
            $truth->levelMax = $this->filterLevel;
        $criteria = $truth->getCriteria(); 
        $criteria->order = 'dateSubmit DESC';
        $truths = Truth::model()->findAll($criteria);
        foreach($truths as $row)
        {
            $wall[$i]['type'] = "Truth";
            $wall[$i]['id'] = "$row->idTruth";
            $wall[$i]['content'] = $row->truth;
            $wall[$i]['createDate'] = $row->dateSubmit;
            $wall[$i]['picture'] = $wallOwner->profilePicture;
            $wall[$i]['pictureExtension'] = $wallOwner->profilePictureExtension;
            $wall[$i]['category'] = $row->category->category;
            $wall[$i]['vote'] = $row->voteUp - $row->voteDown;
            $wall[$i]['nbFavourite'] = $row->nbFavourite;
            $wall[$i]['nbComment'] = $row->nbComment;
            $wall[$i]['displayUsername'] = $wallOwner->username;
            $i++;   
        }

        //Get Dares and add them to the Wall array()
        $dare = new Dare;
        $dare->idUser = $this->idWallOwner;  
        if(isset($this->filterLevel))
            $dare->levelMax = $this->filterLevel;
        $criteria = $dare->getCriteria(); 
        $criteria->order = 'dateSubmit DESC';
        $dares = Dare::model()->findAll($criteria);
        foreach($dares as $row)
        {
            $wall[$i]['type'] = "Dare";
            $wall[$i]['id'] = "$row->idDare";
            $wall[$i]['content'] = $row->dare;
            $wall[$i]['createDate'] = $row->dateSubmit;
            $wall[$i]['picture'] = $wallOwner->profilePicture;
            $wall[$i]['pictureExtension'] = $wallOwner->profilePictureExtension;
            $wall[$i]['category'] = $row->category->category;
            $wall[$i]['vote'] = $row->voteUp - $row->voteDown;
            $wall[$i]['nbFavourite'] = $row->nbFavourite;
            $wall[$i]['nbComment'] = $row->nbComment;
            $wall[$i]['displayUsername'] = $wallOwner->username;
            $i++;   
        }

        //Manage favourites
        $modelUserList = new UserList;
        $userLists = CHtml::listData(array(),'idUserList','name');
        if(!Yii::app()->user->isGuest)
        {
            $userLists = UserList::model()->findAllByAttributes(array('idUser'=>$this->idCurrentUser)); 
            $userLists = CHtml::listData($userLists,'idUserList','name');
        }

        //Order the Wall array() by createDate DESC
        $wall = MyFunctions::arraySort($wall, 'createDate', 'DESC');

        $this->render('userWallWidget',array('model'=>$model,'wall'=>$wall, 'userLists'=>$userLists,'modelUserList'=>$modelUserList));
    }
}
?>