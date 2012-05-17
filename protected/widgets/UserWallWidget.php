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
    
    //Bit
    //Display or not the informations from the Wall Owner friends
    public $withFriendsInformations = 0;
    
    public function run()
    {  
        $wallOwner = User::model()->with('scoreTruth','scoreDare')->findByPk($this->idWallOwner);
        
        //To display notifications from friends
        if($this->withFriendsInformations === 1)
        {
            $friends = $wallOwner->getIdFriends();
            $friends[] = $this->idWallOwner;
        }
        
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
        $criteria->addCondition('t.idUserTo = :idUser');
        $criteria->params = array(':idUser'=>$this->idWallOwner);
        $criteria->order = 'createDate DESC';
        $wallComments = UserWall::model()->with('userFrom','userFrom.scoreTruth','userFrom.scoreDare')->findAll($criteria);

        $wall = array(); $i = 0;
        foreach($wallComments as $row)
        {
            $wall[$i]['type'] = "WallMessage";
            $wall[$i]['id'] = $row->idUserWall;
            $wall[$i]['content'] = $row->content;
            $wall[$i]['createDate'] = $row->createDate;
            $wall[$i]['userPicture'] = $row->userFrom->profilePicture . '_mini' . $row->userFrom->profilePictureExtension;
            $wall[$i]['category'] = null;
            $wall[$i]['vote'] = null;
            $wall[$i]['nbFavourite'] = null;
            $wall[$i]['nbComment'] = null;
            $wall[$i]['idDisplayUser'] = $row->userFrom->idUser;
            $wall[$i]['displayUsername'] = $row->userFrom->username;
            $wall[$i]['pictureChallengeDareMini'] = null;
            $wall[$i]['pictureChallengeDare'] = null;
            $wall[$i]['answerChallengeTruth'] = null;
            $wall[$i]['rankTruth'] = MyFunctions::getTruthRankName($row->userFrom->scoreTruth->score);
            $wall[$i]['rankDare'] = MyFunctions::getDareRankName($row->userFrom->scoreDare->score);
            $i++;   
        }

        //Get Challenges Notifications and add them to the Wall array()
        $criteria = new CDbCriteria;
        $criteria->addCondition('t.status = 1 AND (t.private = 0 OR (t.idUserFrom = :idCurrentUser OR t.idUserTo = :idCurrentUser))');
        $criteria->params = array(':idCurrentUser'=>$this->idCurrentUser);
        if($this->withFriendsInformations === 1)
            $criteria->addInCondition('t.idUserTo',$friends);
        else
        {
            $criteria->addCondition('t.idUserTo = :idUser');
            $criteria->params[':idUser'] = $this->idWallOwner;
        }
        if(isset($this->filterLevel))
            $criteria->addCondition("(categoryTruth.level IS NOT NULL AND categoryTruth.level <= $this->filterLevel) OR (categoryDare.level IS NOT NULL AND categoryDare.level <= $this->filterLevel)");
        $challenges = Challenge::model()->with('userTo','truth','dare','truth.category','dare.category','userTo.scoreTruth','userTo.scoreDare')->findAll($criteria);
        foreach($challenges as $row)
        {
            $wall[$i]['type'] = $row->idTruth === null ? "ChallengeDare" : "ChallengeTruth";
            $wall[$i]['id'] = $row->idChallenge;
            $wall[$i]['content'] = isset($row->truth) ? $row->truth->truth : $row->dare->dare;
            $wall[$i]['createDate'] = $row->createDate;
            $wall[$i]['userPicture'] = $row->userTo->profilePicture . '_mini' . $row->userTo->profilePictureExtension;
            $wall[$i]['category'] = isset($row->truth) ? $row->truth->category->category : $row->dare->category->category;
            $wall[$i]['vote'] = $row->voteUp - $row->voteDown;
            $wall[$i]['nbFavourite'] = 0;
            $wall[$i]['nbComment'] = 0;
            $wall[$i]['idDisplayUser'] = $row->userTo->idUser;
            $wall[$i]['displayUsername'] = $row->userTo->username;
            $wall[$i]['pictureChallengeDareMini'] = $row->pictureName . "_mini" . $row->pictureExtension;
            $wall[$i]['pictureChallengeDare'] = $row->pictureName . "_original" . $row->pictureExtension;
            $wall[$i]['answerChallengeTruth'] = $row->answer;
            $wall[$i]['rankTruth'] = MyFunctions::getTruthRankName($row->userTo->scoreTruth->score);
            $wall[$i]['rankDare'] = MyFunctions::getDareRankName($row->userTo->scoreDare->score);
            $i++;   
        }

        //Get Truths and add them to the Wall array()
        $truth = new Truth;
        if(isset($this->filterLevel))
            $truth->levelMax = $this->filterLevel;
        $criteria = $truth->getCriteria(); 
        if($this->withFriendsInformations === 1)
            $criteria->addInCondition('t.idUser',$friends);
        else
        {
            $criteria->addCondition('t.idUser = :idUser');
            $criteria->params[':idUser'] = $this->idWallOwner;
        }
        $truths = Truth::model()->notAnonymous()->findAll($criteria);
        foreach($truths as $row)
        {
            $wall[$i]['type'] = "Truth";
            $wall[$i]['id'] = $row->idTruth;
            $wall[$i]['content'] = $row->truth;
            $wall[$i]['createDate'] = $row->dateSubmit;
            $wall[$i]['userPicture'] = $row->user->profilePicture . '_mini' . $row->user->profilePictureExtension;
            $wall[$i]['category'] = $row->category->category;
            $wall[$i]['vote'] = $row->voteUp - $row->voteDown;
            $wall[$i]['nbFavourite'] = $row->nbFavourite;
            $wall[$i]['nbComment'] = $row->nbComment;
            $wall[$i]['idDisplayUser'] = $row->user->idUser;
            $wall[$i]['displayUsername'] = $row->user->username;
            $wall[$i]['pictureChallengeDareMini'] = null;
            $wall[$i]['pictureChallengeDare'] = null;
            $wall[$i]['answerChallengeTruth'] = null;
            $wall[$i]['rankTruth'] = MyFunctions::getTruthRankName($row->user->scoreTruth->score);
            $wall[$i]['rankDare'] = MyFunctions::getDareRankName($row->user->scoreDare->score);
            $i++;   
        }

        //Get Dares and add them to the Wall array()
        $dare = new Dare;
        if(isset($this->filterLevel))
            $dare->levelMax = $this->filterLevel;
        $criteria = $dare->getCriteria();
        if($this->withFriendsInformations === 1)
            $criteria->addInCondition('t.idUser',$friends);
        else
        {
            $criteria->addCondition('t.idUser = :idUser');
            $criteria->params[':idUser'] = $this->idWallOwner;
        }
        $dares = Dare::model()->notAnonymous()->findAll($criteria);
        foreach($dares as $row)
        {
            $wall[$i]['type'] = "Dare";
            $wall[$i]['id'] = $row->idDare;
            $wall[$i]['content'] = $row->dare;
            $wall[$i]['createDate'] = $row->dateSubmit;
            $wall[$i]['userPicture'] = $row->user->profilePicture . '_mini' . $row->user->profilePictureExtension;
            $wall[$i]['category'] = $row->category->category;
            $wall[$i]['vote'] = $row->voteUp - $row->voteDown;
            $wall[$i]['nbFavourite'] = $row->nbFavourite;
            $wall[$i]['nbComment'] = $row->nbComment;
            $wall[$i]['idDisplayUser'] = $row->user->idUser;
            $wall[$i]['displayUsername'] = $row->user->username;
            $wall[$i]['pictureChallengeDareMini'] = null;
            $wall[$i]['pictureChallengeDare'] = null;
            $wall[$i]['answerChallengeTruth'] = null;
            $wall[$i]['rankTruth'] = MyFunctions::getTruthRankName($row->user->scoreTruth->score);
            $wall[$i]['rankDare'] = MyFunctions::getDareRankName($row->user->scoreDare->score);
            $i++;   
        }

        //Get Ranks Upgrades of the user
        $criteria = new CDbCriteria;
        if($this->withFriendsInformations === 1)
            $criteria->addInCondition('t.idUser',$friends);
        else
        {
            $criteria->addCondition('t.idUser = :idUser');
            $criteria->params = array(':idUser'=>$this->idWallOwner);
        }
        $userUpgrades = Userrank::model()->with('rank')->findAll($criteria);

        foreach($userUpgrades as $row)
        {
            $wall[$i]['type'] = "RankUpgrade";
            $wall[$i]['id'] = null;
            $wall[$i]['content'] = "I just got upgraded to <b>" . $row->rank->name . "</b>!";
            $wall[$i]['createDate'] = $row->createDate;
            $wall[$i]['userPicture'] = $row->user->profilePicture . '_mini' . $row->user->profilePictureExtension;
            $wall[$i]['category'] = null;
            $wall[$i]['vote'] = null;
            $wall[$i]['nbFavourite'] = null;
            $wall[$i]['nbComment'] = null;
            $wall[$i]['idDisplayUser'] = $row->user->idUser;
            $wall[$i]['displayUsername'] = $row->user->username;
            $wall[$i]['pictureChallengeDareMini'] = null;
            $wall[$i]['pictureChallengeDare'] = null;
            $wall[$i]['answerChallengeTruth'] = null;
            $wall[$i]['rankTruth'] = MyFunctions::getTruthRankName($row->user->scoreTruth->score);
            $wall[$i]['rankDare'] = MyFunctions::getDareRankName($row->user->scoreDare->score);
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