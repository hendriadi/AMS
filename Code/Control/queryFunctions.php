<?php
require_once("class.asset.php");
require_once("class.computer.php");
function arrayItemObjects($dbh){
		$array = array();
	    $result = $stmt = "SELECT * FROM items";
	    $query = $dbh->prepare($stmt);
	    $query->execute();
	    $items = $query->fetchAll(PDO::FETCH_ASSOC);
	    foreach($items as $item){
	    	if($item['computerID'] != 0){
	    		$query = "SELECT * FROM computers WHERE computerID = :computerID";
	    		$stmt = $dbh->prepare($query);
	    		$stmt->BindValue(":computerID", $item['computerID']);
	    		$stmt->execute();
	    		$id = $stmt->fetch(PDO::FETCH_ASSOC);
	    		$pcObj = new computer($id);
	    	}else{
	    		$pcObj = NULL;
	    	}
	    	$asset = new asset($item, $pcObj);
	    	array_push($array, $asset);
	    }
	    return $array;
}

function getLast10($dbh){
	$array = array();
	$getLast10 = "SELECT * FROM items ORDER BY dateEntered DESC LIMIT 10";
	$stmt = $dbh->prepare($getLast10);
	$stmt->execute();
	$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach ($items as $item){
		if($item['computerID'] != 0){
	    		$query = "SELECT * FROM computers WHERE computerID = :computerID";
	    		$stmt = $dbh->prepare($query);
	    		$stmt->BindValue(":computerID", $item['computerID']);
	    		$stmt->execute();
	    		$id = $stmt->fetch(PDO::FETCH_ASSOC);
	    		$pcObj = new computer($id);
	    	}else{
	    		$pcObj = NULL;
	    	}
	    	$asset = new asset($item, $pcObj);
	    	array_push($array, $asset);
	    }
	    return $array;
}

function getSingleObject($aID ,$dbh){
	$result = $stmt = "SELECT * FROM items WHERE itemID = :itemID";
	$query = $dbh->prepare($stmt);
	$query->bindValue(":itemID", $aID);
	    $query->execute();
	    $item = $query->fetch(PDO::FETCH_ASSOC);
	    	if($item['computerID'] != 0){
	    		$query = "SELECT * FROM computers WHERE computerID = :computerID";
	    		$stmt = $dbh->prepare($query);
	    		$stmt->BindValue(":computerID", $item['computerID']);
	    		$stmt->execute();
	    		$id = $stmt->fetch(PDO::FETCH_ASSOC);
	    		$pcObj = new computer($id);
	    	}else{
	    		$pcObj = NULL;
	    	}
	    	$asset = new asset($item, $pcObj);
	   
	    return $asset;
}

function getArrayOfObjectsByID($arrayOfIds, $dbh){
	$arrayOfObjs = array();
	foreach($arrayOfIds as $id){
		$selectQuery = "SELECT * FROM items WHERE itemID = :itemID";
		$stmt = $dbh->prepare($selectQuery);
		$stmt->bindValue(":itemID", $id['itemID']);
		$stmt->execute();
		$item = $stmt->fetch(PDO::FETCH_ASSOC);
		if($item['computerID'] != 0){
			$selectComputerQuery = "SELECT * FROM computers WHERE computerID = :computerID";
			$query = $dbh->prepare($selectComputerQuery);
			$query->bindValue("computerID", $item['computerID']);
			$query->execute();
			$computer = $query->fetch(PDO::FETCH_ASSOC);
			$pcObj = new computer($computer);
		}else{
			$pcObj = NULL;
		}
		$asset = new asset($item, $pcObj);
		array_push($arrayOfObjs, $asset);

	}
	return $arrayOfObjs;
}

function generateAjax($objects, $fileName){
	$result = count($objects);
	$fileM = fopen($fileName, "w");
	$topLine = "{\n\t\"data\": [";
	fwrite($fileM, $topLine);
	$i = 0;
	foreach($objects as $item){
		$i++;
		fwrite($fileM, "\t\t{\n");
		fwrite($fileM, "\t\t\t\"accountLevel\": \"{$_SESSION['status']}\",\n");
		fwrite($fileM, "\t\t\t\"itemID\": \"{$item->get("itemID")}\",\n");
		fwrite($fileM, "\t\t\t\"location\": \"{$item->get("location")}\",\n");
		fwrite($fileM, "\t\t\t\"warrantyExp\": \"{$item->get("warrantyExp")}\",\n");
		fwrite($fileM, "\t\t\t\"manufacturer\": \"{$item->get("manufacturer")}\",\n");
		fwrite($fileM, "\t\t\t\"price\": \"{$item->get("price")}\",\n");
		fwrite($fileM, "\t\t\t\"dateEntered\": \"{$item->get("dateEntered")}\",\n");
		fwrite($fileM, "\t\t\t\"description\": \"{$item->get("description")}\",\n");
		fwrite($fileM, "\t\t\t\"createdBy\": \"{$item->get("createdBy")}\",\n");
		if($item->get("retiredStatus") == 0){
			fwrite($fileM, "\t\t\t\"retiredStatus\": \"no\",\n");
		}else{
			fwrite($fileM, "\t\t\t\"retiredStatus\": \"yes\",\n");
		}
		fwrite($fileM, "\t\t\t\"type\": \"{$item->get("type")}\",\n");
		fwrite($fileM, "\t\t\t\"serialNum\": \"{$item->get("serialNum")}\",\n");
		fwrite($fileM, "\t\t\t\"currentUser\": \"{$item->get("currentUser")}\",\n");
		if($item->get("computer") == NULL){
			fwrite($fileM, "\t\t\t\"computerName\": \"-\",\n");
			fwrite($fileM, "\t\t\t\"operatingSys\": \"-\"\n");
		}else{
			fwrite($fileM, "\t\t\t\"computerName\": \"{$item->get("computer")->get("computerName")}\",\n");
			fwrite($fileM, "\t\t\t\"operatingSys\": \"{$item->get("computer")->get("operatingSys")}\"\n");
		}
		if($i == $result)
			fwrite($fileM, "\t\t}\n");
		else
			fwrite($fileM, "\t\t},\n");
	}
	fwrite($fileM, "\t]\n}");
	fclose($fileM);
}

function generateJsonForDonut($items){
	$retired = 0;
	$active = 0;
	foreach($items as $item){
		if($item->get("retiredStatus") == 0){
			$active++;
		}else{
			$retired++;
		}
	}
    $display =  "{label: 'Active', value: {$active}},";
    $display .= "{label: 'Retired', value: {$retired}},";
    return (string) $display;
}
function generateJsonForDonut2($items){
	$bookcase = 0;
	$computer = 0;
	$confChair = 0;
	$credenza = 0;
	$desk = 0;
	$deskChair = 0;
	$fileCab2 = 0;
	$fileCab3 = 0;
	$fileCab4 = 0;
	$fileCab5 = 0;
	$lamp = 0;
	$laptop = 0;
	$officeChair = 0;
	$phone = 0;
	$printer = 0;
	$speaker = 0;
	$table = 0;
	$other = 0;
	foreach($items as $item){
		if($item->get("type") == "BookShelves"){
			$bookcase++;
		}else if($item->get("type") == "Computer"){
			$computer++;
		}else if($item->get("type") == "Conference Room Chair"){
			$confChair++;
		}else if($item->get("type") == "Credenza"){
			$credenza++;
		}else if($item->get("type") == "Desk"){
			$desk++;
		}else if($item->get("type") == "Desk Chair"){
			$deskChair++;
		}else if($item->get("type") == "File Cabinet 2 drawer"){
			$fileCab2++;
		}else if($item->get("type") == "File Cabinet 3 drawer"){
			$fileCab3++;
		}else if($item->get("type") == "File Cabinet 4 drawer"){
			$fileCab4++;
		}else if($item->get("type") == "File Cabinet 5 drawer"){
			$fileCab5++;
		}else if($item->get("type") == "Lamp"){
			$lamp++;
		}else if($item->get("type") == "Laptop"){
			$laptop++;
		}else if($item->get("type") == "Office Chair"){
			$officeChair++;
		}else if($item->get("type") == "Phone"){
			$phone++;
		}else if($item->get("type") == "Printer"){
			$printer++;
		}else if($item->get("type") == "Speaker"){
			$speaker++;
		}else if($item->get("type") == "Table"){
			$table++;
		}else if($item->get("type") == "Other"){
			$other++;
		}
	}
    $display =  "{label: 'BookShelves', value: {$bookcase}},";
    $display .= "{label: 'Computer', value: {$computer}},";
    $display .= "{label: 'Conf. Rm Chair', value: {$confChair}},";
    $display .= "{label: 'Credenza', value: {$credenza}},";
    $display .= "{label: 'Desk', value: {$desk}},";
    $display .= "{label: 'Desk Chair', value: {$deskChair}},";
    $display .= "{label: 'File Cab. 2', value: {$fileCab2}},";
    $display .= "{label: 'File Cab. 3', value: {$fileCab3}},";
    $display .= "{label: 'File Cab. 4', value: {$fileCab4}},";
    $display .= "{label: 'File Cab. 5', value: {$fileCab5}},";
    $display .= "{label: 'Lamp', value: {$lamp}},";
    $display .= "{label: 'Laptop', value: {$laptop}},";
    $display .= "{label: 'Office Chair', value: {$officeChair}},";
    $display .= "{label: 'Phone', value: {$phone}},";
    $display .= "{label: 'Printer', value: {$printer}},";
    $display .= "{label: 'Speaker', value: {$speaker}},";
    $display .= "{label: 'Table', value: {$table}},";
    $display .= "{label: 'Other', value: {$other}},";

    return (string) $display;
}

function getAccounts($dbh){
	$selectAccounts = "SELECT username FROM users";
	$stmt = $dbh->prepare($selectAccounts);
	$stmt->execute();
	$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
	echo "<option></option>";
	foreach($items as $item){
		echo "<option value='{$item['username']}'>{$item['username']}</option>";
	}
}

function getSuperUsersReport($dbh){
	$selectUsers = "SELECT username FROM users WHERE accountLevel = 2";
	$stmt = $dbh->prepare($selectUsers);
	$stmt->execute();
	$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$counter = 0;
	foreach ($users as $user) {
		$counter++;
	}
	if ($counter <= 6){
		if($counter == 5){
			echo "<div class='row'>";
				echo "<div class='col-md-2>";
					foreach($users as $user){
						echo "<input type='checkbox' name='enteredBy[]' value='{$user['username']}'> {$user['username']}";
					}		
				echo "</div>";
				echo "<div class='col-md-2'></div>";
			echo "</div>";
		}else{
			$colNum = 12 / $counter;
			echo "<div class='row'>";
				
					foreach($users as $user){
						echo "<div class='col-md-{$colNum}'>";
						echo "<input type='checkbox' name='enteredBy[]' value='{$user['username']}'> {$user['username']}";
						echo "</div>";
					}		

			echo "</div>";
		}
	}else{
		$count = 0;
		$termCount = 0;
		foreach($users as $user){
			if($count == 0 || ($count % 6) == 0){
				echo "<div class='row'>";
					echo "<div class='col-md-2'>";
					echo "<input type='checkbox' name='enteredBy[]' value='{$user['username']}'> {$user['username']}";
					echo "</div>";
			}else{
				echo "<div class='col-md-2'>";
				echo "<input type='checkbox' name='enteredBy[]' value='{$user['username']}'> {$user['username']}";
				echo "</div>";
			}
			$termCount++; 
			if($termCount == 6){
				$termCount = 0;
				echo "</div>";
			}
			
			$count++;
		}
		if($termCount != 0){
			$j = 6 - $termCount;
			for($i = 0; $i < $j; $i++){
				echo "<div class='col-md-2'></div>";
			}
			echo "</div>";
		}
	}
}

function getLocationsReport($dbh){
	$selectUsers = "SELECT location FROM items";
	$stmt = $dbh->prepare($selectUsers);
	$stmt->execute();
	$locsArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$locsArr = array();
	foreach($locsArray as $arrayItem){
		array_push($locsArr, $arrayItem["location"]);
	}
	
	$locs = array_unique($locsArr);
	sort($locs);
	$counter = 0;
	foreach ($locs as $loc) {
		$counter++;
	}
	if ($counter <= 6){
		if($counter == 5){
			echo "<div class='row'>";
				echo "<div class='col-md-2>";
					foreach($locs as $loc){
						echo "<input type='checkbox' name='location[]' value='{$loc}'> {$loc}";
					}		
				echo "</div>";
				echo "<div class='col-md-2'></div>";
			echo "</div>";
		}else{
			$colNum = 12 / $counter;
			echo "<div class='row'>";
				
					foreach($locs as $loc){
						echo "<div class='col-md-{$colNum}'>";
						echo "<input type='checkbox' name='location[]' value='{$loc}'> {$loc}";
						echo "</div>";
					}		

			echo "</div>";
		}
	}else{
		$count = 0;
		$termCount = 0;
		foreach($locs as $loc){
			if($count == 0 || ($count % 6) == 0){
				echo "<div class='row'>";
					echo "<div class='col-md-2'>";
					echo "<input type='checkbox' name='location[]' value='{$loc}'> {$loc}";
					echo "</div>";
			}else{
				echo "<div class='col-md-2'>";
				echo "<input type='checkbox' name='location[]' value='{$loc}'> {$loc}";
				echo "</div>";
			}
			$termCount++;
			if($termCount == 6){
				$termCount = 0;
				echo "</div>";
			}
			
			$count++;
		}
		if($termCount != 0){
			$j = 6 - $termCount;
			for($i = 0; $i < $j; $i++){
				echo "<div class='col-md-2'></div>";
			}
			echo "</div>";
		}
	}
}

function getCurrUsersReport($dbh){
	$selectUsers = "SELECT currentUser FROM items";
	$stmt = $dbh->prepare($selectUsers);
	$stmt->execute();
	$userArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$userArr = array();
	foreach($userArray as $arrayItem){
		array_push($userArr, $arrayItem["currentUser"]);
	}
	
	$users = array_unique($userArr);
	sort($users);
	$counter = 0;
	foreach ($users as $user) {
		$counter++;
	}
	if ($counter <= 6){
		if($counter == 5){
			echo "<div class='row'>";
				echo "<div class='col-md-2>";
					foreach($users as $user){
						echo "<input type='checkbox' name='currentUser[]' value='{$user}'> {$user}";
					}		
				echo "</div>";
				echo "<div class='col-md-2'></div>";
			echo "</div>";
		}else{
			$colNum = 12 / $counter;
			echo "<div class='row'>";
				
					foreach($users as $user){
						echo "<div class='col-md-{$colNum}'>";
						echo "<input type='checkbox' name='currentUser[]' value='{$user}'> {$user}";
						echo "</div>";
					}		

			echo "</div>";
		}
	}else{
		$count = 0;
		$termCount = 0;
		foreach($users as $user){
			if($count == 0 || ($count % 6) == 0){
				echo "<div class='row'>";
					echo "<div class='col-md-2'>";
					echo "<input type='checkbox' name='currentUser[]' value='{$user}'> {$user}";
					echo "</div>";
			}else{
				echo "<div class='col-md-2'>";
				echo "<input type='checkbox' name='currentUser[]' value='{$user}'> {$user}";
				echo "</div>";
			}
			$termCount++;
			if($termCount == 6){
				$termCount = 0;
				echo "</div>";
			}
			
			$count++;
		}
		if($termCount != 0){
			$j = 6 - $termCount;
			for($i = 0; $i < $j; $i++){
				echo "<div class='col-md-2'></div>";
			}
			echo "</div>";
		}
	}
}

function getManufacturersReport($dbh){
	$selectUsers = "SELECT manufacturer FROM items";
	$stmt = $dbh->prepare($selectUsers);
	$stmt->execute();
	$manArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$manArr = array();
	foreach($manArray as $arrayItem){
		array_push($manArr, $arrayItem["manufacturer"]);
	}
	
	$mans = array_unique($manArr);
	sort($mans);
	$counter = 0;
	foreach ($mans as $man) {
		$counter++;
	}
	if ($counter <= 6){
		if($counter == 5){
			echo "<div class='row'>";
				echo "<div class='col-md-2>";
					foreach($mans as $man){
						echo "<input type='checkbox' name='manufacturer[]' value='{$man}'> {$man}";
					}		
				echo "</div>";
				echo "<div class='col-md-2'></div>";
			echo "</div>";
		}else{
			$colNum = 12 / $counter;
			echo "<div class='row'>";
				
					foreach($mans as $man){
						echo "<div class='col-md-{$colNum}'>";
						echo "<input type='checkbox' name='manufacturer[]' value='{$man}'> {$man}";
						echo "</div>";
					}		

			echo "</div>";
		}
	}else{
		$count = 0;
		$termCount = 0;
		foreach($mans as $man){
			if($count == 0 || ($count % 6) == 0){
				echo "<div class='row'>";
					echo "<div class='col-md-2'>";
					echo "<input type='checkbox' name='manufacturer[]' value='{$man}'> {$man}";
					echo "</div>";
			}else{
				echo "<div class='col-md-2'>";
				echo "<input type='checkbox' name='manufacturer[]' value='{$man}'> {$man}";
				echo "</div>";
			}
			$termCount++;
			if($termCount == 6){
				$termCount = 0;
				echo "</div>";
			}
			
			$count++;
		}
		if($termCount != 0){
			$j = 6 - $termCount;
			for($i = 0; $i < $j; $i++){
				echo "<div class='col-md-2'></div>";
			}
			echo "</div>";
		}
	}
}
?>