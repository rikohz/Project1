<?php
class TruthList extends CWidget
{
    //Int
    //idUser of the current User
    public $idUser;
    
    //Int
    //Initiate this attribute to display only one Truth
    public $idTruth;
    
    //Bit
    //Display the link to ass to Favourites
    public $withFavourites;
    
    //Bit
    //Display the number of votes and the thumbs links for voting
    public $withVotes;
    
    //Bit
    //Display the number of comments with a link
    public $withComments;
    
    //String
    //Criteria for order results
    public $order;
    
    //Int 
    //Maximum level of Truth we can display
    public $filterLevel;
    
    //Int - idCategory
    //Choose category of Truth to display
    public $category;
    
    //Int
    //Total of Truths we want to display
    public $limit;
    
    //Int
    //Number of Truths displayed on one page
    public $itemsPerPage = 10;
    
    public function run()
    {  
        //We check the level of the user before to allow him to see the content
        if(isset($this->category) && isset($this->filterLevel) && $this->category > $this->filterLevel)
            Yii::app()->user->setFlash('forbiddenLevel','Sorry, to have access to this category you need to register a coin which belongs to this category.');
        
        $model = new Truth;
        
        //Filter and order
        if(isset($this->category) && $this->category != 0)
            $model->category = $this->category;
        if(isset($this->idTruth))
            $model->idTruth = $this->idTruth;
        $criteria = $model->getCriteria();   
        if(isset($this->limit))
            $criteria->limit = $this->limit; 
        $criteria->order = isset($this->order)? "$this->order DESC " : " t.voteUp - t.voteDown DESC ";

        //Page manager
        $count = $model->validated()->levelFilter($this->filterLevel)->count($criteria);
        //Use the $this->limit in Pagination otherwise $pages->pageSize to $criteria overriding the $criteria->limit 
        $pages = new CPagination(isset($this->limit) ? $this->limit : $count);
        $pages->pageSize = isset($this->limit) ? $this->limit : $this->itemsPerPage;
        $pages->applyLimit($criteria);

        //Get the datas
        $datas = $model->validated()->levelFilter($this->filterLevel)->findAll($criteria);

        //Manage favourites
        $modelUserList = new UserList;
        $userLists = CHtml::listData(array(),'idUserList','name');
        if(!Yii::app()->user->isGuest)
        {
            $userLists = UserList::model()->findAllByAttributes(array('idUser'=>Yii::app()->user->getId())); 
            $userLists = CHtml::listData($userLists,'idUserList','name');
        }

        $this->render('truthList',array('datas'=>$datas,'pages'=>$pages, 'userLists'=>$userLists,'modelUserList'=>$modelUserList));
    }
}
?>