<?php
class TruthList extends CWidget
{
    //Int
    //idUser of the current User
    public $idUser;
    
    //Bit
    //Display the link to ass to Favourites
    public $withFavourites;
    
    //Bit
    //Display the number of votes and the thumbs links for voting
    public $withVotes;
    
    //Bit
    //Display the number of comments with a link
    public $withComments;
    
    //Bit
    //Allow user to send as a Challenge
    public $withSendChallenge;
    
    //Bit
    //Display the informations about the author od the Dare
    public $withAuthorScores = 0;
    
    //Int 
    //Maximum level of Truth we can display
    public $filterLevel;
    
    //SearchTruthForm Model
    //Model initialized with the filter criterias
    public $model;
    
    //Int
    //Number of Truths displayed on one page
    public $itemsPerPage = 10;
    
    //String
    //If the Truth List is inside a Div to update with AJAX at each changement of search criteria,
    //use this parameter with the name of the DIV (also see $formCriteria Parameter)
    public $idDivUpdate = null;
    
    //String
    //If the Truth List is inside a Div to update with AJAX at each changement of search criteria,
    //and we use a Form to select criterias, use this parameter with the name of the FORM 
    public $idFormCriteria = null;
    
    public function run()
    {  
        //We check the level of the user before to allow him to see the content
        if(isset($this->model->idCategory) && isset($this->filterLevel) && $this->model->idCategory != '')
        {
            $levelCategory = Category::model()->findByPk($this->model->idCategory);
            if($levelCategory->level > $this->filterLevel)
                Yii::app()->user->setFlash('forbiddenLevel','Sorry, to have access to this category you need to register a coin which belongs to this category.');
        }
        
        //If the user makes a research by username, we shouldn't display the anonymous ones
        if(isset($this->model->username) && $this->model->username !== '')
                $this->model->anonymous = 0;
        
        //We get the generated criterias
        $criteria = $this->model->getCriteria();
        
        //We set up the number of Truth we want to display if necessary
        if(isset($this->model->limit))
            $criteria->limit = $this->model->limit; 
        
        //We choose the order of display
        $criteria->order = isset($this->model->order) && $this->model->order !== '' ? $this->model->order . " DESC " : " t.voteUp - t.voteDown DESC ";                    

        //Page manager
        $count = Truth::model()->levelFilter($this->filterLevel)->count($criteria);
        //Use the $this->limit in Pagination otherwise $pages->pageSize to $criteria overriding the $criteria->limit 
        $pages = new CPagination(isset($this->model->limit) ? $this->model->limit : $count);
        $pages->pageSize = isset($this->model->limit) ? $this->model->limit : $this->itemsPerPage;
        $pages->applyLimit($criteria);

        //Get the datas
        $datas = Truth::model()->levelFilter($this->filterLevel)->findAll($criteria);

        //Manage favourites
        $modelUserList = new UserList;
        $userLists = CHtml::listData(array(),'idUserList','name');
        if(!Yii::app()->user->isGuest)
        {
            $userLists = UserList::model()->findAllByAttributes(array('idUser'=>Yii::app()->user->getId())); 
            $userLists = CHtml::listData($userLists,'idUserList','name');
        }

        //Manage send Challenges
        $friends = CHtml::listData(array(),'idUser','username');
        if(!Yii::app()->user->isGuest) 
            $friends = CHtml::listData(Friend::getFriends(Yii::app()->user->getId()),'idUser','username');

        $this->render('truthList',array(
            'datas'=>$datas,
            'pages'=>$pages, 
            'userLists'=>$userLists,
            'friends'=>$friends
            )
        ); 
    }
}
?>