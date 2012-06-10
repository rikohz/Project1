<?php
$rndId = uniqid();
                
$uploadFromServerView = "/TruthOrDare/userImages/temp/{$rndId}." . pathinfo($_FILES["userfile"]["name"], PATHINFO_EXTENSION);
$uploadfile = $_SERVER['DOCUMENT_ROOT']."TruthOrDare/userImages/temp/{$rndId}." . pathinfo($_FILES["userfile"]["name"], PATHINFO_EXTENSION);
$allowedExtensions = array("image/gif","image/jpeg","image/png","image/pjpeg");

// Upload fichier
if (in_array($_FILES["userfile"]["type"],$allowedExtensions)) 
    if(($_FILES["userfile"]["size"] <= (1024 * 1024 * 2)))
        if ($_FILES["userfile"]["error"] == 0)
            if (move_uploaded_file ($_FILES['userfile']['tmp_name'],$uploadfile))
            {
                Yii::import('application.extensions.image.Image');
                $image = Yii::app()->image->load($uploadfile);
                echo $rndId . "|" . $uploadFromServerView . "|" . $image->__get('width') . "|" . $image->__get('height');
            }
            else 
                echo "4";
        else
            echo "3";
    else
        echo "2";
else 
    echo "1";

?>