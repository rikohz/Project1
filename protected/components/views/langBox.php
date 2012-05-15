<?php
    $myFunctions = new MyFunctions()
?>

<a href="<?php echo $myFunctions->ajouterParametreGET(Yii::app()->request->Url,'lang','fr_fr') ?>"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/flag-FR_FR.png" /></a>
<a href="<?php echo $myFunctions->ajouterParametreGET(Yii::app()->request->Url,'lang','en_us') ?>"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/flag-EN_US.png" /></a>
<a href="<?php echo $myFunctions->ajouterParametreGET(Yii::app()->request->Url,'lang','zh_cn') ?>"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/flag-ZH_CN.png" /></a>

