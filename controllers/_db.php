<?php

if ($arGlblSqlAccess) {
	ORM::configure($arGlblSqlAccess['sql_url']);
	ORM::configure('username', $arGlblSqlAccess['username']);
	ORM::configure('password', $arGlblSqlAccess['password']);
	ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    
    if ($bDevMode) {
        ORM::configure('logging', true);
        ORM::configure('logger', function($sLogString, $sQueryTime) {
            debug ("IdiORM - Query ({$sQueryTime}) : " . $sLogString);
        });
    }
}


