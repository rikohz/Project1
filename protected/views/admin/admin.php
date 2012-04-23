<script type="text/javascript">
$(function() {
$(".validationTruth").click(function() 
{
    var id = $(this).attr("id");
    var name = $(this).attr("name");
    var dataString = 'idTruth='+ id ;
    var parent = $(this);
    var container = $(this).parent().parent();

    if (name=='up')
    {
        $(this).fadeIn(200).html('<img src="/TruthOrDare/images/loading.gif" />');
        $.ajax({
            type: "POST",
            url: "index.php?r=truth/acceptTruth",
            data: dataString,
            cache: false,

            success: function(html)
            {
                container.html("")
                //parent.html(html);
            } 
        });
    }
    else
    {
        $(this).fadeIn(200).html('<img src="/TruthOrDare/images/loading.gif" />');
        $.ajax({
            type: "POST",
            url: "index.php?r=truth/refuseTruth",
            data: dataString,
            cache: false,

            success: function(html)
            {container.html("")}
        });
    }
    return false;
});


$(".validationDare").click(function() 
{
    var id = $(this).attr("id");
    var name = $(this).attr("name");
    var dataString = 'idDare='+ id ;
    var container = $(this).parent().parent();

    if (name=='up')
    {
        $(this).fadeIn(200).html('<img src="/TruthOrDare/images/loading.gif" />');
        $.ajax({
            type: "POST",
            url: "index.php?r=dare/acceptDare",
            data: dataString,
            cache: false,

            success: function(html)
            {
                container.html("")
                //parent.html(html);
            } 
        });
    }
    else
    {
        $(this).fadeIn(200).html('<img src="/TruthOrDare/images/loading.gif" />');
        $.ajax({
            type: "POST",
            url: "index.php?r=dare/refuseDare",
            data: dataString,
            cache: false,

            success: function(html)
            {container.html("")}
        });
    }
    return false;
});

});
</script>

<?php
$this->breadcrumbs=array(
	'Validation',
);?>

<br />
TRUTH
<?php foreach ($dataTruth as $row) { ?>
    <br />
    <div>
        <p align="center"><?php echo $row['truth']; ?></p>
        <p align="center">
            <a href="" class="validationTruth" id="<?php echo $row['idTruth']; ?>" name="up">Approve</a>
            <a href="" class="validationTruth" id="<?php echo $row['idTruth']; ?>" name="down">Refuse</a>
        </p>
    </div>
    <br />
<?php } ?>
DARE
<?php foreach ($dataDare as $row) { ?>
    <br />
    <div>
        <p align="center"><?php echo $row['dare']; ?></p>
        <p align="center">
            <a href="" class="validationDare" id="<?php echo $row['idDare']; ?>" name="up">Approve</a>
            <a href="" class="validationDare" id="<?php echo $row['idDare']; ?>" name="down">Refuse</a>
        </p>
    </div>
    <br />
<?php } ?>