<?php

require_once 'config.inc.php';

/**
 * This hanlder will handle ALL requests to index.php which are NOT a result
 * of a form being submitted.
 * 
 * The page will simply display the index template and provide a list of best
 * selling and latest books to that template.
 * 
 * @global Smarty $smarty
 */
function doDefault() {
    global $smarty;
    
    $smarty->assign("books", selectFrontPageBooks());
    $smarty->display("index.tpl.html");
}

?>