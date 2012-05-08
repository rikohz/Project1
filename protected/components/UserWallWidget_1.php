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
        $wallOwner = User::model()->with('scoreTruth','scoreDare')->findByPk($this->idWallOwner);
        
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
        $criteria->addCondition('success = 1');
        if(isset($this->filterLevel))
            $criteria->addCondition("(categoryTruth.level IS NOT NULL AND categoryTruth.level <= $this->filterLevel) OR (categoryDare.level IS NOT NULL AND categoryDare.level <= $this->filterLevel)");
        $challenges = Challenge::model()->with('truth','dare','truth.category','dare.category')->findAll($criteria);
        foreach($challenges as $row)
        {
            if($row->idTruth === null)
                $wall[$i]['type'] = "ChallengeDare";
            else
                $wall[$i]['type'] = "ChallengeTruth";
            $wall[$i]['id'] = $row->idChallenge;
            $wall[$i]['content'] = isset($row->truth) ? $row->truth->truth : $row->dare->dare;
            $wall[$i]['createDate'] = $row->createDate;
            $wall[$i]['userPicture'] = $wallOwner->profilePicture . '_mini' . $wallOwner->profilePictureExtension;
            $wall[$i]['category'] = isset($row->truth) ? $row->truth->category->category : $row->dare->category->category;
            $wall[$i]['vote'] = $row->voteUp - $row->voteDown;
            $wall[$i]['nbFavourite'] = 0;
            $wall[$i]['nbComment'] = 0;
            $wall[$i]['idDisplayUser'] = $wallOwner->idUser;
            $wall[$i]['displayUsername'] = $wallOwner->username;
            $wall[$i]['pictureChallengeDareMini'] = $row->pictureName . "_mini" . $row->pictureExtension;
            $wall[$i]['pictureChallengeDare'] = $row->pictureName . "_original" . $row->pictureExtension;
            $wall[$i]['answerChallengeTruth'] = $row->answer;
            $wall[$i]['rankTruth'] = MyFunctions::getTruthRankName($wallOwner->scoreTruth->score);
            $wall[$i]['rankDare'] = MyFunctions::getDareRankName($wallOwner->scoreDare->score);
            $i++;   
        }

        //Get Truths and add them to the Wall array()
        $truth = new Truth;
        $truth->idUser = $this->idWallOwner;
        if(isset($this->filterLevel))
            $truth->levelMax = $this->filterLevel;
        $criteria = $truth->getCriteria(); 
        $truths = Truth::model()->notAnonymous()->findAll($criteria);
        foreach($truths as $row)
        {
            $wall[$i]['type'] = "Truth";
            $wall[$i]['id'] = $row->idTruth;
            $wall[$i]['content'] = $row->truth;
            $wall[$i]['createDate'] = $row->dateSubmit;
            $wall[$i]['userPicture'] = $wallOwner->profilePicture . '_mini' . $wallOwner->profilePictureExtension;
            $wall[$i]['category'] = $row->category->category;
            $wall[$i]['vote'] = $row->voteUp - $row->voteDown;
            $wall[$i]['nbFavourite'] = $row->nbFavourite;
            $wall[$i]['nbComment'] = $row->nbComment;
            $wall[$i]['idDisplayUser'] = $wallOwner->idUser;
            $wall[$i]['displayUsername'] = $wallOwner->username;
            $wall[$i]['pictureChallengeDareMini'] = null;
            $wall[$i]['pictureChallengeDare'] = null;
            $wall[$i]['answerChallengeTruth'] = null;
            $wall[$i]['rankTruth'] = MyFunctions::getTruthRankName($wallOwner->scoreTruth->score);
            $wall[$i]['rankDare'] = MyFunctions::getDareRankName($wallOwner->scoreDare->score);
            $i++;   
        }

        //Get Dares and add them to the Wall array()
        $dare = new Dare;
        $dare->idUser = $this->idWallOwner;  
        if(isset($this->filterLevel))
            $dare->levelMax = $this->filterLevel;
        $criteria = $dare->getCriteria();
        $dares = Dare::model()->notAnonymous()->findAll($criteria);
        foreach($dares as $row)
        {
            $wall[$i]['type'] = "Dare";
            $wall[$i]['id'] = $row->idDare;
            $wall[$i]['content'] = $row->dare;
            $wall[$i]['createDate'] = $row->dateSubmit;
            $wall[$i]['userPicture'] = $wallOwner->profilePicture . '_mini' . $wallOwner->profilePictureExtension;
            $wall[$i]['category'] = $row->category->category;
            $wall[$i]['vote'] = $row->voteUp - $row->voteDown;
            $wall[$i]['nbFavourite'] = $row->nbFavourite;
            $wall[$i]['nbComment'] = $row->nbComment;
            $wall[$i]['idDisplayUser'] = $wallOwner->idUser;
            $wall[$i]['displayUsername'] = $wallOwner->username;
            $wall[$i]['pictureChallengeDareMini'] = null;
            $wall[$i]['pictureChallengeDare'] = null;
            $wall[$i]['answerChallengeTruth'] = null;
            $wall[$i]['rankTruth'] = MyFunctions::getTruthRankName($wallOwner->scoreTruth->score);
            $wall[$i]['rankDare'] = MyFunctions::getDareRankName($wallOwner->scoreDare->score);
            $i++;   
        }

        //Get Ranks Upgrades of the user
        $criteria = new CDbCriteria;
        $criteria->addCondition('idUser = :idUser');
        $criteria->params = array(':idUser'=>$this->idWallOwner);
        $userUpgrades = Userrank::model()->with('rank')->findAll($criteria);

        foreach($userUpgrades as $row)
        {
            $wall[$i]['type'] = "RankUpgrade";
            $wall[$i]['id'] = null;
            $wall[$i]['content'] = "You just got upgraded to <b>" . $row->rank->name . "</b>, congratulations!";
            $wall[$i]['createDate'] = $row->createDate;
            $wall[$i]['userPicture'] = $wallOwner->profilePicture . '_mini' . $wallOwner->profilePictureExtension;
            $wall[$i]['category'] = null;
            $wall[$i]['vote'] = null;
            $wall[$i]['nbFavourite'] = null;
            $wall[$i]['nbComment'] = null;
            $wall[$i]['idDisplayUser'] = $wallOwner->idUser;
            $wall[$i]['displayUsername'] = $wallOwner->username;
            $wall[$i]['pictureChallengeDareMini'] = null;
            $wall[$i]['pictureChallengeDare'] = null;
            $wall[$i]['answerChallengeTruth'] = null;
            $wall[$i]['rankTruth'] = MyFunctions::getTruthRankName($wallOwner->scoreTruth->score);
            $wall[$i]['rankDare'] = MyFunctions::getDareRankName($wallOwner->scoreDare->score);
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