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
$catArray = [];
while ($stmt->fetch()) {
	if(isset($_POST['filter'])){
		if($out_category == $_POST['filter'] || $_POST['filter'] == 'all' ){
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
	}
	else{
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
	if(!in_array($out_category,$catArray) && $out_category!=""){
		array_push($catArray,$out_category);
	}
}
	echo '<tr><td>' . "<form action = 'http://web.engr.oregonstate.edu/~westonse/Assignment4-2src/videoStore.php' method = 'post'> <button type='submit' name = 'DeleteAll'>Delete All</button> </form>";
echo '</table>';
echo '<p><h4>Category Menu (Click buttons to filter videos by category)</h4>
<p>
<table border="1">';
 echo '<tr><td>' . "<form action = 'http://web.engr.oregonstate.edu/~westonse/Assignment4-2src/videoStore.php' method = 'post'> <button type='submit' name = 'filter' value = 'all'>'All Categories'</button> </form>";
foreach($catArray as $value){
 echo '<tr><td>' . "<form action = 'http://web.engr.oregonstate.edu/~westonse/Assignment4-2src/videoStore.php' method = 'post'> <button type='submit' name = 'filter' value = '$value'>'$value'</button> </form>";
}
echo '</table>';
echo " <br> <br> <form action = 'http://web.engr.oregonstate.edu/~westonse/Assignment4-2src/videoStore.php' id = 'addMovie' method = 'post'>"; 
echo "<input type = 'text' name = 'newName'> Name <br>";
echo "<input type = 'text' name = 'newCategory'> Category <br>";
echo "<input type='number' name='newLength' min='0'> Length (Mins) <br>";
echo "<button type='submit'> Add New </button> </form>";

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

if(isset($_POST['newName']) || isset($_POST['newCategory']) || isset($_POST['newLength'])){
	
	if($_POST['newName']!=null && is_numeric($_POST['newLength'])){
		$newLength = null;
		$newName = $_POST['newName'];
		if(!isset($_POST['newCategory'])){
			$newCategory = "";
		}
		else{
			$newCategory = $_POST['newCategory'];
		}
		if(!isset($_POST['newLength'])){
			$newLength = 0;
		}
		else{
			$newLength = $_POST['newLength'];
		}
			$newRented = 0;
			echo 'Add Successful';
			if (!($addNew = $mysqli->prepare("INSERT INTO videos (name, category, length, rented) VALUES ('$newName', '$newCategory', '$newLength', '$newRented')"))) {
				echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}
			if (!$addNew->execute()) {
				echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
		
			}
			echo '<meta http-equiv="refresh" content="0,URL=http://web.engr.oregonstate.edu/~westonse/Assignment4-2src/videoStore.php" />';
		
	}
	else if($_POST['newName']!=null && $_POST['newLength']==null){
		$newLength = null;
		$newName = $_POST['newName'];
		if(!isset($_POST['newCategory'])){
			$newCategory = "";
		}
		else{
			$newCategory = $_POST['newCategory'];
		}
			$newLength = 0;
		

			$newRented = 0;
			echo 'Add Successful';
			if (!($addNew = $mysqli->prepare("INSERT INTO videos (name, category, length, rented) VALUES ('$newName', '$newCategory', '$newLength', '$newRented')"))) {
				echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}
			if (!$addNew->execute()) {
				echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
		
			}
			echo '<meta http-equiv="refresh" content="0,URL=http://web.engr.oregonstate.edu/~westonse/Assignment4-2src/videoStore.php" />';
		
	}
	else if($_POST['newName']==null){
		echo 'Name is required please try again';
	}
	
	else{
		echo 'Length must be numerical, please try again';
	}
	
}

echo '</body>
</html>';
?>