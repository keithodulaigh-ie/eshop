<?php

require_once "config.inc.php";

/**
 * This handler updates the user's basket.
 * 
 * @global type $smarty
 */
function doSaveChangesForLater() {
    global $smarty;
    
    $isbns = $_REQUEST[':isbn'];
    $quantities = $_REQUEST[':quantity'];

    for ($index = 0; $index < count($isbns); $index++) {
        $_REQUEST[':isbn'] = $isbns[$index];
        $_REQUEST[':quantity'] = $quantities[$index];
        
        if ($quantities[$index] > 0) {
            updateSaves();
        } else {
            deleteSaves();
        }
    }

    $smarty->assign("banner", "Your basket was updated.");
    doDefault();  
}

/**
 * This handler moves purchases left in a user's basket into their list of 
 * purchases. It then redirects the user back to the homepage and displays 
 * a message to tell them that there purchase has been processed. 
 * 
 * We need to be ensure that if the user changed the quantity that we use the
 * new quantity and if it is 0 then we do not process it. Afterwards,
 * you can delete their basket because there won't be anything left in it. 
 * Amazon has a save for later feature so that items that aren't purchased at
 * the time are still kept for later but eShop doesn't support that (yet).
 */
function doBuyNow() {
    $isbns = $_REQUEST[':isbn'];
    $quantities = $_REQUEST[':quantity'];
    
    for ($index = 0; $index < count($isbns); $index++) {
        if ($quantities[$index] > 0) {
            $_REQUEST[':isbn'] = $isbns[$index];
            $_REQUEST[':price'] = getPrice();
            $_REQUEST[':quantity'] = $quantities[$index];

            insertPurchases();
        }
    }

    deleteAllSaves();

    header("Location: index.php?banner=You purchases are on their way!");    
}

/**
 * The default action for the page is just to show the user the items that
 * are in their basket.
 * 
 * @global Smarty $smarty
 */
function doDefault() {
    global $smarty;
    
    $smarty->assign("saved_books", selectSaves());
    $smarty->display("view_saves.tpl.html");    
}
?>