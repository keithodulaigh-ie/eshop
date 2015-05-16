<?php

require_once "config.inc.php";

/**
 * This handler will handle calls to login.php that are a result of the "Login"
 * button being clicked on the form.
 * 
 * It will assign the members details to the $_SESSION global variable and then
 * redirect the user to the homepageif their password was correct. If their
 * password was incorrect then they will be sent back to the login page and a
 * message will be displayed in the banner to tell them of their mistake.
 */
function doLogin() {
    $member = selectMember();
    
    if(!empty($member) && password_verify($_REQUEST[':password'], $member['password'])) {
        $_SESSION['member'] = $member;        
        $_SESSION[':email'] = $_SESSION['member']['email'];
        
        header("Location: index.php?banner=Welcome back!");
    } else {
        header("Location: login.php?banner=Incorrect email or password.");
    }                
}

/**
 * This handler will handle calls to login.php that are not a result of aform
 * being submitted. For example, somebody clicks a hyperlink on the page to
 * the login page or enters the login URL in their browser.
 * 
 * @global Smarty $smarty
 */
function doDefault() {
    global $smarty;
    
    $smarty->display("login.tpl.html");
}