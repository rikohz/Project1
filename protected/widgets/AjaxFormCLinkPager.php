<?php
class AjaxFormCLinkPager extends CLinkPager {        
        /**
	 * @var string the id of the DIV to be asynchronously reloaded.
	 */
	public $idDivUpdate;
        /**
	 * @var string the id of the FORM used for criterias
	 */
	public $idForm;

    protected function createPageButton($label, $page, $class, $hidden, $selected) 
    {                       
        if($hidden || $selected)                        
            $class.=' '.($hidden ? self::CSS_HIDDEN_PAGE : self::CSS_SELECTED_PAGE);   
        
        if(isset($this->idDivUpdate) && isset($this->idForm))
            return '<li class="'.$class.'">'.CHtml::link($label,"#",array('encode'=>false,'onClick'=>
                "jQuery.ajax({'type':'POST','url':'" . $this->createPageUrl($page) . "','cache':false,'data':jQuery('#" . $this->idForm . "').serialize(),'success':function(html){jQuery('#" . $this->idDivUpdate . "').html(html)}});"
                )).'</li>';
        
//        if(!isset($this->idDivUpdate) && isset($this->idForm))
//            return '<li class="'.$class.'">'.CHtml::linkButton($label,array('encode'=>false,'submit'=>$this->createPageUrl($page))).'</li>';
        
//        if(!isset($this->idDivUpdate) && isset($this->idForm))
//            return '<li class="'.$class.'">'.CHtml::link($label,"#",array('encode'=>false,'onClick'=>"$.post('".$this->createPageUrl($page)."', $('#". $this->idForm ."').serialize());alert($('#". $this->idForm ."').serialize())")).'</li>';
        
        if(!isset($this->idDivUpdate) && !isset($this->idForm))
            return '<li class="'.$class.'">'.CHtml::link($label,$this->createPageUrl($page)).'</li>';
        
        if(!isset($this->idDivUpdate) && isset($this->idForm))
            return '<li class="'.$class.'">'.CHtml::link($label,"#",array('encode'=>false,'onClick'=>"jQuery.yii.submitForm($('#". $this->idForm ."').children(':first'),'".$this->createPageUrl($page)."',{});return false;")).'</li>';
//        
    }        
        
}    
?>