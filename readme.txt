Catalyst it code test.
Import a csv file into a mysql database table.
Db table:
name, surname , email
Email is unique.

Variations
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

Common csv error to catch.
. Field count error. Must be exactly 3 field in record read from file.
. Email address validation.
    . Simple validaiton using regex to match .*@.* with exactly one @.
    . Use an email validator from the internet.

. Zero records.

