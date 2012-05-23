<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/script/jquery-ui-1.8.18.custom.min.js"></script>
<style>
#dialog label, #dialog input { display:block; }
#dialog label { margin-top: 0.5em; }
#dialog input, #dialog textarea { width: 95%; }
#tabs { margin-top: 1em; }
#tabs li .ui-icon-close { float: left; margin: 0.4em 0.2em 0 0; cursor: pointer; }
#add_tab { cursor: pointer; }
</style>
<script>
$(function() {
    var $tab_title_input = $( "#tab_title"),
            $tab_content_input = $( "#tab_content" );
    var tab_counter = 2;

    // tabs init with a custom tab template and an "add" callback filling in the content
    var $tabs = $( "#tabs").tabs({
            tabTemplate: "<li><a href='#{href}'>#{label}</a> <span class='ui-icon ui-icon-close'>Remove Tab</span></li>",
            add: function( event, ui ) {
                    $( ui.panel ).append( "<p>&nbsp;</p>" );
            }
    });

    // modal dialog init: custom buttons and a "close" callback reseting the form inside
    var $dialog = $( "#dialog" ).dialog({
            autoOpen: false,
            modal: true,
            buttons: {
                    Add: function() {
                            addTab();
                            $( this ).dialog( "close" );
                    },
                    Cancel: function() {
                            $( this ).dialog( "close" );
                    }
            },
            open: function() {
                    $tab_title_input.focus();
            },
            close: function() {
                    $form[ 0 ].reset();
            }
    });

    // addTab form: calls addTab function on submit and closes the dialog
    var $form = $( "form", $dialog ).submit(function() {
            addTab();
            $dialog.dialog( "close" );
            return false;
    });

//  <!--****************-->
//  <!-- Send Challenge -->
//  <!--****************-->
    var idDare = null;
    var idTruth = null;
    $("#dialog-form-challenge-sent").dialog({autoOpen: false});
    $("#dialog-form-challenge-alreadyexists").dialog({autoOpen: false});

    $( ".challenge" ).click(function() {
        if($(this).attr("id").substring(0, 2) == "CT")
        {
            idTruth = $(this).attr("id").substring(2, $(this).attr("id").length);
            idDare = null;
            $("#dialog-form-challenge").dialog('option', 'title', 'Challenge Truth #'+idTruth); 
            $("#dialog-form-challenge").dialog( "open" );  
        }
        if($(this).attr("id").substring(0, 2) == "CD")
        {
            idDare = $(this).attr("id").substring(2, $(this).attr("id").length);
            idTruth = null;
            $("#dialog-form-challenge").dialog('option', 'title', 'Challenge Dare #'+idDare); 
            $("#dialog-form-challenge").dialog( "open" );  
        }
    });
    
    $( "#dialog-form-challenge" ).dialog({
            autoOpen: false,
            height: 300,
            width: 350,
            context: $(this), 
            modal: true,
            buttons: {
                "Validate": function() {    
                    if ( $( "#Challenge_idUser" ).val() !== '' ) {
                        var datastring = idTruth === null ? 'idDare='+idDare : 'idTruth='+idTruth;
                        datastring = datastring + "&idUser="+$( "#Challenge_idUser" ).val();
                        datastring = datastring + "&private="+document.getElementById('Challenge_private').checked;
                        datastring = datastring + "&comment="+$( "#Challenge_comment" ).val(),
                        $.ajax({ 
                          url: "index.php?r=user/sendChallenge", 
                          type: "POST", 
                          data: datastring, 
                          success: function(result){ 
                              if(result == "SUCCESS"){
                                $("#dialog-form-challenge" ).dialog( "close" );
                                $("#Challenge_idUser").val('');
                                $("#Challenge_private").checked = 0;
                                $("#Challenge_comment").val('');
                                $("#dialog-form-challenge-sent").dialog( "open" );
                              }
                              if(result == "ALREADY_EXISTS"){
                                $("#dialog-form-challenge-alreadyexists").dialog( "open" );
                              }
                            } 
                        });  
                    }
                },
                Cancel: function() {
                        $( this ).dialog( "close" );
                }
            }
    });
});
</script>
</head>
<body>

<?php 
  @session_start();  
  
  $this->pageTitle=Yii::app()->name . ' - My Favorites';
  $this->breadcrumbs=array(
        User::getUsernameFromId($idUser) . " Page"=>array("user/userPage&idUser=$idUser"),
	'User Favorites',
  );
?>

<div id="tabs">
    <ul>
        <?php $i = 0; foreach($userlists as $row): ?>
            <li><a href="#tabs-<?php echo $i; ?>"><?php echo $row->name; ?></a></li>
        <?php $i++; endforeach; ?>
    </ul>
    <?php $i = 0; foreach($userlists as $row): ?>
        <div id="tabs-<?php echo $i; ?>">
            <?php foreach($row->userListContents as $content): ?>
                <?php $type = $content->truth === null ? "dare" : "truth"; ?>
                <?php $initial = $content->truth === null ? "D" : "T"; ?>
                <?php $idName = $content->truth === null ? "idDare" : "idTruth"; ?>
                <div style="margin-bottom:20px;">
                    <div style="display:inline-block;"><?php echo ucfirst($type); ?> #<?php echo $content->$type->$idName; ?> - <?php echo $content->$type->category->category; ?></div>
                    <div style="float:right; margin-right: 30px;"><a class='challenge' id='C<?php echo $initial . $content->$type->$idName; ?>'>Challenge</a></div>
                    <div style="background-color: #DDD"><?php echo $content->$type->$type; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php $i++; endforeach; ?>
</div>

<!--******************************-->
<!-- Dialog box to send Challenge -->
<!--******************************-->
<div id="dialog-form-challenge" style="font-size:0.8em;" title="Send Challenge">
    <?php echo CHtml::dropDownList('FriendChallenge_username',null, $friends, array('prompt'=>'Select Friend','style'=>'width:330px;','id'=>'Challenge_idUser')); ?>
    <br />
    Comment:
    <textarea rows="4" cols="50" id="Challenge_comment"></textarea>
    <br />Private: 
    <?php echo CHtml::checkBox('ChallengeFriend_private',false, array('id'=>'Challenge_private')); ?>
    <?php if($friends == null): ?>
        <br /><br /><p style='color:red;'>You haven't any friend yet!</p> 
    <?php endif; ?>
</div>

<div id="dialog-form-challenge-sent">
    <p>Challenge sent!</p>
</div>

<div id="dialog-form-challenge-alreadyexists">
    <p>This user already played this challenge or has it in his/her waiting list!</p>
</div>