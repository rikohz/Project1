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
        
        return '<li class="'.$class.'">'.CHtml::link($label,"#",array('encode'=>false,'onClick'=>
            "jQuery.ajax({'type':'POST','url':'" . $this->createPageUrl($page) . "','cache':false,'data':jQuery('#" . $this->idForm . "').serialize(),'success':function(html){jQuery('#" . $this->idDivUpdate . "').html(html)}}); return false;"
            )).'</li>';
    }        
        
}    
?>