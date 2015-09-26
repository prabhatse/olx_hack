<?php
/**
 *
 * sternDev/webapp/_lib/dao/interface/UserErrorDAO.php
 *
 * Copyright (c) 2015-2016 Stern India
 *
 * UserError Data Access Object
 *
 * Inserts user errors into the tu_user_error table.
 * Example user error text includes:
 * "Not found"
 * "Not authorized"
 * "User has been suspended."
 *
 * @copyright 2015-2016 Stern India
 * @author Prabhat Shankar <prabhat@sternindia.com>
 *
 */
interface UserErrorDAO {
    /**
     * Insert a user error
     * @param int $id ID of the user that got the error
     * @param int $error_code The HTTP error code (such as 404 not found or 403 not authorized)
     * @param string $error_text Description of the error
     * @param int $issued_to ID of the authorized user who got the error.
     * @param str $network
     * @return int Update row count
     */
    public function insertError($id, $error_code, $error_text, $issued_to, $network);
}

