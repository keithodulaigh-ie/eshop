<?php

require_once "config.inc.php";

/**
 * This handler is called when a user clicks on a "Register" button on the form
 * displayed by the default action of this page.
 * 
 * It should place the users detail in the database and then redirect them to the
 * login page and let them login.
 * 
 * It'd be nice if sent out an email too and provided better feedback in error
 * cases where for e.g., the user was already registered.
 */
function doRegister() {
    $_REQUEST[':password'] = password_hash($_REQUEST[':password'], PASSWORD_DEFAULT);
    
    createMember();
    
    header("Location: login.php?banner=Check your email to activate your account before logging in.");    
}

/**
 * The default action is to display the registration form.
 * 
 * @global Smarty $smarty
 */
function doDefault() {
    global $smarty;
    
    $smarty->display("register.tpl.html");
}