<?php $this->pageTitle=Yii::app()->name; ?>

<!--GENERAL RANKING-->
<div style="width:150px; display:inline-block;">
    <p style="text-align:center;">GENERAL</p>
    <table width="100%">
    <?php foreach ($generalRanking as $row) { ?>
        <tr>
            <td><?php echo $row['username']; ?></td>
            <td><?php echo $row['Score']; ?></td>
        </tr>
    <?php } ?>
    </table>
</div>

<!--TRUTH RANKING-->
<div style="width:150px; display:inline-block; margin-left:100px; margin-right:100px;">
    <p style="text-align:center;">TRUTH</p>
    <table width="100%">
    <?php 
        foreach ($truthRanking as $row) { ?>
        <tr>
            <td><?php echo $row['username']; ?></td>
            <td><?php echo $row['ScoreTruth']; ?></td>
        </tr>
    <?php } ?>
    </table>
</div>

<!--DARE RANKING-->
<div style="width:150px; display:inline-block;">
    <p style="text-align:center;">DARE</p>
    <table width="100%">
    <?php 
        foreach ($dareRanking as $row) { ?>
        <tr>
            <td><?php echo $row['username']; ?></td>
            <td><?php echo $row['ScoreDare']; ?></td>
        </tr>
    <?php } ?>
    </table>
</div>