<?php if(!isset($_SESSION['user'])){ header("Location: denied"); } ?>
<script type="text/JavaScript">
function timedRefresh(timeoutPeriod) {
    setTimeout("location.reload(true);",timeoutPeriod);
    }
</script>
<body onload="JavaScript:timedRefresh(60000);">

<?php
$label='';
$need_id = isset($_POST['need_id']) ? $_POST['need_id'] : '';
$need_dst = isset($_POST['need_dst']) ? $_POST['need_dst'] : '';
$x = isset($_POST['x']) ? $_POST['x'] : '';
$y = isset($_POST['y']) ? $_POST['y'] : '';
if (!empty($need_id)){
$pos="{left:".$x.", top:".$y."}";
/*if ($need_dst=='hosts') {
    $dbs = new PDO('sqlite:dbf/nettemp.db');
}
else {*/
    $dbmaps = new PDO('sqlite:dbf/nettemp.db');
//}
$dbmaps->exec("UPDATE maps SET map_pos='$pos' WHERE map_num='$need_id'");
header("location: " . $_SERVER['REQUEST_URI']);
exit();
}
?>

  <link rel="stylesheet" href="html/jquery/jquery-ui.css">
  <script src="html/jquery/jquery-ui.js"></script>


<style type="text/css">
  .draggable {
      width: 1px;
      height: 1px;
      //padding: 0.5em;
      float: left;
      margin: 0px;
      cursor: move;
      border: 0px;
  }
  .draggable, a {
      cursor:move;
  }
  #draggable, .draggable2 {
      margin-bottom:20px;
      cursor:move;
  }
  #draggable {
      cursor: move;
  }
  #draggable {
      cursor: e-resize;
  }
  #content {
      width: 1140px;
      height: 600px;
      border:2px solid #ccc;
      padding: 2px;
<?php 
    if (file_exists("map.jpg")) { ?>
      background: url("map.jpg") left top;
<?php 
    } else { ?>
    background: url("map_example.jpg") left top;
<?php
    }
?>
      //background-size: cover;
      background-repeat: no-repeat;
  }
  h3 {
      clear: left;
  }
  .draggable.ui-draggable-dragging { 
	//padding: 2px;
	
    }
</style>


<script>
<?php
$array = array();
$dirn = "sqlite:dbf/nettemp.db";
$dbn = new PDO($dirn) or die("cannot open database");
$dbmaps = new PDO('sqlite:dbf/nettemp.db');

$query = "select map_num,map_pos FROM maps";//sensors";
$dbn->query($query);
foreach ($dbmaps->query($query) as $row) {
	$array[$row[0]]=$row[1];
    }
$js_array = json_encode($array);
$js_array = str_replace('"','', $js_array);
echo "var elements = ".$js_array.";\n";

unset($query);
unset($js_array);
unset($array);

?>

var sensors = JSON.stringify(elements);
var sensors = JSON.parse(sensors);


var id = 0
//alert(JSON.stringify(positions, null, 4));
$(function() {

if (elements != null) {
$.each(elements, function (id, pos) {
        $("#data-need" + id).css(pos)
    })
}


$( "#content div" ).draggable({
    containment: '#content',
    stack: "#content div",
    scroll: false,

      stop: function(event, ui) {

	var pos_x = ui.position.left;
        var pos_y = ui.position.top;
          var need = ui.helper.data("need");
	  var dst = ui.helper.data("dst");
          $.ajax({
              type: "POST",
              url: "",
              data: { x: pos_x, y: pos_y, need_id: need, need_dst: dst }
            });

        }
    });
});

</script>
<div id="content">
<?php
$rows = $dbmaps->query("SELECT * FROM maps WHERE map_on='on' AND type='sensors'");
$row = $rows->fetchAll();
foreach ($row as $b) {
	$rows=$dbn->query("SELECT * FROM sensors WHERE id='$b[element_id]'");//always one record
	$a=$rows->fetchAll();
	$a=$a[0];//extracting from array
	
	if($a['type'] == 'lux'){ $unit='lux'; $type='<img src="media/ico/sun-icon.png"/>';} 
	if($a['type'] == 'temp'){ $unit='&#8451'; $type='<img src="media/ico/temp2-icon.png"/>';}
	if($a['type'] == 'humid'){ $unit='%'; $type='<img src="media/ico/rain-icon.png"/>';}
	if($a['type'] == 'press'){ $unit='hPa'; $type='<img src="media/ico/Science-Pressure-icon.png"/>';}
	if($a['type'] == 'water'){ $unit='m3'; $type='<img src="media/ico/water-icon.png"/>';}
	if($a['type'] == 'gas'){ $unit='m3'; $type='<img src="media/ico/gas-icon.png"/>';}
	if($a['type'] == 'elec'){ $unit='kWh'; $type='<img src="media/ico/Lamp-icon.png"/>';}
	if($a['type'] == 'watt'){ $unit='W'; $type='<img src="media/ico/watt.png" alt="Watt"/>';}
	if($a['type'] == 'volt'){ $unit='V'; $type='<img src="media/ico/volt.png" alt="Volt" /> ';}
	if($a['type'] == 'amps'){ $unit='A'; $type='<img src="media/ico/amper.png" alt="Amps"/> ';}
	if($a['type'] == 'dist'){ $unit='cm'; $type='<img src="media/ico/Distance-icon.png" alt="cm"/> ';}
	
	//Jesli w��czone to wy�wietlamy nazw� inaczej pusty ci�g
	$sensor_name='';
	$transparent_bkg='';
	$background_color='';
	$background_low='';
	$background_high='';
	$font_color='';
	$font_size='';
	$label_class='';
	if($b['display_name'] == 'on')	$sensor_name=$a['name'];
	if($b['transparent_bkg'] == 'on') $transparent_bkg='transparent-background';
	if($b['background_color'] != '') $background_color="background:".$b['background_color'];
	if($b['background_low'] != '') $background_low="background:".$b['background_low'];
	if($b['background_high'] != '') $background_high="background:".$b['background_high'];
	if($b['font_color'] != '') $font_color="color:".$b['font_color'];
	if($b['font_size'] != '') $font_size="font-size:".$b['font_size']."%";
?>
<div data-need="<?php echo $b['map_num']?>" id="<?php echo "data-need".$b['map_num']?>" data-dst="sensors" 
											class="ui-widget-content draggable" 
											title="<?php echo $a['name'].' - Last update'.$a['time']; ?>" 
											ondblclick="location.href='index.php?id=view&type=temp&max=day&single=<?php echo $a['name']; ?>'">
    <?php 
			$display_style='style=""';
			if(($a['tmp'] == 'error') || ($label=='danger') || ($a['tmp'] == 'wait')) {
				//echo '<span class="label label-danger label-sensors">';
				$label_class="label-danger";
		    } 
			elseif (($a['type'] == 'temp') && ($a['alarm'] == 'on') && ($a['tmp']  < $a['tmp_min']))
			{
				$type='<img src="media/ico/temp_low.png"/>';
				$label_class="label-to-low";
				$background_color=$background_low;
				//echo '<span class="label label-to-low label-sensors">';
			}
			elseif (($a['type'] == 'temp') && ($a['alarm'] == 'on') && ($a['tmp']  > $a['tmp_max']))
			{
				$type='<img src="media/ico/temp_high.png"/>';
				$label_class="label-to-high";
				$background_color=$background_high;
				//echo '<span class="label label-to-high label-sensors">';
			}
		    else 
			{
				$label_class=$transparent_bkg.' label-sensors';
				//$background_color='';
				//echo '<span class="'.$transparent_bkg.' label label-success">';
		    } 
			echo '<span class="label '.$label_class.'" style="'.$background_color.';'.$font_size.';'.$font_color.'">';
			if ((is_numeric($a['tmp']) && (($a['type'])=='elec')))  {
			echo 	$type." ".$sensor_name." ".number_format($a['tmp'], 3, '.', ',')." ".$unit;
		    } 
		    elseif (is_numeric($a['tmp'])) { 
			echo 	$type." ".$sensor_name." ".number_format($a['tmp'], 1, '.', ',')." ".$unit;
		    }
		    else {
			echo $type." ".$sensor_name." ".$a['tmp']." ".$unit;
		    }

	?>
    </span>
</div>
<?php 
    }
unset($a);
unset($row);
unset($rows);
?>

<?php
$rows = $dbmaps->query("SELECT * FROM maps WHERE type='gpio' AND map_on='on'");
$row = $rows->fetchAll();
foreach ($row as $b) {
	$rows=$dbn->query("SELECT * FROM gpio WHERE id='$b[element_id]'");//always one record
	$a=$rows->fetchAll();
	$a=$a[0];//extracting from array
	$icon='';
	if($b['icon'] != '')
	{
		$icon=$b['icon'];
	}
	switch ($icon){
		case 'Light':
			$device='<img src="media/ico/Lamp-icon.png" />';
			break;
		case 'Socket':
			$device='<img src="media/ico/Socket-icon.png" />';
			break;
		case 'Switch':
			$device='<img src="media/ico/Switch-icon.png" />';
			break;
		default:
			$device='<img src="media/ico/SMD-64-pin-icon_24.png" />';
	}
	if (($a['mode'] != 'dist') && ($a['mode'] != 'humid')) {
?>
<div data-need="<?php echo $b['map_num']?>" id="<?php echo "data-need".$b['map_num']?>" data-dst="gpio" class="ui-widget-content draggable"title="<?php echo $a['name']; ?>">
    <?php if(($a['status'] == 'error') || ($a['status'] == 'OFF') || ($label=='danger')) {
		    echo '<span class="label label-danger">';
		    } 
		    else {
		    echo '<span class="label label-success">';
		    }
	        ?>

    <?php 
		//Je�li w��czone to wy�wietlamy nazw� i status przeciwnie tylko status
		if ($b['display_name'] == 'on') {
		echo $device." ".$a['name']." ".$a['status'];
		}
		else
		{
			echo $device." ".$a['status'];
		}
		?>
	<?php
		if ($a['mode'] == 'simple' && $b['control_on_map'] == 'on'){
			 $gpio_post= $_POST['gpio'];
			 include('modules/gpio/html/gpio_simple.php');
		}
		elseif ($a['mode'] == 'time' && $b['control_on_map'] == 'on'){
			$gpio_post= $_POST['gpio'];
			include('modules/gpio/html/gpio_time.php');
		}
		elseif ($a['mode'] == 'moment' && $b['control_on_map'] == 'on'){
			$gpio_post= $_POST['gpio'];
			include('modules/gpio/html/gpio_moment.php');
		}
		elseif ($a['mode'] == 'control' && $b['control_on_map'] == 'on'){
			$gpio_post= $_POST['gpio'];
			include('modules/gpio/html/gpio_control.php');
		}
	?>
    </span>
</div>
<?php 
	}//end if
    }
unset($a);
?>

<?php
$dbh = new PDO("sqlite:dbf/nettemp.db");
$rows = $dbmaps->query("SELECT * FROM maps WHERE map_on='on' AND type='hosts'");
$row = $rows->fetchAll();
foreach ($row as $b) {
	$rows=$dbh->query("SELECT * FROM hosts WHERE id='$b[element_id]'");//always one record
	$h=$rows->fetchAll();
	$h=$h[0];//extracting from array
    $device='<img src="media/ico/Computer-icon.png" />';
	if($b['icon'] != '')
	{
		$icon=$b['icon'];
	}
	switch ($icon){
		case 'Host':
			$device='<img src="media/ico/Computer-icon.png" />';
			break;
		case 'Camera':
			$device='<img src="media/ico/Eye-icon.png" />';
			break;
		case 'Printer':
			$device='<img src="media/ico/Mail-icon.png" />';
			break;
		case 'Raspberry':
			$device='<img src="media/ico/raspberry-icon.png" />';
			break;
		default:
			$device='<img src="media/ico/SMD-64-pin-icon_24.png" />';
	}
?>
<div data-need="<?php echo $h['map_num']?>" id="<?php echo "data-need".$h['map_num']?>" data-dst="hosts" class="ui-widget-content draggable">
    <?php 
	if(($h['status'] == 'error') || ($h['last']== 0)) {
		    echo '<span class="label label-danger">';
		    } 
		    else {
		    echo '<span class="label label-success">';
		    }
	        ?>

    <?php echo $device." ".$h['name']?>
     </span>
</div>
<?php 
    }
unset($h);
?>
</div>

