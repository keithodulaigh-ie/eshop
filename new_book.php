<?php

require_once 'config.inc.php';

/**
 * Displays the form for entering in a new book. Must be an admin to view this 
 * page.
 * 
 * @global Smarty $smarty
 */
function doDefault() {
    if (isAdmin()) {
        global $smarty;

        $smarty->display("new_book.tpl.html");
    } else {
        header("Location: index.php?banner=This feature requires admin privileges.");
    }
}

/**
 * Creates a new book and then redisplays the create form to the admin. Must be
 * an admin to run this page.
 * 
 * @global Smarty $smarty
 */
function doCreateBook() {
    if (isAdmin()) {
        global $smarty;

        $fileName = $_FILES[':small_image']['tmp_name'];
        $_REQUEST[':small_image'] = bin2hex(file_get_contents($fileName));

        $fileName = $_FILES[':large_image']['tmp_name'];
        $_REQUEST[':large_image'] = bin2hex(file_get_contents($fileName));

        createBook();

        $smarty->assign("banner", "Book created.");
        $smarty->display("new_book.tpl.html");
    } else {
        header("Location: index.php?banner=This feature requires admin privileges.");
    }
}

?>