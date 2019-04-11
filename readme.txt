Catalyst it code test.
Import a csv file into a mysql database table.
Db table:
name, surname , email
Email is unique.

Some Possible Variations
1: Get one => Store one. Simple fgetcsv direct mysql insert. Catch error on Email primary key duplicate.
   **Note: unable to perform full dry run if using the DB for duplicate primary key detection.
2: Use generator, Get One => store One. Encapsulate fgetcsv into generator function to iterate data from csv file. Store each. Catch error on Email primary key.
   **Note: unable to perform full dry run if using the DB for duplicate primary key detection.
3: Read all, catch duplicate primary key in php, Store one at a time using simple array iterate.
    **Note csv size handing limitation governed by memory available to php process.
4: Read all, catch duplicate primary key in php, Store one at a time using array_map function.
    **Note csv size handing limitation governed by memory available to php process.
5: Read all, catch duplicate primary key. Store in batch. Configurable batch size
    **Note csv size handing limitation governed by memory available to php process.
6: Staging Table. Use a staging table ( temp table) to upload and validate data ( catch duplicates ) prior to copy to Prod table.
    **Note Dry run this requires the use of the DB.
    **Note can be used as variation for any of the 'Read' / 'Store' variations.
7: Stored Procedures. Use stored procedures for the creation and import of data. This is to hide the details of where data is stored.
Data validation/Tranform could be done in the procedure, Would prequire an our parameter for errors. Use case includes the use of staging tables
as part of the validation process.
8: use 'Data_load' to load file directly from local storage. This can include data transforms.
Duplicates can be handled using 'ignore' option but is unable to report on the duplicate records.
    **Note email validation could be included where failure to validate would attempt to insert a 'Null'. This would cause an error
    on the import with difficulties in reporting.


Common csv error to catch.
. Field count error. Must be exactly 3 field in record read from file.
. Email address validation.
    . Simple validaiton using regex to match .*@.* with exactly one @.
    . Use an email validator from the internet.

. Zero records.

I added a command line parameter '-d' to provide the name of database to use for the creation of the user table.

I implemented Variation 1 using a Constructed sql statement string and as a re-usable mysqli Statement (--statement_mode).

I also implemented variation 5 in the file 'user_upload_batch.php', catching duplicate email addresses in php before storing in batches.
I attempted to let the sql server handle rejection of duplicates but this resulted in batches being rejected.
I determined that removal of duplicate data from a batch for resubmitting to the server for possible rejection was untennable.

