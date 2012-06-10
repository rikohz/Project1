<script type="text/javascript">
    //Send friend request
    function sendFriendRequest(friendLink)
    {
        var idUser = friendLink.id;
        $.ajax({
            type: "POST",
            url: "index.php?r=user/sendFriendRequest",
            data: "idUser="+idUser,
            cache: false,

            success: function(html)
            {
                document.getElementById('dialog-friendRequest').innerHTML = html;
                $('#dialog-friendRequest').dialog('open');
            } 
        })
    };
        
    $(document).ready(function() {

        <?php foreach($searchResult as $row): ?>
            $("#PBT<?php echo $row->idUser; ?>").progressbar({ value: <?php echo MyFunctions::getValueProgressBar($row->scoreTruth->score); ?> });
            $("#PBD<?php echo $row->idUser; ?>").progressbar({ value: <?php echo MyFunctions::getValueProgressBar($row->scoreDare->score); ?> });
        <?php endforeach; ?>
 

        //Dialog box for Friend Request      
        $("#dialog-friendRequest").dialog({ autoOpen: false })
        $("#dialog-friendRequest").dialog({
                modal: true,
                buttons: {
                        Ok: function() {
                                $( this ).dialog( "close" );
                        }
                }
        });

    });  
</script>

<?php foreach($searchResult as $row): ?>
    <a style="text-decoration: none; color:black;" href="index.php?r=user/userPage&idUser=<?php echo $row->idUser; ?>">
        <div style="position:relative;padding:5px; margin: 3px 3px 0 0; display:inline-block; width:210px; cursor:pointer; background-color: <?php echo $row->gender == 0 ? '#FDD' : '#DDF'; ?>">
            <div style="float:left; width:64px; margin:0 3px 3px 0;"><img src="<?php echo Yii::app()->request->baseUrl; ?>/userImages/profilePicture_mini/<?php echo $row->profilePicture . '_mini' . $row->profilePictureExtension; ?>" width="64px" height="64px" style="margin:0" /></div>
            <div>
                <div><b><?php echo $row->username; ?></b></div>
                <div style="font-size:0.9em;">
                    <?php 
                        echo isset($row->province)? $row->province->name : "";
                        echo isset($row->city)? " - " . $row->city->name : "";
                        echo isset($row->district)? " - " . $row->district->name : "" ; 
                    ?>
                </div>
                <div><?php echo isset($row->birthDate) ? MyFunctions::getAge($row->birthDate) . " yo" : ''; ?></div>
                <div>Level <?php echo isset($row->level) ? $row->level->level : 0; ?></div>
            </div>
            <div style="clear:both; width:140px;">
                <div style="width:210px;"><?php echo MyFunctions::getTruthRankName($row->scoreTruth->score); ?></div>
                <div id="PBT<?php echo $row->idUser; ?>" style="width:110px; height:10px;"></div>
                <div style="width:210px;"><?php echo MyFunctions::getDareRankName($row->scoreDare->score); ?></div>
                <div id="PBD<?php echo $row->idUser; ?>" style="width:110px; height:10px;"></div>
            </div>
            <div style="position:absolute; bottom:23px;right:3px;">
                <img onClick="sendFriendRequest(this);return false;" id="<?php echo $row->idUser; ?>" src="/TruthOrDare/images/addFriend.png" style="cursor:pointer;z-index:64000;" width="32px" height="32px" title="Add Friend" />
            </div>
            <div style="position:absolute; bottom:3px;right:3px;font-size:0.7em;">Last login: <?php echo MyFunctions::getNbDaysBetweenDates($row->lastLoginDate,date('Y-m-d, H:i:s')) - 1; ?> days</div>
        </div>
    </a>
<?php endforeach; ?>

<br />
<br />
<br />
<br />

<!--************-->
<!-- Link Pager -->
<!--************-->
<?php if(isset($pages)): ?>
    <div style="text-align: center;">
        <?php $this->widget('AjaxFormCLinkPager', array(
            'pages' => $pages,
            'maxButtonCount'=>10,
            'header'=>"",
            'idDivUpdate'=>"divSearchResult",
            'idForm'=>"user-search-form"
        )) ?>
    </div>
<?php endif; ?>

<!--***************************-->
<!-- Dialog for Friend Request -->
<!--***************************-->
<div id="dialog-friendRequest" title="Friend Request"></div>