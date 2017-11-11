# Original Author - Credits
This 'Database.class.php' class has been made by Mr. Philip Brown
It has been made with his post "Roll your own PDO PHP Class" (http://culttt.com/2012/10/01/roll-your-own-pdo-php-class/)

# Usage (from the original link)

## Insert a new record

Firstly you need to instantiate a new database.
```
$database = new Database();
```

Next we need to write our insert query. Notice how I’m using placeholders instead of the actual data parameters.
```
$database->query('INSERT INTO mytable (FName, LName, Age, Gender) VALUES (:fname, :lname, :age, :gender)');
```

Next we need to bind the data to the placeholders.
```
$database->bind(':fname', 'John');
$database->bind(':lname', 'Smith');
$database->bind(':age', '24');
$database->bind(':gender', 'male');
```

And finally we run execute the statement.
```
$database->execute();
```

Before running the file, echo out the lastInsertId function so you will know that the query successfully ran when viewed in the browser.
```
echo $database->lastInsertId();
```

## Insert multiple records using a Transaction

The next test we will try is to insert multiple records using a Transaction so that we don’t have to repeat the query.

The first thing we need to do is to begin the Transaction.
```
$database->beginTransaction();
```

Next we set the query.
```
$database->query('INSERT INTO mytable (FName, LName, Age, Gender) VALUES (:fname, :lname, :age, :gender)');
```

Next we bind the data to the placeholders.
```
$database->bind(':fname', 'Jenny');
$database->bind(':lname', 'Smith');
$database->bind(':age', '23');
$database->bind(':gender', 'female');
```

And then we execute the statement.
```
$database->execute();
```

Next we bind the second set of data.
```
$database->bind(':fname', 'Jilly');
$database->bind(':lname', 'Smith');
$database->bind(':age', '25');
$database->bind(':gender', 'female');
```

And run the execute method again.
```
$database->execute();
```

Next we echo out the lastInsertId again.
```
echo $database->lastInsertId();
```

And finally we end the transaction
```
$database->endTransaction();
```

## Select a single row

The next thing we will do is to select a single record.

So first we set the query.
```
$database->query('SELECT FName, LName, Age, Gender FROM mytable WHERE FName = :fname');
```

Next we bind the data to the placeholder.
```
$database->bind(':fname', 'Jenny');
```

Next we run the single method and save it into the variable $row.
```
$row = $database->single();
```

Finally, we print the returned record to the screen.
```
echo "<pre>";
print_r($row);
echo "</pre>";
```

## Select multiple rows

The final thing we will do is to run a query and return multiple rows.

So once again, set the query.
```
$database->query('SELECT FName, LName, Age, Gender FROM mytable WHERE LName = :lname');
```

Bind the data.
```
$database->bind(':lname', 'Smith');
```

Run the resultSet method and save it into the $rows variable.
```
$rows = $database->resultset();
```

Print the return records to the screen.
```
echo "<pre>";
print_r($rows);
echo "</pre>";
```

And finally display the number of records returned.
```
echo $database->rowCount();
```