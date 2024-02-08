<?php

use api\controller\ApiBaseController;



define('VG_ACCESS', true);

$rootDir = $_SERVER['DOCUMENT_ROOT'] . '/';

require_once   $rootDir.'core/base/settings/internal_settings.php';

require_once $rootDir.'api/settings/constants.php';

require_once $rootDir.'api/settings/settings.php';

$apiBaseController = new ApiBaseController();

