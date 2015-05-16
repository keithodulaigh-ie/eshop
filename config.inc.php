<?php

/* It's required that the session is started as the first line of a PHP file so
 * here it is. Since all the other PHP files will be importing this config file,
 * we won't have to do this in any other place.
 */
session_start();

/*
 * The following need to be changed to match the database server settings.
 * 
 * Here is a table of suggested values. The port numbers are the defaults
 * used by the respective servers.
 * ____________________________________
 * | Server     | DBPROTOCOL | DBPORT |
 * |------------|------------|--------|
 * | MySql      | mysql      | 3306   |
 * |------------|------------|--------|
 * | PostgreSql | pgsql      | 5432   |
 * |__________________________________|
 * 
 * The DBSCHEMA variable only needs to be defined if usng a PostgreSQL system. 
 * If you using another system such as MySQL then comment it out.
 */
define("DBNAME", "eshop");
define("DBUSER", "eshop");
define("DBPASS", "eshop");
define("DBHOST", "localhost");
define("DBPORT", 5432);
define("DBPROTOCOL", "pgsql");
define("DBSCHEMA", "eshop");

/**
 * Most of the PHP files will be using our database queries and the Smarty template
 * engine. So, we may as well import the necessary libraries here. That way any
 * other PHP files which includes this config file wil automatically have access
 * to those libraries.
 */
require_once "queries.php";
require_once "lib/Smarty.class.php";

/**
 * Since most of our PHP files will be displaying a Smarty template and also 
 * importing this config file, it makes sense to initialise the Smarty object here. 
 * This way we won't have to rewrite this same piece of code in 8+ other files.
 */
$smarty = new Smarty();
$smarty->setTemplateDir("templates");
$smarty->setCompileDir("templates_c");
$smarty->assign("categories", selectTopCategories());

/**
 * We will assume that when the user logs in that our code will store the
 * user details in $_SESSION['member']. So, if this variable is set then the
 * user is logged in; otherwise they aren't.
 * 
 * @return boolean TRUE if a member is logged in. FALSE otherwise.
 */
function memberLoggedIn() {
    return isset($_SESSION['member']);
}

/**
 * By convention, we MUST ensure that all of our submit buttons in HTML code
 * have the name "handler". For example:
 * 
 * <input type="submit" name="handler" value="Save to Basket" />
 * 
 * If we adhere to this (religiously!) then we will know that a user has
 * submitted a form when either $_GET['handler'] or $_POST['handler'] is set. For
 * simplicity we will check $_REQUEST['handler'] as this covers both $_GET and
 * $_POST.
 * 
 * Note:
 * Make sure than NO OTHER ELEMENTS are given the name 'handler'!
 * 
 * @return boolean TRUE if a form was submitted. FALSE otherwise.
 */
function isFomSubmitted() {
    return isset($_REQUEST['handler']);
}

/**
 * Converts a phrase to camel case but with the first letter also capitalised.
 * 
 * Examples:
 * "Save to Basket" becomes "SaveToBasket"
 * "add to basket" becomes "AddToBasket"
 * "aDd TO BASKET" becomes "ADdTOBASKET"
 * 
 * Note the delimiter is whitespace.
 * 
 * @param type $phrase The phrase to convert to camel case.
 * @return string The phrase as a single word in camel case.
 */
function toCamelCase($phrase) {
    $pieces = preg_split("/\W/", $phrase);
    
    return implode("", array_map('ucfirst', $pieces));
}

/**
 * Sends the user back to the login page.
 */
function requestUserToLogin() {
    header("Location: login.php?banner=Please login first.");
}

/**
 * Checks if a client is logged in as an admin.
 * 
 * @return boolean TRUE if the user is logged in as an admin. FALSE otherwise.
 */
function isAdmin() {
    if(isset($_SESSION['member'])) {
        return $_SESSION['member']['is_admin'];
    }
    
    return FALSE;
}

/**
 * These two lines are the most important in this whole website. Without them 
 * nothing usefull will happen!
 * 
 * The idea is that any other PHP file will have functions that have names to
 * match the VALUE of the submit button displayed on the web page rendered by 
 * that script. And of course since sometimes pages are called without being
 * the result of a form submission, the function to
 * be called in that case will be called "doDefault".
 * 
 * In other words, suppose the file register.php displays a form with a submit button
 * that looks like this:
 * 
 * <input type="submit" name="handler" value="Register" />
 * 
 * then our register.php file will need to have two functions:
 * 
 * 1. doDefault which just diplays the form to the user.
 * 2. doRegister which takes the input from the form submission, registers the user
 *    and redirects them to the login page upon completion.
 * 
 * Th real reason for this code is to help make our PHP code look a little
 * more organised because now we can break each file into functions and don't
 * have to worry about putting code in each file to choose which function to
 * execute.
 * 
 * Note:
 * These lines of code are NOT in a function so they will be executed anytime
 * another PHP file is called by the user.
 */

$functionToCall = isFomSubmitted() ? toCamelCase($_REQUEST['handler']) : "Default";
call_user_func("do" . $functionToCall);

?>