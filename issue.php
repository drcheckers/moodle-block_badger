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
 * Manage issuing of value badge awards.
 *
 * @package   block_badger
 * @2013 Iain Checkland (@iaincheckland)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
            
require('../../config.php');
require_once('class_badge.php');

global $DB,$CFG,$USER;

$badgeid = required_param('badgeid', PARAM_INT);
$sid=$USER->id;

if($badgeid){
    $badge = new badge($badgeid);
}
                       
// get a candidate set of course modules where this badge can be claimed
if(!$cms = $DB->get_records_sql("select cm.* from {url} u  
                                        inner join {course_modules} cm on (u.id=cm.instance)
                                        inner join {modules} m on (m.id=cm.module) 
                                        where m.name='url' and u.externalurl=?",array($CFG->wwwroot .'/blocks/badger/issue.php?badgeid='.$badgeid))){
   print_error('you are not entitled to claim this badge'); 
}   

$entitled=false;
$allowed=explode(',',$badge->badge->courserestrictions);
                                   
foreach($cms as $cm){
   // check this badge can be claimed here 
   // [in case someone places a link to the badge from somewhere unauthorised!!]
   if(in_array($cm->course,$allowed)){
       try{
           // check whether this module is visible to the student - probably a nice way of doing this
           require_course_login($cm->course, true, $cm, false, true);
           $entitled=true;
       }catch(Exception $ex) {
       }
   }
}


$title = get_string('badgereport','block_badger');
$PAGE->set_url('/blocks/badger/issue.php');
$PAGE->requires->js('/blocks/badger/badger.js');
$PAGE->set_context($context);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('mydashboard');

// check the claim is coming from an allowed course
if(!$entitled){
   print_error('you are not entitled to claim this badge'); 
}

$badge->issue($USER->id,$cm->course);

echo $OUTPUT->header();
echo html_writer::tag('script','Javscript required to claim badge',array('src'=>badge::api()));
        
echo $OUTPUT->box_start('generalbox');

echo html_writer::tag('h2',get_string('congratulations','block_badger'));
echo html_writer::tag('p',get_string('awarded','block_badger',array('badge'=>$badge->name()) ));
echo html_writer::tag('p',get_string('clickclaim','block_badger'));

echo '<hr/>';
echo $badge->details($badge->image(120,array('data-assertion'=>$badge->assertionurl(),'class'=>'getbadge','title'=>$badge->name() . ': Click to claim')));
echo '<hr/>';
echo html_writer::tag('p',html_writer::tag('small',get_string('modernbrowser','block_badger')));

echo $OUTPUT->box_end(); 
echo $OUTPUT->footer();

?>