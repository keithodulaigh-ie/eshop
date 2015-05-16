<?php

require_once "config.inc.php";

/**
 * Inserts a new book into the database.
 */
function createBook() {
    $sql = "INSERT INTO book (isbn, title, price, category, author_name_first, author_name_last, published, description, small_image, large_image)" .
            "VALUES (:isbn, :title, :price, :category, :author_name_first, :author_name_last, :published, :description, :small_image, :large_image);";

    run_query($sql);
}

/**
 * Gets the small image associated with a book.
 * 
 * @return byte array
 */
function selectSmallBookImage() {
    $sql = "SELECT small_image " .
            "FROM book " .
            "WHERE isbn = :isbn;";

    return hex2bin(run_query($sql)[0]['small_image']);
}

/**
 * Gets the large image associated with a book.
 * 
 * @return byte array
 */
function selectLargeBookImage() {
    $sql = "SELECT large_image " .
            "FROM book " .
            "WHERE isbn = :isbn;";

    return hex2bin(run_query($sql)[0]['large_image']);
}

/**
 * Selects all of the categories associated with books in the database.
 * 
 * @return array The book categories.
 */
function selectCategories() {
    $sql = "SELECT DISTINCT category " .
            "FROM book " .
            "ORDER BY category ASC;";

    return run_query($sql);
}

/**
 * Searches for all books that match a particular category.
 * 
 * @return array All details belonging to books in the category.
 */
function categorySearch() {
    $sql = "SELECT * " .
            "FROM book " .
            "WHERE category = :category;";

    return run_query($sql);
}

/**
 * Quick search tries to match the query against the author name, book title or
 * description or category.
 * 
 * Note: The double pipe (||) means string concatenation.
 * 
 * @return array An array with the information for the books that match SOME of 
 * the search criteria.
 */
function quickSearch() {
    $sql = "SELECT * " .
            "FROM book " .
            "WHERE title ILIKE '%' || :query || '%'" .
            "   OR author_name_first ILIKE '%' || :query || '%'" .
            "   OR author_name_last ILIKE '%' || :query || '%'" .
            "   OR category ILIKE '%' || :query || '%'" .
            "   OR description ILIKE '%' || :query || '%';";

    return run_query($sql);
}

/**
 * Performs a full search using the inputs from the user.
 * 
 * @return array An array with the information for the books that match ALL of the 
 * search criteria.
 */
function search() {
    $sql = "SELECT * " .
            "FROM book " .
            "WHERE title ILIKE '%' || :title || '%'" .
            "   AND (author_name_first || author_name_last) ILIKE '%' || :author || '%'" .
            "   AND description ILIKE '%' || :description || '%'" .
            "   AND category ILIKE '%' || :category || '%';";

    return run_query($sql);
}

/**
 * This returns the top three categories from the database.  The top 3
 * are based on how many books are there in that category and NOT by
 * how many have been sold.
 * 
 * @return array The top three categories.
 */
function selectTopCategories() {
    $sql = "SELECT category " .
            "FROM book " .
            "GROUP BY category " .
            "ORDER BY count(category) DESC, category ASC " .
            "LIMIT 2;";

    return run_query($sql);
}

/**
 * This inserts a new member into the database.
 */
function createMember() {
    $sql = "INSERT INTO member (email, name_first, name_last, phone, " .
            "                    password, address_street, " .
            "                    address_town, address_county)" .
            "VALUES (:email, :name_first, :name_last, :phone, :password, " .
            "        :address_street, :address_town, :address_county);";

    run_query($sql);
}

/**
 * This selects a member from the database by matching their email address and
 * password. If there is no user matching the details then FALSE will be returned.
 * 
 * @return mixed An array with all of the members information.
 */
function selectMember() {
    $sql = "SELECT * " .
            "FROM member " .
            "WHERE email = :email;";

    $result = run_query($sql);
    
    if(!empty($result)) {
        return $result[0];
    } else {
        return array();
    }
}

/**
 * Selects all of a books information. The book is selected based on its ISBN
 * number.
 * 
 * @return array All of the books information.
 */
function selectBook() {
    $sql = "SELECT * " .
            "FROM book " .
            "WHERE isbn = :isbn;";

    return run_query($sql)[0];
}

/**
 * Selects all of the comments about a book as well as the details of the member
 * who wrote the comment.
 * 
 * @return array The comment details and the details of the member who wrote the 
 * comment for a particular book.
 */
function selectComments() {
    $sql = "SELECT * " .
            "FROM comments " .
            "JOIN member ON email = member_email " .
            "WHERE book_isbn = :isbn;";

    return run_query($sql);
}

/**
 * Inserts a new comment into the database.
 */
function insertComments() {
    $sql = "INSERT INTO comments (member_email, book_isbn, review, score) " .
            "VALUES (:email, :isbn, :review, :score)";

    run_query($sql);
}

/**
 * Checks if a user can comment on a book. A user can only comment if they
 * haven't done so before. So, we check how many comments that user already
 * wrote fo that book.  If it's less than 1 they can comment. Otherwise no!
 * 
 * @return boolean TRUE if the user is allowed to comment on a book. FALSE otherwise.
 */
function canComment() {
    $sql = "SELECT count(*) < 1 AS result " .
            "FROM comments " .
            "WHERE member_email = :email " .
            "   AND book_isbn = :isbn;";

    return run_query($sql)[0]['result'];
}

/**
 * Gets the 12 most popular books OR 12 most recent. 
 * 
 * This is based on how many copies of the book were sold. It orders the results 
 * by date published so that newer books are given more prominence. In the case 
 * where the website has just "launched" there will be no purchases so we select 
 * the 12 latest publications instead.
 * 
 * @return array The 12 books to show on the landing page.
 */
function selectFrontPageBooks() {
    $sql = "SELECT * " .
            "FROM " .
            "( SELECT * " .
            "  FROM book " .
            "  WHERE isbn IN ( SELECT isbn " .
            "                  FROM book " .
            "                  JOIN purchases ON isbn = book_isbn " .
            "                  GROUP BY isbn " .
            "                  ORDER BY count(isbn) DESC, published DESC " .
            "                  LIMIT 12 " .
            "                ) " .
            " UNION " .
            "  SELECT * " .
            "  FROM book " .
            "  ORDER BY published DESC " .
            "  LIMIT 12 " .
            ") AS T " .
            "ORDER BY published DESC " .
            "LIMIT 12;";


    return run_query($sql);
}

/**
 * Gets the price for a book based on its ISBN. 
 * 
 * I wouldn't need this query if PDO allowed for variables in nested
 * statements! 
 * 
 * @return double A money value for the book's price. 
 */
function getPrice() {
    $sql = "SELECT price " .
            "FROM book " .
            "WHERE isbn = :isbn;";

    return run_query($sql)[0]['price'];
}

/**
 * Inserts a purchase in the database.
 */
function insertPurchases() {
    $sql = "INSERT INTO purchases (member_email, book_isbn, price, quantity) " .
            "VALUES (:email, :isbn, :price, :quantity)";

    run_query($sql);
}

/**
 * Inserts an item into the basket for a user.
 */
function insertSaves() {
    $sql = "INSERT INTO saves (member_email, book_isbn, quantity) " .
            "VALUES (:email, :isbn, :quantity);";

    run_query($sql);
}

/**
 * Updates a basket item for a user.
 */
function updateSaves() {
    $sql = "UPDATE saves " .
            "SET quantity = :quantity " .
            "WHERE member_email = :email AND book_isbn = :isbn;";

    run_query($sql);
}

/**
 * Deletes an item from a user's basket.
 */
function deleteSaves() {
    $sql = "DELETE FROM saves " .
            "WHERE member_email = :email AND book_isbn = :isbn;";

    run_query($sql);
}

/**
 * Delets all of a user's basket items.
 */
function deleteAllSaves() {
    $sql = "DELETE FROM saves " .
            "WHERE member_email = :email;";

    run_query($sql);
}

/**
 * Gets all of the items in a user's basket.
 * 
 * @return array The details of a book and quantity of it saved by a user.
 */
function selectSaves() {
    $sql = "SELECT book.*, saves.quantity " .
            "FROM saves " .
            "JOIN book ON isbn = book_isbn " .
            "WHERE member_email = :email;";

    return run_query($sql);
}

/**
 * This accepts an SQL query, scans the query for required parameters
 * and then builds an array of values to match exactly those required parameters.
 * Note by default with PDO if you include more or less parameters than required by the 
 * query you will get an error but this function alleviates the programmer from
 * having to worry about that.
 * 
 * For example, suppose your query looks like:
 * 
 *           SELECT * FROM books WHERE isbn = :book_isbn;
 * 
 * There is ONE parameter to this query i.e. :book_isbn. (Query parameters begin
 * with a colon :). If we try to bind this array of parameter values 
 *     (:member_email => "blah@example.com", :book_isbn => "123")
 * 
 * to that query using $query->bindParams($ourArray) we get an error as it 
 * provides TWO parameter values. This function will filter out the unneeded 
 * parameter values, i.e. :member_email so that the bindParams call works.
 * 
 * Also, without this function, you could have to pass a horseload of variables from
 * $_POST, $_GET or $_REQUEST to the SQL functions above, which would then bind the
 * parameters before calling this function. This way means that the above functions
 * can be called with zero parameters in their headers and things still work. Provided
 * that the needed values are in the $_REQUEST array.
 * 
 * @param type $sql An SQL query using 
 * @return array An array of associative arrays with the results of the query.
 */
function run_query($sql) {
    try {
        $params = getRequiredParamValues(getRequiredParams($sql));
        $connection = new PDO(DBPROTOCOL . ":host=" . DBHOST . ";port=" . DBPORT . ";dbname=" . DBNAME, DBUSER, DBPASS);

        if (defined(DBSCHEMA)) {
            $query = $connection->prepare("SET search_path TO " . DBSCHEMA);
            $query->execute();
        }

        $query = $connection->prepare($sql);
        $query->execute($params);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $error) {
        die($error->getMessage());
    }
}

/**
 * This function scans an SQL query and returns an array of the parameters that
 * it requires. For example, suppose you have this query:
 * 
 * SELECT isbn FROM books WHERE title = :title AND author = :author;
 * 
 * then the required parameters are (:title, :author).
 * 
 * @param string $sql The query to get the parameters from.
 */
function getRequiredParams($sql) {
    $NON_ALPHA_COL_UNDER = "/[^A-Za-z:_]/";
    $requiredParams = array();
    $words = explode(" ", $sql);

    if (count($words) > 0) {
        foreach ($words as $word) {
            /* Some params in the query might be beside brackets or end with 
             * commas etc. so strip those off first.
             * 
             * For e.g. in 
             * 
             *    INSERT INTO tName VALUES (:value1, :value2);
             * 
             * the params would appear as (:values1, and :value2); so we
             * need to remove the (, and ); characters.
             */
            $strippedWord = preg_replace($NON_ALPHA_COL_UNDER, '', $word);

            if (startsWith($strippedWord, ":")) {
                array_push($requiredParams, $strippedWord);
            }
        }
    }

    return $requiredParams;
}

/**
 * Given a list of required parameter names, this function searches the
 * $_REQUEST and $_SESSION array for appropriate values and then creates a new associative 
 * array with only those values.
 * 
 * Precedence given to $_SESSION values.
 * 
 * For e.g., if $requiredParams is passed array(":email", ":isbn")
 * 
 * and 
 * 
 * $_REQUEST looks like $_REQUEST[':isbn'] => 1234567890
 *                      $_REQUEST[':title'] => Test Title
 * 
 * and
 * 
 * $_SESSION looks like $_SESSION[':email'] => keithwantsnospam@cs.nuim.ie
 *                      $_SESSION[':is_admin'] => TRUE
 * 
 * then this function will return an array like
 * 
 * $result is $result[':isbn'] => 1234567890
 *            $result[':email'] => keithwantsnospam@cs.nuim.ie
 * 
 * If $_REQUEST and $_SESSION share indices, for example if there exists
 * $_REQUEST[':email'] and $_SESSION[':email'] it will always pick the one
 * from $_SESSION.
 * 
 * @param array $requiredParams The names of the paramters required.
 * @return array As associative array mapping SQL param names to values.
 */
function getRequiredParamValues($requiredParams) {
    $allValues = array_merge($_REQUEST, $_SESSION);
    $values = $allValues;

    foreach ($allValues as $key => $value) {
        if (!in_array($key, $requiredParams)) {
            unset($values[$key]);
        }
    }

    /* Note to self:
     * Add a loop here to strip_tags from the returned values to prevent
     * XSS attack.
     **/
    
    return $values;
}

/**
 * Checks if a string starts with a substring. 
 * 
 * (I borrowed this from StackOverflow.)
 * 
 * @param type $haystack
 * @param type $needle
 * @return type
 */
function startsWith($haystack, $needle) {
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

?>
