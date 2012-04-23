<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MyFunctiuns
 *
 * @author Eric
 */
class MyFunctiuns {
    
    // ----------------------------------------- 
    // Give age according to Birthdate
    // -----------------------------------------
    public static function getAge ($birthday)
   {
       list($year,$month,$day) = explode("-",$birthday);
       $year_diff  = date("Y") - $year;
       $month_diff = date("m") - $month;
       $day_diff   = date("d") - $day;
       if (($month_diff < 0) || (($month_diff==0) && ($day_diff < 0))) 
           $year_diff--;
       return $year_diff;
   }
    
    // ----------------------------------------- 
    // Ajoute/Modifie un parametre à un URL.
    // -----------------------------------------

    public function ajouterParametreGET($url, $paramNom, $paramValeur){
        $urlFinal = "";
        if($paramNom==""){
            $urlFinal = $url;
        }else{
            $t_url = explode("?",$url);
            if(count($t_url)==1){
            // pas de queryString
                $urlFinal .= $url;
                if(substr($url,strlen($url)-1,strlen($url))!="/"){
                    $t_url2 = explode("/",$url);
                    if(preg_match("/./",$t_url2[count($t_url2)-1])==false){
                    $urlFinal .= "/";
                    }
                }
                $urlFinal .= "?".$paramNom."=".$paramValeur;
            }else if(count($t_url)==2){
                // il y a une queryString
                $paramAAjouterPresentDansQueryString = "non";
                $t_queryString = explode("&",$t_url[1]);
                foreach($t_queryString as $cle => $coupleNomValeur){
                    $t_param = explode("=",$coupleNomValeur);
                    if($t_param[0]==$paramNom){
                        $paramAAjouterPresentDansQueryString = "oui";
                    }
                }
                if($paramAAjouterPresentDansQueryString=="non"){
                    // le parametre à ajouter n'existe pas encore dans la queryString
                    $urlFinal = $url."&".$paramNom."=".$paramValeur;
                }else if($paramAAjouterPresentDansQueryString=="oui"){
                    // le parametre à ajouter existe déjà dans la queryString
                    // donc on va reconstruire l'URL
                    $urlFinal = $t_url[0]."?";
                    foreach($t_queryString as $cle => $coupleNomValeur){
                        if($cle > 0){
                            $urlFinal .= "&";
                        }
                        $t_coupleNomValeur = explode("=",$coupleNomValeur); 
                        if($t_coupleNomValeur[0]==$paramNom){ 
                            $urlFinal .= $paramNom."=".$paramValeur;
                        }else{
                            $urlFinal .= $t_coupleNomValeur[0]."=".$t_coupleNomValeur[1];
                        }
                    }
                }
            }
        }
        return $urlFinal;
    }

}

?>
