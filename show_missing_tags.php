<?php

    include('simple_html_dom.php');

	function fConnectToDatabase() {
    	$connection = mysql_connect("localhost", "mikeob_clear", "Pass1234") or die('Connection error: ' . mysql_error());
    	mysql_select_db("mikeob_clear", $connection) or die('Database not found: ' . mysql_error());
	}
	
	fConnectToDatabase();
	
	$strSQL = "SELECT pageID, pageURL, live_title, live_meta_description FROM tblTitleMeta WHERE live_title = 'No Title Found' OR live_meta_description = 'No Meta Data'";
	$result = mysql_query($strSQL) or die ('SQL syntax error: ' . mysql_error());

	//$counter = 0;
	while ($row = mysql_fetch_array($result)) {
            $pageID = $row['pageID'];	
            $page = $row['pageURL'];
            $title = $row['live_title'];
            $meta_desc = $row['live_meta_description'];
            
            echo $pageID . " " . $page . "<br />";
            echo $title . "<br />";
            echo $meta_desc;
            echo "<hr />";

	}

?>