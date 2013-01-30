<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * block_badger.
 *
 * @package   block_badger
 * @2013 Iain Checkland (@iaincheckland)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
            
require('../../config.php');
require_once('class_badge.php');

$badgeid = optional_param('badgeid', 0, PARAM_INT);
$type = optional_param('type','',PARAM_TEXT);

$title = 'Badge Report';
$PAGE->set_url('/blocks/badger/report.php');
$PAGE->set_context($context);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('mydashboard');

echo $OUTPUT->header();
echo $OUTPUT->box_start('generalbox');

$badge = new badge($badgeid);

// return an array of badge id's
$family = $badge->family();
if(count($family)>1){
    foreach($family as $k=>$badgeid){
       $badge = new badge($k);
       echo $badge->details(); 
    }
}else{
    echo $badge->details();
}

echo $OUTPUT->box_end(); 
echo $OUTPUT->footer();

?>