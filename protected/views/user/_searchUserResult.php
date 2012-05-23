
<table width="100%" style="border-spacing:5px;">
    <tr>
        <td width="20%">&nbsp;</td>
        <td width="10%">&nbsp;</td>
        <td width="10%">&nbsp;</td>
        <td width="20%">&nbsp;</td>
        <td width="20%">&nbsp;</td>
        <td width="20%">&nbsp;</td>
    </tr>
    <?php foreach($searchResult as $row): ?>
        <tr style="cursor:pointer; background-color: <?php echo $row->gender == 0 ? '#FBE3E4' : '#B7D6E7'; ?>" onClick="window.location='index.php?r=user/userPage&idUser=<?php echo $row->idUser; ?>;'">
            <td><img src="<?php echo Yii::app()->request->baseUrl; ?>/userImages/profilePicture_mini/<?php echo $row->profilePicture . '_mini' . $row->profilePictureExtension; ?>" width="64px" height="64px" /></td>
            <td><?php echo $row->username; ?></td>
            <td><?php echo isset($row->birthDate) ? MyFunctions::getAge($row->birthDate) : 'UNKNOWN'; ?></td>
            <td><?php echo isset($row->level) ? $row->level->level : 0; ?></td>
            <td><?php echo MyFunctions::getTruthRankName($row->scoreTruth->score); ?></td>
            <td><?php echo MyFunctions::getDareRankName($row->scoreDare->score); ?></td>
        </tr>
    <?php endforeach; ?>
</table>
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
            'idDivUpdate'=>"searchResult",
            'idForm'=>"user-search-form"
        )) ?>
    </div>
<?php endif; ?>