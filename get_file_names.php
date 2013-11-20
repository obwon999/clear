<?php

    include('simple_html_dom.php');

	function fConnectToDatabase() {
    	$connection = mysql_connect("localhost", "mikeob_clear", "Pass1234") or die('Connection error: ' . mysql_error());
    	mysql_select_db("mikeob_clear", $connection) or die('Database not found: ' . mysql_error());
	}
	
	//Cleans given string to prevent SQL injections
	function fCleanString($UserInput) {
    	
        
    $UserInput = html_entity_decode($UserInput, ENT_QUOTES, 'UTF-8');
	$UserInput = strip_tags($UserInput);
    	return str_replace("'", "''", $UserInput);
	}
	
	//Grabs the page title from the given page
	function getTitle($page) {
		$html = file_get_html($page); 
		$items = $html->find('title');
		if($items[0]->innertext != "") {
			$title = $items[0]->innertext;
		} else {
			$title = "No Title Found";	
		}
		return $title;
	}
	
	//Gets the meta description from the given page
	function processMeta($page) {
		$meta = get_meta_tags($page);
			
		if($meta['description'] != "") {
			$meta_desc = $meta['description'];
		} else {
			$meta_desc = "No Meta Data";	
		}
		
		return $meta_desc;
	}
	
	//Updates the database with title and meta description
	function updateTable($pageID, $title, $meta_desc) {
		$title = fCleanString($title);
		$meta_desc = fCleanString($meta_desc);
		
		$strSQLupdate = "UPDATE tblLiveTitleMeta SET live_title = '$title', live_meta = '$meta_desc' WHERE pageID = '$pageID';";
		mysql_query($strSQLupdate) or die('Update error: ' . mysql_error());
	}
	
	//Calculate how many sets of 10 rows appear in the database
	function tenSets() {
		$strSQLRecords = "SELECT pageID FROM tblLiveTitleMeta";
		$recordsResult = mysql_query($strSQLRecords) or die ('SQL syntax error: ' . mysql_error());
		$numRows = mysql_num_rows($recordsResult);
		$tenSets = ceil($numRows/10);	
		return $tenSets;
	}
	
	//Searchs ten URLs at a time and updates them the title and meta description in the table
	function searchTenURLS() {
		$strSQL = "SELECT pageID, pageURL FROM tblLiveTitleMeta WHERE live_title = '' OR live_title = NULL LIMIT 10";
		$result = mysql_query($strSQL) or die ('SQL syntax error: ' . mysql_error());
	
		while ($row = mysql_fetch_array($result)) {
			$page = $row['pageURL'];
			$pageID = $row['pageID'];
			$meta_desc;
			
			$handle = @fopen($page,'r');
			
			if($handle !== false) {	
				$title = getTitle($page);
				$meta_desc = processMeta($page);
			}
			
			updateTable($pageID, $title, $meta_desc);
		}	
	}
	
	
	
	fConnectToDatabase();
	
	$tenSets = tenSets();
	
	$strSQLClear = "UPDATE tblLiveTitleMeta SET live_title='', live_meta=''";
	mysql_query($strSQLClear) or die('Clear error: ' . mysql_error());
	
	$counter = 0;
	while($counter < $tenSets) {
		searchTenURLS();
		$counter += 1;
	}
	
	//echo ini_get('memory_limit');

?>