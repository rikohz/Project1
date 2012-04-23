<?php
if(isset($_POST['idUserList']) && (isset($_POST['idTruth']) || isset($_POST['idDare'])))
{
    
    //VERIFIER QUE PERSONNE QUI AJOUTE EST BIEN PERSONNE QUI POSSEDE LA LISTE
    $test = 'toto';
    $test = 'toto';
    $test = 'toto';
    $model = new Userlistcontent;
    $model->idUserList = $_POST['idUserList'];
    if(isset($_POST['idTruth']))
        $model->idTruth = $_POST['idTruth'];
    if(isset($_POST['idDare']))
        $model->idTruth = $_POST['idDare'];
    if($model->save())
        echo "SUCCESS";
    else
        echo "FAIL";
}    
?>