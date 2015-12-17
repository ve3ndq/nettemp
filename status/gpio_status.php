<?php 
$root=$_SERVER["DOCUMENT_ROOT"];
$dir="modules/gpio/";
$db = new PDO("sqlite:$root/dbf/nettemp.db") or die ("cannot open database");
$sth = $db->prepare("select * from gpio where mode='trigger' or mode='simple' or mode='day' or mode='week' or mode='temp' or mode='call' ");
$sth->execute();
$result = $sth->fetchAll();
$numRows = count($result);
?>
<?php if ( $numRows > '0' ) { ?>
<div class="grid-item gs">
<div class="panel panel-default">
            <div class="panel-heading">GPIO</div>
<table class="table table-hover table-condensed">
<?php
foreach ( $result as $a) {
$gpio=$a['gpio'];
?>
    <tr <?php echo $a['status'] == 'ALARM' ? 'class="danger"' : '' ?>>
	<td >
	    <img type="image" src="media/ico/SMD-64-pin-icon_24.png" />
	</td>
	<td >
	    <small>
		<?php echo $a['name']; ?>
	    <small>
	</td>
	<td >
	    <small>
		<?php echo $a['mode']; ?>
	    </small>
	</td>
	<td >
	    <small>
		<?php echo $a['status']; ?>
	    </small>
	</td>
    </tr>
<?php
}

?>
</table>
</div>
</div>
<?php }  ?>
