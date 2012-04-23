<?php
class MyController extends Controller
{
    function init()
    {
        parent::init();
        $app = Yii::app();
               
        if (isset($_POST['lang']))
        {            
            $app->language = $_POST['lang'];            
            $app->session['lang'] = $app->language;
        }
        else if(isset($_GET['lang']))
        {
            $app->language = $_GET['lang'];
            $app->session['lang'] = $app->language;
            $ccc = $app->session['lang'];
        }
        else if (isset($app->session['lang']))
        {
            $app->language = $app->session['lang'];
        }
    }
    
    public function render($view,$data=null,$return=false)	{		
        switch(Yii::app()->user->getLevel())
        {			
            case 1:				
                //Yii::app()->setTheme('backoffice');			
                break;			
            case 2:				
                    Yii::app()->setTheme('classic');			
                break;		            
        }		
        return parent::render($view,$data,$return);	               
    }
}
?>