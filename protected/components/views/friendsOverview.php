<div style="width:150px; height:500px; border:1px black dashed;">   
    <?php foreach($datas as $row){ ?>
        <?php $friend = $row->userTo->idUser == $this->idUser ? $row->userFrom : $row->userTo; ?>
    <a href="index.php?r=user/userProfile?id=<?php echo $friend->idUser; ?>" style="text-decoration:none; color:black;">
        <div style="display:inline-block;">
            <div style="width:64px; height:64px; border:1px black solid;margin:5px 0 0 5px;"><img src="<?php echo Yii::app()->request->baseUrl; ?>/userImages/profilePicture_mini/<?php echo $friend->profilePicture . '_mini' . $friend->profilePictureExtension; ?>" width="64px" height="64px" /></div>
            <div style="text-align:center;"><?php echo $friend->username; ?></div>
        </div>
    </a>
    <?php } ?>   
</div>