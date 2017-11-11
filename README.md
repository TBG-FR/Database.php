# TBG-FR/Database.php
A PDO PHP Class, for a simple and safe link with Databases (MySQL)

------------

------------

# Database.php
My own version of that 'Database' class, merging versions located in https://github.com/TBG-FR/Database.php/Ressources/  
The aim of that one is to put together the benefits of all versions, and to be well-commented, well-organized and upgraded  

# Installation (2 Simple Steps)
First, download the 'Database.php' file and put in your directory

Then, put the following code where you want  
(I usually use a settings PHP file that I include before the Database class, but you can put it in the Database class file)
```
/* ----- Database Configuration ----- */
define("DB_HOST", "your_db_host");
define("DB_PORT", "3306");
define("DB_USER", "your_db_user");
define("DB_PASS", "your_db_pass");
define("DB_NAME", "your_db_name");
define("DB_CHARSET", "utf8");
```

For example, for a Wamp installation, it would goes like this
```
/* ----- Database Configuration ----- */
define("DB_HOST", "localhost");
define("DB_PORT", "3306");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_NAME", "bdd");
define("DB_CHARSET", "utf8");
```

# Usage

 -- TODO --