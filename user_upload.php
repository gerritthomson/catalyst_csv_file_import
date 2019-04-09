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
define('DEFAULT_FILE_NAME','users.csv');
define('REQUIRED_FIELD_COUNT', 3);
define('DB_TABLE_NAME', 'users');
define('DB_DEFAULT_USER_NAME', 'root');
define('DB_DEFAULT_PASSWORD', 'toor');
define('DB_DEFAULT_HOST', 'localhost');
define('DB_DEFAULT_DATABASE', 'test');

$dry_run_flag = false;

define('SHORT_OPTIONS','u:h:p:d:');
define('LONG_OPTIONS', array('file:',
    'create',
    'dry_run',
    'help'));

// check and use command line parameters
$options = getopt(SHORT_OPTIONS, LONG_OPTIONS);
print_r($options);
if(array_key_exists('help', $options)){
    printF("Usage: %s --file[filename] [--dry_run] -u[db use name] -h[db host name] -p[db password] -d[db database name] [--help]\n", __FILE__);
    exit;
}
// Database Options
define('DB_HOST', array_key_exists('h',$options) ? $options['h'] : DB_DEFAULT_HOST);
define('DB_USER', array_key_exists('u',$options) ? $options['u'] : DB_DEFAULT_USER);
define('DB_PASSWORD', array_key_exists('p',$options) ? $options['p'] : DB_DEFAULT_PASSWORD);
define('DB_DATABASE', array_key_exists('d',$options) ? $options['d'] : DB_DEFAULT_DATABASE);

$cmdlineFileName = $options['file'];
$fileName = $cmdlineFileName;
if(strpos($cmdlineFileName, DIRECTORY_SEPARATOR) === FALSE){
    $fileName = dirname(__FILE__) . DIRECTORY_SEPARATOR . $cmdlineFileName;
}



$first_record_is_headers = true;

function isEmailValid($emailAddress){
    return true;
}

function getTableName(){
    return DB_TABLE_NAME;
}
function getDbHandle(){
    static $dbHandle = null;
    if(is_null($dbHandle)){
        $dbHandle = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    }
    return $dbHandle;
}

function storeInDb($row){
//    $dbHandle = getDbHandle();
    $sql = sprintf('INSERT INTO %s 
                          (name,surname,email)
                          values 
                          ("%s","%s","%s")',
                            getTableName(),
                            $row[0],$row[1],$row[2]);
    printf("DEBUG:%s\n", $sql);

}
$numberOfRecordsRead = 0;
$fp = fopen($fileName, 'r');
while($record = fgetcsv($fp)){
    $numberOfRecordsRead ++;

    // print_r($record);
    if ((count($record) == 1) && empty($record[0])){
        // skip balnk lines
        continue;
    }
    if(count($record) != REQUIRED_FIELD_COUNT){
        printf("Invalid number of fields [%s] read, expected [%s] fields\n", count($record), REQUIRED_FIELD_COUNT);
        continue;
    }
    if(($first_record_is_headers == true) and ($numberOfRecordsRead == 1) ){
        // skip header line. Perhaps later use as names for temporary storage array indexes.
        continue;
    }
    if( ! isEmailValid($record[2])){
        printf("Invalid email [%s] Record skipped\n", $record[2]);
        continue;
    }
    // Store in db;
    storeInDb($record);
}
fclose($fp);