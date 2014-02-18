<?php

require_once('project/public_html/config.php');

if(is_array($argv))
{
    $user=$argv[1];
    $pass=$argv[2];
    if(count($argv) > 3)
    $addr=$argv[3];
}
else
{
    echo "needs args!";
    exit;
}


shell_exec('echo "
    GRANT USAGE ON *.* TO \''.DB_USER.'\'@\'localhost\';
    DROP USER \''.DB_USER.'\'@\'localhost\';
    CREATE USER \''.DB_USER.'\'@\'localhost\' IDENTIFIED BY \''.DB_PASS.'\';
    DROP DATABASE '.DB_NAME.';
    CREATE DATABASE '.DB_NAME.';
    GRANT ALL ON '.DB_NAME.'.* TO \''.DB_USER.'\'@\'localhost\';" | mysql -u'.$user.' -p'.$pass.'');

shell_exec('mysql -u'.DB_USER.' -p'.DB_PASS.' '.DB_NAME.' < project/mysql_dump.sql');