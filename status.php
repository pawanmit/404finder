<?php

require_once 'db_util.php';
require_once 'utils.php';

$db_connection = wrapper_mysql_connect(null);

echo "Number of parent links processed " . get_num_processed_links() . "<BR>";

echo "Number of parent child links generated " . get_num_child_links() . "<BR>";

function get_num_processed_links() {
    date_default_timezone_set("America/Los_Angeles");
    global $db_connection;
    $sql = "SELECT COUNT(1) as total FROM parent_link where is_processed = 1";
    $result = wrapper_mysql_query($sql, $db_connection);
    $data = mysql_fetch_array($result);
    return $data['total'];
}

function get_num_child_links() {
    date_default_timezone_set("America/Los_Angeles");
    global $db_connection;
    $sql = "SELECT COUNT(1) as total FROM child_link ";
    $result = wrapper_mysql_query($sql, $db_connection);
    $data = mysql_fetch_array($result);
    return $data['total'];
}

?>