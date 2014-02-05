<?php

require_once 'db_util.php';
require_once 'utils.php';

$db_connection = wrapper_mysql_connect(null);

set_time_limit(0);
ini_set('memory_limit', '1024M');

get_and_save_child_urls();

function get_and_save_child_urls() {
    date_default_timezone_set("America/Los_Angeles");
    global $db_connection;
    $sql = "SELECT id, link FROM parent_link WHERE is_processed = 0";
    $result = wrapper_mysql_query($sql, $db_connection);
    if (mysql_numrows($result) == 0) {
        echo "No parent links found that need processing";
    } else {
        while($parent = mysql_fetch_array($result)) {
                write_to_log("Processing parent with id " . $parent['id'] . "\n");
                $child_links = get_child_links($parent['link']);
                save_child_links($child_links);
                mark_parent_processed($parent['id']);
        }
    }
}

function is_parent_processed($parent_id) {
    $is_processed = false;
    $sql = "SELECT is_processed FROM parent_link WHERE id=" . $parent_id;
    global $db_connection;
    $result = wrapper_mysql_query($sql, $db_connection);
    $data=mysql_fetch_assoc($result);
    if ( ! ($data['is_processed'] == 0) ) {
        $is_processed  = true;
    }
    return $is_processed;
}

function get_child_links($parent_link) {
    $html = get_link_content($parent_link);
    $links = get_hrefs($html);
    //foreach($links as $link) {
        //echo $link . "<BR>";
    //}
    return $links;
}

function save_child_links($child_links) {
    foreach($child_links as $link) {
        $link = rel2abs($link, '');
        if (filter_var($link, FILTER_VALIDATE_URL) === FALSE) {
            write_to_log('Not a valid URL:' .  $link . "\n");
        } else {
            insert_child_link($link);
        }
    }
}

function insert_child_link($link) {
    $sql = "INSERT into child_link (link) VALUES ('" . $link . "')"; ;
    //write_to_log($sql . "\n");
    global $db_connection;
    try {
        wrapper_mysql_query($sql, $db_connection);
    } catch (Exception $e) {

    }
}

function get_http_status($url) {
    $http = curl_init($url);
    ob_start();
    curl_exec ($http);
    ob_end_clean();
    $http_status = curl_getinfo($http, CURLINFO_HTTP_CODE);
    curl_close($http);
    return $http_status;
}

function get_link_content($url) {
    $http = curl_init($url);
    ob_start();
    curl_exec ($http);
    $output = ob_get_contents();
    ob_end_clean();
    curl_close($http);
    return $output;
}

function get_hrefs($html) {
    $dom = new DOMDocument;
    $dom->loadHTML($html);
    $internal_links = array();
    foreach ($dom->getElementsByTagName('a') as $node) {
        if ($node->hasAttribute( 'href' )) {
            $href = $node->getAttribute( 'href' );
            array_push($internal_links, $href);
        }
    }
    return $internal_links;
}

function mark_parent_processed($parent_id) {
    $sql = "UPDATE parent_link SET is_processed = 1 WHERE id = " . $parent_id;
    global $db_connection;
    write_to_log($sql . "\n");
    wrapper_mysql_query($sql, $db_connection);
}

function rel2abs($rel, $base)
{
    $host = "www.wired.com";
    $scheme = "http";
    /* return if already absolute URL */
    if (parse_url($rel, PHP_URL_SCHEME) != '') return $rel;

    /* queries and anchors */
    if ($rel[0]=='#' || $rel[0]=='?') return $base.$rel;

    /* parse base URL and convert to local variables:
       $scheme, $host, $path */
    extract(parse_url($base));

    /* remove non-directory element from path */
    $path = preg_replace('#/[^/]*$#', '', $rel);

    /* destroy path if relative url points to root */
    if ($rel[0] == '/') $path = '';

    /* dirty absolute URL // with port number if exists */
    if (parse_url($base, PHP_URL_PORT) != ''){
        $abs = "$host:".parse_url($base, PHP_URL_PORT)."$path/$rel";
    }else{
        $abs = "$host$path/$rel";
    }
    /* replace '//' or '/./' or '/foo/../' with '/' */
    $re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
    for($n=1; $n>0; $abs=preg_replace($re, '/', $abs, -1, $n)) {}

    /* absolute URL is ready! */
    return $scheme.'://'.$abs;
}
?>