<?php

require_once "config.inc.php";

function doShowSmallImage() {
    header("Content-Type: image/jpeg");
    
    echo selectSmallBookImage();
}

function doShowLargeImage() {
    header("Content-Type: image/jpeg");
    
    echo selectLargeBookImage();
}

?>