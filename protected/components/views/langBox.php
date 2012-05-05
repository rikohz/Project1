<?php
    $myFunctions = new MyFunctions()
?>

<a href="<?php echo $myFunctions->ajouterParametreGET(Yii::app()->request->Url,'lang','FR_FR') ?>"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/flag-FR_FR.png" /></a>
<a href="<?php echo $myFunctions->ajouterParametreGET(Yii::app()->request->Url,'lang','EN_US') ?>"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/flag-EN_US.png" /></a>
<a href="<?php echo $myFunctions->ajouterParametreGET(Yii::app()->request->Url,'lang','ZH_CN') ?>"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/flag-ZH_CN.png" /></a>

