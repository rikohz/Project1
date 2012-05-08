<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/script/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/script/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />

<script type="text/javascript">
    $(document).ready(function() {
            $("a#picture").fancybox();
    });
</script>


<p>test</p>
<a id="picture" title="Lorem ipsum dolor sit amet, consectetur adipiscing elit." href="userImages/profilePicture_original/4fa64b2057cd6_original.jpg"><img src="userImages/profilePicture/4fa64b2057cd6_profile.jpg" /></a>