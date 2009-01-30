<div class='wrap'>
<h2>Add Locations</h2><br>
<?php
global $wpdb;
initialize_variables();

//Inserting addresses by manual input
if ($_POST[sl_store] && $_GET[mode]!="pca") {
	foreach ($_POST as $key=>$value) {
		if (ereg("sl_", $key)) {
			$fieldList.="$key,";
			$value=comma($value);
			$valueList.="\"".stripslashes($value)."\",";
		}
	}
	$fieldList=substr($fieldList, 0, strlen($fieldList)-1);
	$valueList=substr($valueList, 0, strlen($valueList)-1);
	//$wpdb->query("INSERT into ". $wpdb->prefix . "store_locator (sl_store, sl_address, sl_city, sl_state, sl_zip) VALUES ('$_POST[sl_store]', '$_POST[sl_address]', '$_POST[sl_city]', '$_POST[sl_state]', '$_POST[sl_zip]')");
	//print "INSERT into ". $wpdb->prefix . "store_locator ($fieldList) VALUES ($valueList)"; exit;
	$wpdb->query("INSERT into ". $wpdb->prefix . "store_locator ($fieldList) VALUES ($valueList)");
	$address="$_POST[sl_address], $_POST[sl_city], $_POST[sl_state] $_POST[sl_zip]";
	do_geocoding($address);
	print "<div class='highlight'>Successful Addition. $view_link</div> <!--meta http-equiv='refresh' content='0'-->"; //header("location:$_SERVER[HTTP_REFERER]");
}

//Importing addresses from an local or remote database
if ($_POST[remote] && trim($_POST[query])!="" || $_POST[finish_import]) {
	
	if (ereg(".*\..{2,}", $_POST[server])) {
		include($sl_path."/addons/db-importer/remoteConnect.php");
	}
	else {
		/*if (file_exists("addons/db-importer/localImport.php")) {
			include($sl_path."/addons/db-importer/localImport.php");
		}
		else {*/
			include($sl_path."/localImport.php");
		//}
	}
	//for intermediate step match column data to field headers
	if ($_POST[finish_import]!="1") {exit();}
}

//Importing CSV file of addresses
$newfile="temp-file.csv"; 
$target_path="$root/";
$root=$_SERVER[DOCUMENT_ROOT];
if (move_uploaded_file($_FILES['csv_import']['tmp_name'], "$root/$newfile")) {
		include($sl_path."/addons/csv-xml-importer-exporter/csvImport.php");
	}
	else{
		//echo "<div style='background-color:salmon; padding:5px'>There was an error uploading the file, please try again. </div>";
	}

//If adding via the Point, Click, Add map (accepting AJAX)
if ($_GET[mode]=="pca") {
	include($sl_path."/addons/point-click-add/pcaImport.php");
}
	
$base=get_option('siteurl');

print <<<EOQ
<!--h2>Copy and Paste Addresses into Text Area:</h2>
<form  method=post>
<textarea rows='20' cols='100'></textarea><br>
<input type='submit'>
</form-->
EOQ;

print "
<table cellpadding='10px' cellspacing='0' style='width:100%'><tr>
<td style='/*border-right:solid silver 1px;*/ padding-top:0px;' valign='top'>

<form name='manualAddForm' method=post>
	<table cellpadding='0' class='widefat'>
	<thead><tr><td>".__("Type&nbsp;Address")."</td></tr></thead>
	<tr>
		<td>
		<b>".__("The General Address Format: ")."</b>(<a href=\"#\" onclick=\"show('format'); return false;\">show/hide</a>)
		<span id='format' style='display:none'><br><i>".__("Location Name")."<br>
		".__("Address (Street - Line1)")."<br>
		".__("Address (Street - Line2 - optional)")."<br>
		".__("City, State Zip")."</i></span><br><hr>
		".__("Location Name")."<br><input name='sl_store'><br><br>
		".__("Address (Street - Line1)")."<br><input name='sl_address'><br><br>
		".__("Address (Street - Line 2 - optional)")."<br><input name='sl_address2'><br><br>
		".__("City")."<br><input name='sl_city'><br><br>
		".__("State")."<br><input name='sl_state'><br><br>
		".__("Zip")."<br><input name='sl_zip'><br><br>
		<a href='#' onclick=\"show('more_fields'); return false;\">".__("Show/Hide More Fields")."</a><br><br>
		<div id='more_fields' style='display:none'>
		".__("Tags (seperate with commas)")."<br><input name='sl_tags'><br><br>
		".__("Description")."<br><textarea name='sl_description'></textarea><br><br>
		".__("URL")."<br><input name='sl_url'><br><br>
		".__("Hours")."<br><input name='sl_hours'><br><br>
		".__("Phone")."<br><input name='sl_phone'><br><br>
		".__("Image (shown on map with location)")."<br><input name='sl_image'><br><br>
		</div>
	<input type='submit' value='".__("Add Location")."' class='button'>
	</td>
		</tr>
	</table>
</form>

</td>
<td style='/*border-right:solid silver 1px;*/ padding-top:0px;' valign='top'>";

if (file_exists($sl_path."/addons/csv-xml-importer-exporter/csv-import-form.php")) {
	include($sl_path."/addons/csv-xml-importer-exporter/csv-import-form.php");
	print "<br>";
}
include($sl_path."/database-info.php");
if (file_exists($sl_path."/addons/db-importer/db-import-form.php")) {
	include($sl_path."/addons/db-importer/db-import-form.php");
}

print "
</td>
<td valign='top' style='padding-top:0px;'>
";

if (file_exists($sl_path."/addons/point-click-add/point-click-add-form.php")) {
	include($sl_path."/addons/point-click-add/point-click-add-form.php");
}

print "</td>
</tr>
</table>";
?>
</div>