<?php
	define('DB_HOST', 'localhost');  
	define('DB_USER', 'root');
	define('DB_PASSWORD','');
	define('DB_NAME', 'QCM_DB');

	$dblink = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);

	if($dblink) {
    	$selectdatabase = mysql_select_db(DB_NAME);
    if(!$selectdatabase) {
        echo 'erreur lors de la selection de la bdd';
        exit;
    }
	} else {
    	echo 'erreur de connexion a la bdd';
    	exit;
	}
?>