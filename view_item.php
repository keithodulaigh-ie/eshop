<?php

require_once "config.inc.php";

/**
 * This handler will add an item being viewed to the user's basket.
 * 
 * @global Smarty $smarty
 */
function doAddToBasket() {
    if (memberLoggedIn()) {
        global $smarty;

        insertSaves();
        $smarty->assign("banner", "Saved to your basket.");
        doDefault();
    } else {
        requestUserToLogin();
    }
}

/**
 * This handler inserts a user's review for a book into the database,
 * then displays the page for the book and a banner to thank them
 * for their feedback.
 * 
 * @global Smarty $smarty
 */
function doReview() {
    if (memberLoggedIn()) {
        global $smarty;

        insertComments();
        $smarty->assign("banner", "Thank you for your feedback.");
        doDefault();
    } else {
        requestUserToLogin();
    }
}

/**
 * This is the default handler for the page. It simply shows information about the
 * book.
 * 
 * @global Smarty $smarty
 */
function doDefault() {
    if (memberLoggedIn()) {
        global $smarty;

        $smarty->assign("book", selectBook());
        $smarty->assign("canComment", canComment());
        $smarty->assign("comments", selectComments());
        $smarty->display("view_item.tpl.html");
    } else {
        requestUserToLogin();
    }
}

?>