<?php

require_once "config.inc.php";

/**
 * This page doesn't display any forms so the only action it has to handle is
 * when somebody clicks on a hyperlink to logout.
 * 
 * The session should be destroyed and then the user redirected to the homepage
 * with a message telling them that they have been logged out.
 */
function doDefault() {
    session_destroy();
    
    unset($_SESSION);
    
    header("Location: index.php?banner=You have been logged out.");
}

?>