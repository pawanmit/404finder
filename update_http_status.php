<?php

require_once 'db_util.php';
require_once 'utils.php';

set_time_limit(0);
ini_set('memory_limit', '1024M');

//echo "Num child links to process " . get_num_child_links();
update_child_lnks_with_http_status();



function update_child_lnks_with_http_status() {
    $total_links = get_num_child_links();
    for ($count=0; $count < $total_links; $count +=100) {
        $offset = $count;
        if ( ($total_links - $count) < 100 ) {
            $limit = $total_links - $count;
        } else {
            $limit = 100;
        }
        $links = get_child_links_from_db($offset, $limit);
        update_http_status($links);
    }
}

function get_child_links_from_db($offset, $limit) {
    $db_connection = wrapper_mysql_connect('local');
    $sql = "select * from child_link where link like ('http://www.wired.com%') AND http_status IS NULL LIMIT " . $limit . " OFFSET " . $offset;
    //echo $sql . "<BR>";
    $result = wrapper_mysql_query($sql, $db_connection);
    //echo (mysql_numrows($result) . "<BR>");
    $links = array();
    while($link = mysql_fetch_array($result)) {
        array_push($links, $link['link'] );
    }
    return $links;
}

function get_num_child_links() {
    $db_connection = wrapper_mysql_connect('local');
    $sql = "SELECT COUNT(1) AS total from child_link WHERE link LIKE ('http://www.wired.com%') AND http_status IS NULL";
    $result = wrapper_mysql_query($sql, $db_connection);
    $data=mysql_fetch_assoc($result);
    return $data['total'];
}

function update_http_status($links) {
    foreach($links as $link) {
        $http_status = get_http_status($link);
        //write_to_log($link . " : " . $http_status);
        if (isset($http_status)) {
            update_http_status_db($link, $http_status);
        }
    }
}

function update_http_status_db($link, $http_status) {
    $sql = "UPDATE child_link SET http_status = " . $http_status . " WHERE link = '" . $link . "'";
    $db_connection = wrapper_mysql_connect('dev');
    wrapper_mysql_query($sql, $db_connection);
    //write_to_log($sql);
}

function get_http_status($url) {
    write_to_log($url . " : ");
    $http = curl_init($url);
    curl_setopt($http, CURLOPT_TIMEOUT, 20);
    //--- Start buffering
    ob_start();
    curl_exec ($http);
    //--- End buffering and clean output
    ob_end_clean();
    // do your curl thing here
    $http_status = curl_getinfo($http, CURLINFO_HTTP_CODE);
    curl_close($http);
    write_to_log($http_status . "\n");
    return $http_status;
}
?>