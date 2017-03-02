<?php
define('APP_DIR', dirname(dirname(__FILE__)));

chdir(APP_DIR);

require 'vendor/autoload.php';

require_once 'controllers/_config.php';
require_once 'controllers/_db.php';
require_once 'controllers/_log.php';

