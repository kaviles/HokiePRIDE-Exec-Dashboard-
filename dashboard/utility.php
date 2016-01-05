<?php

include_once('dbconnect.php')

/*
************************************************
* UTILITY FUNCTIONS
************************************************
*/

/**
* Authorizes a user by checking $user with the usernames in the to the HokiePRIDE database.
*
* @param $user the username to authorize.
*
* @return NULL in all cases.
*/
function authorizeUser($user = '')
{
    $mysqli = connectToDB();

    $q = "SELECT * FROM exec_board WHERE pid = '".$user."'";
    $result = $mysqli->query($q);

    if ($result->num_rows == 0) 
    {
        // Row not found meaning user not found
        $message = date("Y-m-d H:i:s").' failed to authorize user '.$user.' as a HokiePRIDE exec board member.'."\n";
        error_log($message, 3, 'logs/general.log');
        disconnectFromDB($mysqli);
        return NULL;
    } 
    else 
    {
        // Row found meaning user was found
        $message = date("Y-m-d H:i:s").' authorized user '.$user.' as a HokiePRIDE exec board member.'."\n";
        error_log($message, 3, 'logs/general.log');
        disconnectFromDB($mysqli);
        return $result;
    }
}

function isValidIsbn10($isbn) {
    $check = 0;

    for ($i = 0; $i < 10; $i++) {
        if ('x' === strtolower($isbn[$i])) {
            $check += 10 * (10 - $i);
        } elseif (is_numeric($isbn[$i])) {
            $check += (int)$isbn[$i] * (10 - $i);
        } else {
            return false;
        }
    }

    return (0 === ($check % 11)) ? 1 : false;
}

function isValidIsbn13($isbn) {
    $check = 0;

    for ($i = 0; $i < 13; $i += 2) {
        $check += (int)$isbn[$i];
    }

    for ($i = 1; $i < 12; $i += 2) {
        $check += 3 * $isbn[$i];
    }

    return (0 === ($check % 10)) ? 2 : false;
}

function getTimeStamp() {

    return date_format(date_create(), 'Y-m-d H:i:s');
}

function escapeData($array) {

    $mysqli = connectToDB();

    if ($mysqli) {
        $arrLength = count($array);

        foreach ($array as $x => $x_val) {
            $array[$x] = $mysqli->real_escape_string($x_val);
        }
    }

    disconnectFromDB($mysqli);

    return $array; // I don't like that this has to be returned...
}

function generateLibID() {

    $libid = md5(uniqid(), false);

    while (strlen($libid) > 13) {
        $libid = substr_replace($libid, "", rand(0, strlen($libid) - 1), 1);
    }

    return $libid;
}

?>