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
define('REQUIRED_FIELD_COUNT', 3);

function isEmailValid($emailAddress){
    return true;
}

$fp = fopen($fileName, 'r');
while($record = fgetcsv($fp)){
    print_r($record);
    if(count($record) != REQUIRED_FIELD_COUNT){
        printf("Invalid number of fields [%s] read, expected [%s] fields\n", count($record), REQUIRED_FIELD_COUNT);
        continue;
    }
    if( ! isEmailValid($record[2])){
        printf("Invalid email [%s] Record skipped\n", $record[2]);
        continue;
    }
    // Store in db;
}
fclose($fp);