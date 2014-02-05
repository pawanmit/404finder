<?php
function write_to_log($message) {
    file_put_contents("./log/404.log" , $message . "\n", FILE_APPEND);
}

function str_lreplace($search, $replace, $subject) {
    $pos = strrpos($subject, $search);
    if($pos !== false) {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }
    return $subject;
}

?>