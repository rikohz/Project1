<?php if(isset($labelProvince)) echo CHtml::label($labelProvince, false);
if(isset($attributeProvince)) echo CHtml::activeDropDownList(
    $model, $attributeProvince, array(), $htmlOptions) . $separtor;

if(isset($labelCity)) echo CHtml::label($labelCity, false);
if(isset($attributeCity)) echo CHtml::activeDropDownList(
    $model, $attributeCity, array(), $htmlOptions) . $separtor;

if(isset($labelArea)) echo CHtml::label($labelArea, false);
if(isset($attributeArea)) echo CHtml::activeDropDownList(
    $model, $attributeArea, array(), $htmlOptions);
