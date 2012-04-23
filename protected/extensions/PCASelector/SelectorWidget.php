<?php

/*
 * !!! WARNING: ONLY CHINA MAINLAND AND HONGKONG ARE SUPPORTED !!!
 *
 * This widget render three dropDownlist which used to define the place 
 * cotained province, city, area. When the first dropDownlist changed, the 
 * second will changed automaticly, so does the third one;
 *
 * If one of province, city or area attribute is not set, there will no 
 * related dropdown list rendered.
 *
 * You can call this widget like this in any view files:
 *
 * @author: francis
 * @email : francis.tm@gmail.com
 * @blog  : http://blog.francistm.com
 * @usage:
 *
 * $this->widget('extension.PCASelector.SelectorWidget', array(
 *     'model' => $someCFormModel,
 *     'attributeCity' => 'city',
 *     'attributeArea' => 'area',
 *     'attributeProvince' => 'province',
 *
 *     'labelCity' => 'City',
 *     'labelArea' => 'Area',
 *     'labelProvince' => 'Province',
 *
 *     'selectSepartor' => '<br />',
 *     'htmlOptions' => array(
 *         'class' => 'positionSelector',
 *      ),
 *  ));
 *
 * After you call method like this, will generate three dropDownlist which 
 * separtored by '<br />' and each one has a html option class which valued 
 * positionSelctor.
 *
 * If labelCity, Area, Province are not defined, there will not render any 
 * label.
 * 
 */

class SelectorWidget extends CWidget
{
    public $model;
    public $attributeCity;
    public $attributeArea;
    public $attributeProvince;

    public $htmlOptions;
    public $selectSepartor;
    public $labelCity, $labelArea, $labelProvince;
    
    protected $assetsPath, $widgetId;

    public function init()
    {
        $assetsPath = dirname(__FILE__) .
                      DIRECTORY_SEPARATOR . 'assets';

        $this->htmlOptions = isset($this->htmlOptions) ?
            $this->htmlOptions : array();

        $this->selectSepartor = isset($this->selectSepartor) ? 
            $this->selectSepartor : '';

        $this->assetsPath = Yii::app()->getAssetManager()
                                      ->publish($assetsPath);

        $nameList = array();
        $attributeList = array();
        $dropListInit = 'new PCAS(';

        $attrCity = $this->attributeCity;
        $attrArea = $this->attributeArea;
        $attrProvince = $this->attributeProvince;

        $modelName = get_class($this->model);

        if(isset($this->attributeProvince)) {
            $nameList[] = '"' . $modelName . '[' . $this->attributeProvince . ']' . '"';
            $attributeList[] = '"' . $this->model->$attrProvince . '"';
        }
        if(isset($this->attributeCity)) {
            $nameList[] = '"' . $modelName . '[' . $this->attributeCity . ']' . '"';
            $attributeList[] = '"' . $this->model->$attrCity . '"';
        }
        if(isset($this->attributeArea)) {
            $nameList[] = '"' . $modelName . '[' . $this->attributeArea . ']' . '"';
            $attributeList[] = '"' . $this->model->$attrArea . '"';
        }

        $initArray = array_merge($nameList, $attributeList);
        $dropListInit .= implode(', ', $initArray) . ');';

        Yii::app()->clientScript
                  ->registerScriptFile($this->assetsPath . '/PCAS.js');
        Yii::app()->clientScript
                  ->registerScript(__CLASS__ . '#' . uniQId(), $dropListInit);

        parent::init();
    }

    public function run()
    {
        $this->render('selectorWidget', array(
            'model' => $this->model,
            'attributeCity' => $this->attributeCity,
            'attributeArea' => $this->attributeArea,
            'attributeProvince' => $this->attributeProvince,

            'separtor' => $this->selectSepartor,
            'htmlOptions' => $this->htmlOptions,
        ));
    }
}
