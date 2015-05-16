<?php

require_once "config.inc.php";

function doAllCategories() {
    global $smarty;
    
    $smarty->assign("categories", selectCategories());
    $smarty->display("all_categories.tpl.html");
}

function doCategorySearch() {
    global $smarty;
    
    $smarty->assign("books", categorySearch());
    $smarty->display("search_results.tpl.html");    
}

/**
 * This performs a quick search which sees if the query string is a substring 
 * of either the title, description or author's first/last names. It then 
 * displays the results to the user.
 * 
 * @global Smarty $smarty
 */
function doQuickSearch() {
    global $smarty;
    
    $smarty->assign("books", quickSearch());
    $smarty->display("search_results.tpl.html");
}

/**
 * This performs a full search using the full search page and returns the
 * books found to the user.
 * 
 * @global Smarty $smarty
 */
function doSearch() {
    global $smarty;

    $smarty->assign("categories", selectCategories());
    $smarty->assign("books", search());
    $smarty->display("search_results.tpl.html");
}

/**
 * This simply displays the search form.
 */
function doDefault() {
    global $smarty;
    
    $smarty->display("search.tpl.html");
}

?>