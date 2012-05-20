<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/script/jquery-ui-1.8.18.custom.min.js"></script>
<style>
#dialog label, #dialog input { display:block; }
#dialog label { margin-top: 0.5em; }
#dialog input, #dialog textarea { width: 95%; }
#tabs { margin-top: 1em; }
#tabs li .ui-icon-close { float: left; margin: 0.4em 0.2em 0 0; cursor: pointer; }
#add_tab { cursor: pointer; }
.public {
    height:18px;
    width:49px;
    background-image: url(<?php echo Yii::app()->request->baseUrl; ?>/images/PublicPrivate.png);
    background-position: 0% 33%;
    float:right;
}
.private {
    height:18px;
    width:55px;
    background-image: url(<?php echo Yii::app()->request->baseUrl; ?>/images/PublicPrivate.png);
    background-position: 0% 97%;
    float:right;
}
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

    // actual addTab function: adds new tab using the title input from the form above
    function addTab() {
        var tab_title = $tab_title_input.val() || "List " + tab_counter;
        $.ajax({
            type: "POST",
            url: "index.php?r=user/addUserList",
            data: {
                'name':tab_title,
                'public':document.getElementById('UserList_public').checked
            },
            cache: false,

            success: function(html)
            {
                if(html === "SUCCESS")
                {
                    $tabs.tabs( "add", "#tabs-" + tab_counter, tab_title );
                    tab_counter++;
                }
            } 
        });
    }

    // addTab button: just opens the dialog
    $( "#add_tab" )
            .button()
            .click(function() {
                    $dialog.dialog( "open" );
            });

//      <!--*********************-->
//      <!-- Delete list -->
//      <!--*********************-->
    var idUserList = null;
    var index = null;

    $("#tabs span.ui-icon-close").live( "click", function() {
        idUserList = $(this).attr("id");
        index = $( "li", $tabs ).index( $( this ).parent() );
        $( "#dialog-confirm-delete-userList" ).dialog("open");
    });

    $("#dialog-confirm-delete-userList").dialog({
        autoOpen: false,
        resizable: false,
        modal: true,
        buttons: {
            "Delete": function() {
                $.ajax({
                    type: "POST",
                    url: "index.php?r=user/deleteUserList",
                    data: {'idUserList':idUserList},
                    cache: false,

                    success: function(html)
                    {
                        if(html === "SUCCESS")
                            $tabs.tabs( "remove", index );
                    } 
                }); 
                $( this ).dialog( "close" );
            },
            Cancel: function() {
                    $( this ).dialog( "close" );
            }
        }
    });


//      <!--*********************-->
//      <!-- Delete content list -->
//      <!--*********************-->
    $(".deleteUserListContent").click(function() 
    {
        var idUserListContent = $(this).attr("id");
        var container = $(this).parent().parent();

        $.ajax({
            type: "POST",
            url: "index.php?r=user/deleteUserListContent",
            data: {'idUserListContent':idUserListContent},
            cache: false,

            success: function(html)
            {
                if(html === "SUCCESS")
                    container.html("");
            } 
        });
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
//  <!--******************-->
//  <!-- Public / Private -->
//  <!--******************-->
var showPublic = true;
var showPrivate = true;
function selectPublic(){
    showPublic = !showPublic;
    filterPublicPrivate();
}
function selectPrivate(){
    showPrivate = !showPrivate;
    filterPublicPrivate();
}
function filterPublicPrivate(){
    document.getElementById('public').style.backgroundPosition = showPublic === true ? "0% 33%" : "0% 0%";
    document.getElementById('private').style.backgroundPosition = showPrivate === true ? "0% 97%" : "0% 66%";
}
</script>
</head>
<body>

<?php 
  @session_start();  
  
  $this->pageTitle=Yii::app()->name . ' - My Favorites';
  $this->breadcrumbs=array(
        'My Page'=>array('user/myPage'),
	'My Favorites',
  );
?>

<button id="add_tab">Create List</button>

<div style="float:right; width:60px">
    <div class="public" id="public" onClick="selectPublic()">&nbsp;</div>
    <div class="private" id="private" onClick="selectPrivate()">&nbsp;</div>
</div>

<div id="tabs">
    <ul>
        <?php $i = 0; foreach($userlists as $row): ?>
            <li><a href="#tabs-<?php echo $i; ?>"><?php echo $row->name; ?></a> <span id="<?php echo $row->idUserList; ?>" class="ui-icon ui-icon-close">Remove Tab</span></li>
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
                    <div style="float:right;"><a class="deleteUserListContent" id="<?php echo $content->idUserListContent; ?>">Delete</a></div>
                    <div style="float:right; margin-right: 30px;"><a class='challenge' id='C<?php echo $initial . $content->$type->$idName; ?>'>Challenge</a></div>
                    <div style="background-color: #DDD"><?php echo $content->$type->$type; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php $i++; endforeach; ?>
</div>


<!--********************************-->
<!-- Dialog box for adding UserList -->
<!--********************************-->
<div id="dialog" title="Tab data">
    <form>
        <fieldset class="ui-helper-reset">
            <label for="tab_title">Name</label>
            <input type="text" name="tab_title" id="tab_title" value="" class="ui-widget-content ui-corner-all" />
            <label for="UserList_private">Public</label>
            <?php echo CHtml::checkBox('UserList_public',false, array('id'=>'UserList_public')); ?>
        </fieldset>
    </form>
</div>

<!--*******************************-->
<!-- Dialog box to delete UserList -->
<!--*******************************-->
<div id="dialog-confirm-delete-userList" title="Delete list">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Are you sure you want to delete this list?</p>
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