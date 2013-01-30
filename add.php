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
 * Manage/add badges in course area.
 *
 * @package   block_badger
 * @copyright 2013 Iain Checkland (@iaincheckland)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
            
require('../../config.php');
require_once("add_form.php");
require_once('class_badge.php');

require_once("$CFG->dirroot/repository/lib.php");
            
$blockid = optional_param('id', 0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);
$retire = optional_param('retire', 0, PARAM_INT);
$unretire = optional_param('unretire', 0, PARAM_INT);
$edit = optional_param('edit', 0, PARAM_INT);
$fulledit = optional_param('fulledit', 0, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_INT);
$revoke = optional_param('revoke', 0, PARAM_INT);
$revoke = optional_param('revoke', 0, PARAM_INT);
require_login();

if (isguestuser()) {
    die(); 
}
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
if (empty($returnurl)) {
    $returnurl = new moodle_url('add.php');
}else{
    $returnurl = new moodle_url($returnurl);
    $returnurl->params(array('id'=>$courseid));
}

$context = get_context_instance(CONTEXT_BLOCK, $blockid);

if($edit||$fulledit){
    $title = get_string('editbadge','block_badger');
}else{
    $title = get_string('addbadge','block_badger');
}
$PAGE->set_url('/blocks/badger/add.php',array('courseid'=>$courseid,'id'=>$blockid,'returnurl'=>$returnurl,'edit'=>$edit));
$PAGE->set_context($context);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('mydashboard');

$data = new stdClass();
$data->block_id = $blockid;
$data->courseid = $courseid;
$data->returnurl = $returnurl;
$data->contextid = $context->id;
$options = array('subdirs'=>false, 'maxbytes'=>$CFG->userquota, 'maxfiles'=>-1, 'accepted_types'=> array('*.png'), 'return_types'=>FILE_INTERNAL);

$fs = get_file_storage();        
$msg='';

if($edit||$fulledit){
  $badge=new badge($edit?$edit:$fulledit);
  foreach($badge->badge as $k=>$v){
      $data->$k=$v;
  }    
}

if($revoke<>0){
   if($revoke<0){
       $revoke=-1*$revoke;
       $badge = badge::badgefromiid($revoke);
       $url=$PAGE->url;
       $url->param('revoke',$revoke);
       $yes = $url->__toString();
       $url->remove_params('revoke');
       $no = $url;
       echo $OUTPUT->confirm(get_string('explainissuername', 'block_badger') . $badge->badge->name . ' ' . get_string('from', 'block_badger') . ' ' . $badge->recipient(), $yes, $no);
   }else{
        $DB->delete_records('block_badger_badges_issued',array('id'=>$revoke));
   } 
}

if($delete){
  // remove from badges  
  $DB->delete_records('block_badger_badges',array('id'=>$delete));  
  $msg = get_string('badgedeleted', 'block_badger');
}

$data->fulledit=$fulledit;
$data->edit=$edit;

$mform = new badge_upload_form(null, array('data'=>$data, 'options'=>$options, 'config'=>$config));

if ($mform->is_cancelled()) {
    redirect($returnurl);
} else if ($formdata = $mform->get_data()) {
    if(isset($formdata->files)){
        // if in a collection - check either new collection or in an allowed course
        if(!empty($formdata->collection)){
            if($cbadges=$DB->get_records('block_badger_badges',array('collection'=>$formdata->collection))){
                $valid=false;
                $msg=get_string('cannotcreatebadge', 'block_badger');
                foreach($cbadges as $b){
                    $allowed=explode(',',$b->courserestrictions);
                    if(in_array($courseid,$allowed)){
                        $valid=true;
                        $msg='';
                    }    
                }
            }    
        }

        // its an add 
        $draftitemid = $formdata->files;
        $valid=false;    
        $files = file_get_drafarea_files($draftitemid);
        if (count($files->list) == 1) {
            if ($draftitemid) {
                // move file from draft area
                file_save_draft_area_files($draftitemid, $context->id, 'local_badger', 'content', $draftitemid );
                $files = $fs->get_area_files($context->id, 'local_badger', 'content', $draftitemid);
                foreach($files as $f){
                   if($f->get_filesize()>0){
                       // check the image meets specification - otherwise delete it
                       $x=$f->get_imageinfo();  
                       if($x['width']==$x['height']
                        && ($x['mimetype']=='image/png') ){
                           $valid=true;
                           $newid=$f->get_id(); 
                           $url = "{$CFG->wwwroot}/pluginfile.php/{$f->get_contextid()}/local_badger/content";
                           $filename = $f->get_filename();
                           $url .= $f->get_filepath().$f->get_itemid().'/'.$filename;
                       }else{
                          $f->delete(); 
                       }
                   }
                }
            }
        }
        if($valid){
            $new = new stdClass();
            $new->name=$formdata->name;
            $new->description=$formdata->description;
            $new->criteria=$formdata->criteria['text'];
            $new->collection=$formdata->collection;
            $new->level=$formdata->level;
            $new->nickname=$formdata->nickname;
            $new->courseid=$formdata->courseid;
            $allowed = array_combine($formdata->courserestrictions,$formdata->courserestrictions);
            $allowed[$formdata->courseid]=$formdata->courseid;
            $new->courserestrictions=implode(',',$allowed);
            $new->image=$url;
            $DB->insert_record('block_badger_badges',$new);
            redirect($returnurl);
        }else{
            $msg .= get_string('incorrectbadgespec','block_badger');
        }
    }else{
        // its an edit
        $new = new stdClass();
        if(isset($formdata->name)){
          $new->name=$formdata->name;  
        }
        $new->id=$formdata->bid;
        $new->description=$formdata->description;
        $new->criteria=$formdata->criteria['text'];
        $new->collection=$formdata->collection;
        $allowed = array_combine($formdata->courserestrictions,$formdata->courserestrictions);
        $allowed[$formdata->courseid]=$formdata->courseid;
        $new->courserestrictions=implode(',',$allowed);
        $new->nickname=$formdata->nickname;
        $new->level=$formdata->level;
        $DB->update_record('block_badger_badges',$new);
        redirect($returnurl);
    }
}

echo $OUTPUT->header();
echo $OUTPUT->box_start('generalbox');

$can_manage_badges = has_capability('moodle/block:edit', $context, $USER->id);
if($can_manage_badges){
    if($msg){
        echo html_writer::tag('h2',$msg);
    }
    echo html_writer::tag('h2',$title);
    $mform->display();
}

echo html_writer::tag('br','').html_writer::tag('h2',get_string('badgereview','block_badger'));
$badges = $DB->get_records('block_badger_badges', array('courseid'=>$courseid),"deleted,name,level");        

if($badges){
    foreach($badges as $b){
        $badge = new badge($b->id);
        if($b->id==$retire){
           $badge->retire(1); 
        }elseif($b->id==$unretire){
           $badge->retire(0); 
        }
        echo $badge->details(html_writer::tag('img','',array(width=>'150px','src'=>$badge->badge->image)));
                                                                                                     
        echo $badge->status($PAGE->url);
    }
}
echo $OUTPUT->box_end();
echo $OUTPUT->footer();

?>