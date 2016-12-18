<style type="text/css">

* {
  -webkit-box-sizing: border-box;
          box-sizing: border-box;
}


/* ---- grid ---- */

.grid {
	<?php if($id=='screen') { ?>
   	width: 800px;
   <?php } ?>
}

/* clearfix */
.grid:after {
  content: '';
  display: block;
  clear: both;
}

/* ---- grid-item ---- */

.grid-item {
    width: 340px;
    float: left;
    border-radius: 5px;
}

</style>



<div class="grid">
    <div class="grid-sizer"></div>
    <?php
    include('status/sensor_status.php');
    
    //GROUPS
    $rows = $db->query("SELECT ch_group FROM sensors");
	$result_ch_g = $rows->fetchAll();
	
	foreach($result_ch_g as $uniq) {
		if(!empty($uniq['ch_group'])&&$uniq['ch_group']!='none') {
			$unique[]=$uniq['ch_group'];
		}
	}
	
	$rowu = array_unique($unique);
	$group_num=count($rowu);
	$groupc=0;
	foreach ($rowu as $ch_g) { 
		include('status/sensor_groups.php');
		$groupc++;
	}
	//END GROUPS
	
    include('status/justgage_status.php');
    include('status/minmax_status.php');
    include('status/hosts_status.php');
    include('status/gpio_status.php');
    include('status/counters_status.php');
    include('status/relays_status.php');
    include('status/switch_status.php');
    include('status/meteo_status.php');
    foreach (range(1, 10) as $v) {
		$ow=$v;
		include('status/ownwidget.php');
    }
    include('status/ipcam_status.php');
    include('status/ups_status.php');
    ?>
</div>

<script type="text/javascript">
    setInterval( function() {
    $('.ss').load("status/sensor_status.php");
    <?php
		foreach ($rowu as $key => $ch_g) { 
	?>
		$('.sg<?php echo $key?>').load("status/sensor_groups.php?chg=<?php echo $ch_g?>");
	<?php
		}
	?>
    $('.co').load("status/counters_status.php");
    $('.gs').load("status/gpio_status.php");
    $('.hs').load("status/hosts_status.php");
    $('.rs').load("status/relays_status.php");
    $('.rss').load("status/switch_status.php");
    $('.ms').load("status/meteo_status.php");
    $('.ow2').load("status/ownwidget2.php");
    $('.ow3').load("status/ownwidget3.php");
    $('.mm').load("status/minmax_status.php");
    $('.ups').load("status/ups_status.php");
    $('#justgage_refresh').load("status/justgage_refresh.php");
}, 60000);

$(document).ready( function() {

  $('.grid').masonry({
    itemSelector: '.grid-item',
    columnWidth: 350
  });
  
});
</script>
<script src="html/masonry/masonry.pkgd.min.js"></script>
<div id="justgage_refresh"></div>

