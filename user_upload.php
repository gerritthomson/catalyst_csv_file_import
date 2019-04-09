<?php
/**
 * Created by Gerrit Thomson.
 * User: gerrit
 * Date: 09/04/2019
 * Time: 21:21
 */
/*
 * variation: Read One + Store One using simple loop interator.
 */
$fileName = 'users.csv';

function isEmailValid($emailAddress){
    return true;
}

$fp = fopen($fileName, 'R');
while($record = fgetcsv($fp)){
    if( ! isEmailValid($record[2])){
        printf("Invalid email [%s] Record skipped\n");
        continue;
    }
    // Store in db;
}
fclose($fp);