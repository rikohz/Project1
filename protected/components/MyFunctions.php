<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MyFunctions
 *
 * @author Eric
 */
class MyFunctions {
    
    /**
    * Returns age of user according to his/her $birthdate date
    * @return Int
    */
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
        
   
    /**
    * Returns URL with the $paramNom parameter added using the $paramValeur value
    * @return String
    */
    public static function ajouterParametreGET($url, $paramNom, $paramValeur)
    {
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
        
    
    /**
    * Returns the $array ordered by the specified $subKey usinf the specified $order
    * @return array
    */
    public static function arraySort($array,$subKey,$order='ASC')
    {
        if($array !== array())
        {
             foreach( $array as $k=>$v )
                $b[$k] = strtolower( $v[$subKey] );
             if( $order === 'ASC' )
                asort( $b );
             if( $order === 'DESC' )
                arsort( $b );
             foreach( $b as $key=>$val )
                $c[$key] = $array[$key];
             return $c;
        }
        return array();
    }
     
    
   /**
    * Returns the Ranking of the users using with the following criterias
    * type (truth / dare / null)
    * period (week / month / year / null)
    * level (1 / 2 / 3 / null)
    * gender (0 / 1 / null)
    * limit (integer)
    * @return array of Ranking
    */
   public static function getRanking($type=null,$period=null,$level=null,$gender=null,$limit=10)
   {
       switch($period)
       {
           case 'week':
                $dayOfWeek = CTimestamp::getDayofWeek(date('Y'),date('n'),date('d'));
                $minDate = date('Y-m-d',strtotime(date("Y-m-d") . " -" . ($dayOfWeek -1) . "day")); 
                break;
           case 'month':
                $minDate = date('Y-m-d',strtotime(date("Y-m-d") . " -" . (date('d') -1) . "day")); 
                break;
           case 'year':
                $cDateFormatter = new CDateFormatter(Yii::app()->language);
                $dayOfYear = $cDateFormatter->format("D",date('Y-m-d'));
                $minDate = date('Y-m-d',strtotime(date("Y-m-d") . " -" . ($dayOfYear) . "day"));
                break;
           default:
                $minDate = "2012-01-01";
                break;
       }   

       switch($type)
       {
           case 'truth':
                $order = "(IFNULL(SVT.score,0) + IFNULL(SCT.score,0) + IFNULL(SVCT.score,0)) DESC";
                break;
           case 'dare':
                $order = "(IFNULL(SVD.score,0) + IFNULL(SCD.score,0) + IFNULL(SVCD.score,0)) DESC";
                break;
           default:
                $order = "(IFNULL(SVT.score,0) + IFNULL(SCT.score,0) + IFNULL(SVCT.score,0) + IFNULL(SVD.score,0) + IFNULL(SCD.score,0) + IFNULL(SVCD.score,0)) DESC";
                break;
       } 

       $level = $level === null ? "(1,2,3)" : "($level)";
       $gender = $gender === null ? "(0,1)" : "($gender)";

       $query = " 
            SELECT 
              US.idUser,
              US.username,
              (IFNULL(SVT.score,0) + IFNULL(SCT.score,0) + IFNULL(SVCT.score,0) + IFNULL(SVD.score,0) + IFNULL(SCD.score,0) + IFNULL(SVCD.score,0)) AS score,
              (IFNULL(SVT.score,0) + IFNULL(SCT.score,0) + IFNULL(SVCT.score,0)) AS scoreTruth,
              (IFNULL(SVD.score,0) + IFNULL(SCD.score,0) + IFNULL(SVCD.score,0)) AS scoreDare,
              IFNULL(SVT.score,0) AS scoreVoteTruth,
              IFNULL(SVD.score,0) AS scoreVoteDare,
              IFNULL(SCD.score,0) AS scoreChallengeDare,
              IFNULL(SVCT.score,0) AS scoreVoteChallengeTruth,
              IFNULL(SVCD.score,0) AS scoreVoteChallengeDare
            FROM  User US
            LEFT JOIN
              (SELECT TR.idUser, SUM(CASE WHEN voteType = 1 THEN 1 ELSE -1 END) AS score
               FROM votingDetail VD
               INNER JOIN truth TR ON TR.idTruth = VD.idTruth
               INNER JOIN category CA ON CA.idCategory = TR.idCategory
               WHERE CA.level IN $level AND VD.voteDate >= :minDate
               GROUP BY TR.idUser) AS SVT ON SVT.idUser = US.idUser
            LEFT JOIN
              (SELECT DA.idUser, SUM(CASE WHEN voteType = 1 THEN 1 ELSE -1 END) AS score
               FROM votingDetail VD
               INNER JOIN dare DA ON DA.idDare = VD.idDare 
               INNER JOIN category CA ON CA.idCategory = DA.idCategory
               WHERE CA.level IN $level AND VD.voteDate >= :minDate
               GROUP BY DA.idUser) AS SVD ON SVD.idUser = US.idUser
            LEFT JOIN
              (SELECT CT.idUserTo, SUM(CASE WHEN voteType = 1 THEN 1 ELSE -1 END) AS score
               FROM votingDetail VD
               INNER JOIN challenge CT ON CT.idChallenge = VD.idChallenge
               INNER JOIN truth TR ON TR.idTruth = CT.idTruth 
               INNER JOIN category CA ON CA.idCategory = TR.idCategory
               WHERE CA.level IN $level AND VD.voteDate >= :minDate
               GROUP BY CT.idUserTo) AS SVCT ON SVCT.idUserTo = US.idUser
            LEFT JOIN
              (SELECT CD.idUserTo, SUM(CASE WHEN voteType = 1 THEN 1 ELSE -1 END) AS score
               FROM votingDetail VD
               INNER JOIN challenge CD ON CD.idChallenge = VD.idChallenge
               INNER JOIN dare DA ON DA.idDare = CD.idDare 
               INNER JOIN category CA ON CA.idCategory = DA.idCategory
               WHERE CA.level IN $level AND VD.voteDate >= :minDate
               GROUP BY CD.idUserTo) AS SVCD ON SVCD.idUserTo = US.idUser
            LEFT JOIN
              (SELECT CT.idUserTo, SUM(2) AS score
               FROM challenge CT
               INNER JOIN truth TR ON TR.idTruth = CT.idTruth 
               INNER JOIN category CA ON CA.idCategory = TR.idCategory
               WHERE CA.level IN $level AND CT.status = 1 AND CT.finishDate >= :minDate
               GROUP BY CT.idUserTo) AS SCT ON SCT.idUserTo = US.idUser
            LEFT JOIN
              (SELECT CD.idUserTo, SUM(5) AS score
               FROM challenge CD
               INNER JOIN dare DA ON DA.idDare = CD.idDare 
               INNER JOIN category CA ON CA.idCategory = DA.idCategory
               WHERE CA.level IN $level AND CD.status = 1 AND CD.finishDate >= :minDate
               GROUP BY CD.idUserTo) AS SCD ON SCD.idUserTo = US.idUser
            WHERE US.gender IN $gender
            ORDER BY $order
            LIMIT $limit ";

       $command = Yii::app()->db->createCommand($query);
       $command->bindParam(":minDate",$minDate,PDO::PARAM_STR);
       return $command->queryAll();
   }
   
   public static function getFirstDayWeek()
   {
       $dayOfWeek = CTimestamp::getDayofWeek(date('Y'),date('n'),date('d'));
       return date('Y-m-d',strtotime(date("Y-m-d") . " -" . ($dayOfWeek -1) . "day")); 
   }
   
   public static function getFirstDayMonth()
   {
       return date('Y-m-d',strtotime(date("Y-m-d") . " -" . (date('d') -1) . "day")); 
   }
   
   public static function getFirstDayYear()
   {
       $cDateFormatter = new CDateFormatter(Yii::app()->language);
       $dayOfYear = $cDateFormatter->format("D",date('Y-m-d'));
       return date('Y-m-d',strtotime(date("Y-m-d") . " -" . ($dayOfYear) . "day"));
   }
 
   public static function getTruthRankName($scoreTruth)
   {
        if($scoreTruth < 10)
            return 'Baby Angel';
        elseif($scoreTruth < 50)
            return 'Little Angel';
        elseif($scoreTruth < 200)
            return 'Wise Angel';
        elseif($scoreTruth < 500)
            return 'White Angel';
        elseif($scoreDare < 1000)
            return 'Golden Angel';
        elseif($scoreDare < 3000)
            return 'Black Angel';
        elseif($scoreDare < 10000)
            return 'Black Angel';
    }
 
    public static function getDareRankName($scoreDare)
    {
        if($scoreDare < 10)
            return 'Baby Imp';
        elseif($scoreDare < 50)
            return 'Little Imp';
        elseif($scoreDare < 200)
            return 'Naughty Evil';
        elseif($scoreDare < 500)
            return 'Red Evil';
        elseif($scoreDare < 1000)
            return 'Black Evil';
        elseif($scoreDare < 3000)
            return 'Black Evil';
        elseif($scoreDare < 10000)
            return 'Black Evil';
    }
 
    public static function getValueProgressBar($score)
    {
        if($score < 10)
            return 100 * $score / 10;
        elseif($score < 50)
            return 100 * ($score - 10) / 40;
        elseif($score < 200)
            return 100 * ($score - 50) / 150;
        elseif($score < 500)
            return 100 * ($score - 200) / 300;
        elseif($score < 500)
            return 100 * ($score - 500) / 500;
        elseif($score < 500)
            return 100 * ($score - 1000) / 2000;
        elseif($score < 500)
            return 100 * ($score - 3000) / 7000;
    }
   
}

?>
