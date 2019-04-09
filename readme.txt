Catalyst it code test.
Import a csv file into a mysql database table.
Db table:
name, surname , email
Email is unique.

Variations
1: Get one Store one. Simple fgetcsv direct mysql insert. Catch error on Email primary key duplicate.
2: Use generator, Get One -> store One. Encapsulate fgetcsv into generator function to iterate data from csv file. Store each. Catch error on Email primary key.
3: Read all, catch duplicate primary key in php, Store one at a time using array_map function.
4: Read all, catch duplicate primary key. Store in batch.