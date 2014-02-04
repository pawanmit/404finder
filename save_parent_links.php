<?php

require_once 'db_util.php';
require_once 'utils.php';

save_parent_urls('./urls.txt');

function save_parent_urls($filename) {
	$file_handle = @fopen($filename, "r");
	$values = "";
	$count = 0;
	while (!feof($file_handle)) {
		$line = fgets($file_handle);
        $count++;
        $values .= "('" . $line . "')" . ",";
        if ($count >= 100) {
            $values = str_lreplace("," , "", $values);
            insert_primary_links($values);
            $values = "";
            $count = 0;
		}
	}
	fclose($file_handle);
}

function insert_primary_links($values) {
	$sql = "INSERT into parent_link (link) VALUES " . $values;
	$db_connection = wrapper_mysql_connect(null);
    write_to_log($sql . "\n");
	wrapper_mysql_query($sql, $db_connection);
}

?>