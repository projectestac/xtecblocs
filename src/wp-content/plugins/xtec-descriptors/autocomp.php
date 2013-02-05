<?php
/*
 * Suggest descriptors stored in data base
 * author: XTECBlocs
 * param: a string with the descriptor entry
 * return: A list of strings
*/

if(!isset($_GET['sstring']) || $_GET['sstring']==''){
	return;
}
//Get descriptors from database
include_once('../../../wp-config.php');
include_once('../../../wp-includes/wp-db.php');
$descriptorsArray = array(); // create an array to hold tag code		
// Pull in tag data		
$tags = $wpdb->get_results("SELECT descriptor FROM wp_descriptors WHERE descriptor like '$_GET[sstring]%' ORDER BY descriptor");
$tags1 =  $wpdb->get_results("SELECT descriptor FROM wp_descriptors_pre WHERE descriptor like '$_GET[sstring]%' ORDER BY descriptor");
$arr=array();
for($i=0;$i<count($tags);$i++){
	$arr[] = $tags[$i]->descriptor;
}

for($i=0;$i<count($tags1);$i++){
	if (!in_array($tags1[$i]->descriptor,$arr)){
		$arr[] = $tags1[$i]->descriptor;
	}
}

foreach($arr as $a){?>
	<div style="width: 200px; padding:4px; height:14px; background:#EEEEEE;" onMouseOver="this.style.background='#CCCCCC'" onMouseOut="this.style.background='#EEEEEE'" onClick="setvalue('<?php echo $a;?>')"><?php echo $a?></div>
<?php } ?>
