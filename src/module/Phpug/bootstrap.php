<?php
require_once "src/Phpug/Entity/Usergroup.php";

if (!class_exists("Doctrine\Common\Version", false)) {
    require_once "bootstrap_doctrine.php";
}