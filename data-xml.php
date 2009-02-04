<?php
/*
include("$sl_path/cache/cacher.php"); 

var $total_xml;
function cache_xml($buffer) {
	global $total_xml;
	$total_xml=$buffer;
	return $buffer;
}
ob_start("cache_xml"); */

header("Content-type: text/xml");
include("database-info.php");

// Opens a connection to a MySQL server
$connection=mysql_connect ($host, $username, $password);
if (!$connection) {
  die('Not connected : ' . mysql_error());
}

// Set the active MySQL database
$db_selected = mysql_select_db($database, $connection);
if (!$db_selected) {
  die ('Can\'t use db : ' . mysql_error());
}

// Select all the rows in the markers table
$query = "SELECT sl_address, sl_store, sl_city, sl_state, sl_zip, sl_latitude, sl_longitude, sl_description, sl_url, sl_hours, sl_phone, sl_image FROM ".$wpdb->prefix."store_locator";

$result = mysql_query($query);
if (!$result) {
  die('Invalid query: ' . mysql_error());
}

// Start XML file, echo parent node
echo "<markers>\n";
// Iterate through the rows, printing XML nodes for each
while ($row = @mysql_fetch_assoc($result)){
  // ADD TO XML DOCUMENT NODE
  echo '<marker ';
  echo 'name="' . parseToXML($row['sl_store']) . '" ';
  echo 'address="' . parseToXML($row['sl_address']) . ', '. parseToXML($row['sl_city']). ', ' .parseToXML($row['sl_state']).' ' .parseToXML($row['sl_zip']).'" ';
  echo 'lat="' . $row['sl_latitude'] . '" ';
  echo 'lng="' . $row['sl_longitude'] . '" ';
  echo 'distance="' . $row['sl_distance'] . '" ';
  echo 'description="' . comma($row['sl_description']) . '" ';
  echo 'url="' . $row['sl_url'] . '" ';
  echo 'hours="' . comma($row['sl_hours']) . '" ';
  echo 'phone="' . $row['sl_phone'] . '" ';
  echo 'image="' . $row['sl_image'] . '" ';
  echo "/>\n";
}

// End XML file
echo "</markers>\n";

/*include("$sl_path/cache/cacher-end.php"); 
*/
?>

