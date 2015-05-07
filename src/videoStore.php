<?php
ini_set('display_errors','On');
include 'secret.php';
echo '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title></title>
</head>
<body>';
$mysqli = new mysqli("oniddb.cws.oregonstate.edu","westonse-db",$myPassword,"westonse-db");
if($mysqli->connect_errno){
	echo "Failed to connect to mySQL: (" . $mysqli->connect_errno . ")" . $mysqli->connect_error;

}
else{
	echo "Connection worked! <br>";
	
}
if (!($stmt = $mysqli->prepare("SELECT name, category, length, rented FROM videos"))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
}


$out_name = NULL;
$out_category = NULL;
$out_length = NULL;
$out_rented = NULL;
if (!$stmt->bind_result($out_name, $out_category, $out_length, $out_rented)) {
    echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}

echo '<p><h3>Video Table</h3>
<p>
<table border="1">
<tr>
<td>';

echo '<td>' . 'Name';
echo '<td>' . 'Category';
echo '<td>' . 'Length';
echo '<td>' . 'Checked in/out';

while ($stmt->fetch()) {
    //printf("id = %s (%s), name = %s (%s) category = %s (%s)\n", $out_id, gettype($out_id), $out_name, gettype($out_name), $out_category, gettype($out_category));
	echo '<tr><td>' . "<form action = 'http://web.engr.oregonstate.edu/~westonse/Assignment4-2src/videoStore.php' id = '$out_name' method = 'post'> <button type='submit' name = 'Delete' value = '$out_name'>Delete</button> </form>";
	echo '<td>' . $out_name;
	echo '<td>' . $out_category;
	echo '<td>' . $out_length;
	if($out_rented == 0){
		$rented = 'Checked in';
	}
	if($out_rented == 1){
		$rented = 'Checked out';
	}
	echo '<td>' . "<form action = 'http://web.engr.oregonstate.edu/~westonse/Assignment4-2src/videoStore.php' id = '$out_name'.'check' method = 'post'> <button type='submit' name = 'check' value = '$out_name'>'$rented'</button> </form>";
}
	echo '<tr><td>' . "<form action = 'http://web.engr.oregonstate.edu/~westonse/Assignment4-2src/videoStore.php' method = 'post'> <button type='submit' name = 'DeleteAll'>Delete All</button> </form>";
echo '</table>';
$stmt->close();
if(isset($_POST['Delete'])){
$deleteVid = $_POST['Delete'];
if (!($remove = $mysqli->prepare("DELETE FROM videos WHERE name = '$deleteVid'"))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
if (!$remove->execute()) {
    echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
echo '<meta http-equiv="refresh" content="0,URL=http://web.engr.oregonstate.edu/~westonse/Assignment4-2src/videoStore.php" />';
}
if(isset($_POST['check'])){
$checkVid = $_POST['check'];
if (!($check = $mysqli->prepare("UPDATE videos SET rented = !rented WHERE name = '$checkVid'"))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
if (!$check->execute()) {
    echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
echo '<meta http-equiv="refresh" content="0,URL=http://web.engr.oregonstate.edu/~westonse/Assignment4-2src/videoStore.php" />';
}
if(isset($_POST['DeleteAll'])){

if (!($deleteAll = $mysqli->prepare("DELETE FROM videos"))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
if (!$deleteAll->execute()) {
    echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
echo '<meta http-equiv="refresh" content="0,URL=http://web.engr.oregonstate.edu/~westonse/Assignment4-2src/videoStore.php" />';
}

echo '</body>
</html>';
?>