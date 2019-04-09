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
$fileName = dirname(__FILE__).DIRECTORY_SEPARATOR.'users.csv';

function isEmailValid($emailAddress){
    return true;
}

$fp = fopen($fileName, 'r');
while($record = fgetcsv($fp)){
    print_r($record);
    if( ! isEmailValid($record[2])){
        printf("Invalid email [%s] Record skipped\n", $record[2]);
        continue;
    }
    // Store in db;
}
fclose($fp);