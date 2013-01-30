<?php

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/class_badge.php');

header('Content-Type: application/json');
$iid = $_GET['id'];
$badge = badge::badgefromiid($iid);
echo json_encode($badge->assertion());
?>