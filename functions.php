<?php
    
    function HttpStatus($code) {
	    $status = array( 200 => 'OK', 400 => 'Fail', 500 => 'Internal Server Error');
        return $status[$code] ? $status[$code] : $status[500];
    }

    function SetHeader($code){
        header("HTTP/1.1 ".$code." ".HttpStatus($code));
        header("Content-Type: application/json; charset=utf-8");
    }

    function clean($string) {
        $string = str_replace(' ', '-', $string);
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
     }

?>