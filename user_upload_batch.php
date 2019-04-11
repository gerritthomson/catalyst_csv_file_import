<?php
/**
 * Created by Gerrit Thomson.
 * User: gerrit
 * Date: 09/04/2019
 * Time: 21:21
 */
/*
 * variation: Read Many + Store Many using simple loop interator.
 * variation: using statements.
 */
// Email regex from http://emailregex.com/
define('EMAIL_VALIDATION_REGEX','/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD');

define('DEFAULT_FILE_NAME','users.csv');
define('REQUIRED_FIELD_COUNT', 3);
define('DB_TABLE_NAME', 'users');
define('DB_DEFAULT_USER', 'root');
define('DB_DEFAULT_PASSWORD', 'toor');
define('DB_DEFAULT_HOST', 'localhost');
define('DB_DEFAULT_DATABASE', 'test');

define('DB_BATCH_SIZE',4);
/**
 * Validates an email address according matching the REGEX
 * @param $emailAddress
 * @return false|int
 */
function isEmailValid($emailAddress){
    $valid = preg_match(EMAIL_VALIDATION_REGEX , $emailAddress);
    return $valid;
}

/**
 * Create the data table
 */
function createUsersTable(){
    $dbHandle = getDbHandle();
    $tableName = getTableName();
    $result = mysqli_query($dbHandle, sprintf('DROP TABLE if exists %s', $tableName));
    if(mysqli_errno($dbHandle) != 0){
        $errorList = mysqli_error_list($dbHandle);
        print_r($errorList);
        exit;
    }
    $result = mysqli_query($dbHandle, sprintf('CREATE TABLE `%s` 
                                                        ( `email` VARCHAR(255) NOT NULL , 
                                                          `name` VARCHAR(255) NULL DEFAULT NULL , 
                                                          `surname` VARCHAR(255) NULL DEFAULT NULL , 
                                                          PRIMARY KEY (`email`(255))) 
                                                          ENGINE = InnoDB',
                                                    $tableName
                                            )
                            );
    if(mysqli_errno($dbHandle) != 0){
        $errorList = mysqli_error_list($dbHandle);
        print_r($errorList);
        exit;
    }

}

/**
 * Apply Transforms to the data prior to being processed.
 * @param $record
 * @return array
 */
function transformData($record){
    $returnData = array();
    $returnData['name'] = ucfirst( trim($record[0]));
    $returnData['surname'] = ucfirst( trim($record[1]));
    $returnData['email'] = strtolower( trim($record[2]));
    return($returnData);
}

/**
 * Get the Name of the table.
 * @return string
 */
function getTableName(){
    return DB_TABLE_NAME;
}

/**
 * Get the database connection. Connect if need be.
 * @return mysqli|null
 */
function getDbHandle(){
    static $dbHandle = null;
    if(is_null($dbHandle)){
        $dbHandle = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    }
    return $dbHandle;
}

/**
 * Begin a Transaction. Used to enable Dry Run mode Rollback.
 */
function beginTransaction(){
    $dbHandle = getDbHandle();
    mysqli_autocommit($dbHandle,FALSE);
    mysqli_begin_transaction($dbHandle);
    echo "Transaction begun\n";
}

/**
 * Commit the Transaction.
 */
function commitTransaction(){
    $dbHandle = getDbHandle();
    mysqli_commit($dbHandle);
    echo "transaction Comitted\n";
}

/**
 * Roll back the Sql transaction. Used to enable Dry Run mode.
 */
function rollbackTransaction(){
    $dbHandle = getDbHandle();
    mysqli_rollback($dbHandle);
    echo "Transaction Rolled Back\n";
}

/**
 * Store data in the Db.
 * Uses a constructed sql string.
 * @param $data
 * @return bool
 */
function storeBacthInDb($data){
    $dbHandle = getDbHandle();
    $sql = sprintf('INSERT INTO %s 
                          (name,surname,email)
                          values ',
                            getTableName());
    $dataParts = array_map(function($row) use( $dbHandle) {
                            return sprintf('("%s","%s","%s")',
                                mysqli_real_escape_string($dbHandle,$row['name']),
                                mysqli_real_escape_string($dbHandle,$row['surname']),
                                mysqli_real_escape_string($dbHandle,$row['email'])
                                );
                    },
                    $data);
    $sql .= join(',', $dataParts);
    $result = mysqli_query($dbHandle, $sql);
    if(mysqli_errno($dbHandle) != 0){
        $errorList = mysqli_error_list($dbHandle);
        print_r($errorList);
        return false;
    }
    return true;
}


/**
 * Get Statement to Execute
 * $statement is held in static so ita acts as a singleton
 */
function getStatementToExecute(){
    static $statement;
    if(is_null($statement)) {
        $dbHandle = getDbHandle();
        $sql = sprintf('INSERT INTO %s 
                          (name,surname,email)
                          values 
                          (?,?,?)',
            getTableName()
        );
        $statement = mysqli_stmt_init($dbHandle);
        mysqli_stmt_prepare($statement, $sql);
    }
    return $statement;
}

/**
 * Create the statement bound variable.
 * $keys is an array of key value pairs [$key=>$val] where the key is the index for the val i the type of the data.
 *
 * @param $statement mysqli_stmt
 * @param $keys array of index=>typeIndicator
 * @return mixed
 */
function getStatementBoundVar($statement, $keys)
{
    $types = '';
    foreach ($keys as $key => $type) {
        $var[$key] = '';
        $types .= $type;
    }
    // $refarg is an array to contain the references to the indexed variable to be bound.
    $refarg = array($statement, $types);//First two parameter of mysqli_stmt_bind_param

    // Adding the references to the variable to be bound.
    foreach ($var as $key => $value){//create array of parameters' references
        $refarg[] =& $var[$key];
    }
    // bind the $var variable to the $statement
    call_user_func_array("mysqli_stmt_bind_param", $refarg);
    return $var;
}

/**
 * find and remove duplate records based on email data
 * @param $data
 */
function removeDuplicateEmails($data){
    $emails = array_column($data, 'email');
    $emailCount = array_count_values($emails);
    print_r($emailCount);
    $justDuplicates = array_filter($emailCount, function($data,$key){
        print_r($key);
        print_r($data);
            if($data > 1){
                return true;
            }
            return false;
    },ARRAY_FILTER_USE_BOTH);
    print_r($justDuplicates);
    $revData = array_reverse($data);
    $revEmails = array_reverse($emails);
    foreach($justDuplicates as $key=>$value){
        while($value-- > 1){
            $unsetKey = array_search($key,$revEmails);
            unset($revEmails[$unsetKey]);
            unset($revData[$unsetKey]);
        }
    }
    $returnData = array_reverse($revData);
    return $returnData;
}
/**
 * Begin Run Code
 */

// Flag used to inidicate if Drt Run mode is in effect
$dry_run_flag = false;

// Statement Mode. Create a statement, and use a bound var for providing data
$statement_mode = false;

define('SHORT_OPTIONS','u:h:p:d:');
define('LONG_OPTIONS', array('file:',
    'create',
    'dry_run',
    'statement_mode',
    'help'));

// check and use command line parameters
$options = getopt(SHORT_OPTIONS, LONG_OPTIONS);
print_r($options);
if(array_key_exists('help', $options)){
    printf("Usage: %s --file[filename] [--dry_run] -u[db user name] -h[db host name] -p[db password] -d[db database name] [--statement_mode] [--help]\n", __FILE__);
    echo "filename: name of file to import. Assumed local unless name has directory separators\n";
    echo "db user name: name of database user to authenticate with. Must have create, drop and insert priviledges in the 'db database name' database\n";
    echo "db host name: Host name or ip address of database host to connect to\n";
    echo "db password: password of 'db user name' to use for authentication\n";
    echo "db database name: name of database to use to create table for storage of data. This must exists\n";
    echo "--dry_run: all data will be read and processed. data storage will not persist.\n";
    echo "--statement_mode: use the more secure and faster statement mode.\n";
    echo "--help: this help\n";
    exit;
}

// Database Options
define('DB_HOST', array_key_exists('h',$options) ? $options['h'] : DB_DEFAULT_HOST);
define('DB_USER', array_key_exists('u',$options) ? $options['u'] : DB_DEFAULT_USER);
define('DB_PASSWORD', array_key_exists('p',$options) ? $options['p'] : DB_DEFAULT_PASSWORD);
define('DB_DATABASE', array_key_exists('d',$options) ? $options['d'] : DB_DEFAULT_DATABASE);

if(array_key_exists('dry_run', $options)) {
    $dry_run_flag = true;
    echo "Dry Run mode\n";
}

if(array_key_exists('statement_mode', $options)) {
    $statement_mode = true;
    echo "Statement mode\n";
    $dataKeys = array('name'=>'s','surname'=>'s','email'=>'s');
}

if(array_key_exists('create', $options)){
    echo "Creating user table\n";
    createUsersTable();
    echo "User table created\n";
    exit();
}

$cmdlineFileName = $options['file'];
$fileName = $cmdlineFileName;
if(strpos($cmdlineFileName, DIRECTORY_SEPARATOR) === FALSE){
    $fileName = dirname(__FILE__) . DIRECTORY_SEPARATOR . $cmdlineFileName;
}


// Flag to indicate if first lin of csv file is a header line.
// First line will be skipped if TRUE;
$first_record_is_headers = true;



$numberOfRecordsRead = 0;
$numberOfRecordsStored = 0;

$fp = @fopen($fileName, 'r');
if ($fp === FALSE){
    printf("Error opening file [%s].\n", $fileName);
    exit();
}

if($statement_mode == TRUE){
    echo "Getting Statement and Bound Variable\n";
    $statmentToExecute = getStatementToExecute();
    $boundVar = getStatementBoundVar($statmentToExecute, $dataKeys);
}

beginTransaction();
$batchData = array();
while($record = fgetcsv($fp)){
    $numberOfRecordsRead ++;

    if ((count($record) == 1) && empty($record[0])){
        // skip blank lines
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
    $dataToStore = transformData($record);
    if( ! isEmailValid($dataToStore['email'])){
        printf("Invalid email [%s] Record skipped\n", $dataToStore['email']);
        continue;
    }
    // Store in db;
    if($statement_mode == TRUE){
        // set data to store into bound variable
        foreach($dataKeys as $key=>$value){
            $boundVar[$key] = $dataToStore[$key];
        }
        mysqli_stmt_execute($statmentToExecute);
        $errorList = mysqli_stmt_error_list($statmentToExecute);
        if(!empty($errorList)){
            print_r($errorList);
            continue;
        }
        $numberOfRecordsStored ++;
        continue;
    }
    $batchData[] = $dataToStore;
//    $result = storeInDb($dataToStore);
//    if($result == TRUE){
//        $numberOfRecordsStored ++;
//    }
}
fclose($fp);

$cleanedBatchData = removeDuplicateEmails($batchData);
$chunkedData = array_chunk($cleanedBatchData, DB_BATCH_SIZE);
foreach($chunkedData as $dataChunk){
    storeBacthInDb($dataChunk);
}

if($statement_mode == TRUE){
    mysqli_stmt_close($statmentToExecute);
    echo "Statement closed\n";
}
if($dry_run_flag == TRUE){
    echo "Dry Run mode :";
    rollbackTransaction();
    exit();
}
commitTransaction();
