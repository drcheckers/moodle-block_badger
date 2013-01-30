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


require_once('class_badge.php');

class block_badger extends block_base {

    function init() {
        global $PAGE;
        $this->title = get_string('pluginname', 'block_badger');         
    }

    function applicable_formats() {
        return array('course' => true);
    }

    function specialization() {
        $this->title = get_string('pluginname','block_badger'); 
    }

    function instance_allow_multiple() {
        return false;
    }

    function get_content() {
        global $USER,$COURSE,$FULLSCRIPT,$PAGE,$CFG,$DB;
        if ($this->content !== NULL) {
            return $this->content;
        }
        
        if(!isset($this->config))$this->config = new stdClass();
        $this->config->groups= (isset($this->config->groups)) ? $this->config->groups : '';
        $this->config->showcompletion= (isset($this->config->showcompletion)) ? $this->config->showcompletion : '';
        
        $PAGE->requires->css('/blocks/badger/styles.css');
        $PAGE->requires->js('/blocks/badger/badger.js');
        $return_url = $FULLSCRIPT;
        $this->content = new stdClass;
        
        $this->content->footer = html_writer::tag('p',html_writer::tag('a',get_string('badgepack','block_badger'),array('href'=>'http://beta.openbadges.org/')));
        $context = get_context_instance(CONTEXT_BLOCK, $this->instance->id);
        if($can_manage_badges = has_capability('moodle/block:edit', $context, $USER->id)){
            $this->content->footer .= html_writer::tag('p',html_writer::tag('a',get_string('managebadges','block_badger'),array('href'=>$CFG->wwwroot . '/blocks/badger/add.php?courseid=' . $COURSE->id . '&id=' . $this->instance->id . '&returnurl=' . $return_url)));
        }
        
        if($this->config->collections){
            $groups = explode(',',$this->config->collections);
            foreach($groups as $g){
                if($badges = $DB->get_records('block_badger_badges',array('deleted'=>0,'collection'=>trim($g)),'id','id')){
                    $this->content->text .= $this->rendergroup($badges,$g);
                }
            }
        }else{
            $badges = $DB->get_records('block_badger_badges',array('deleted'=>0,'courseid'=>$COURSE->id),'level,id','id');
            $this->content->text .= $this->rendergroup($badges);
        }
        return $this->content;
    }
    
    function rendergroup($bids,$name=''){
        global $USER;
        $out ='';
        if($bids){
            if($name){
                $out .= html_writer::tag('h4',$name);    
            }        
            $max = 0;
            foreach($bids as $b){
                $badge = new badge($b->id);
                $blvl=$badge->level(true);
                if($this->config->showcompletion){
                    $uid = $badge->uid(); 
                    $issues[$uid]=$badge->issued();
                    $badges[$uid]=$badge;
                }  
                $lvl = $badge->level(); 
                if(isset($got[$badge->name()])){
                    if($got[$badge->name()]<$lvl){
                        // found a better level than previously stored up so update image
                        $images[$badge->name()]=$badge->render(40,$USER->id);
                        $got[$badge->name()]=$lvl; 
                        unset($extra[$badge->name()]);
                    }elseif($blvl>$got[$badge->name()]){
                        $extra[$badge->name()]=$badge->render(40,$USER->id);
                    }
                }else{
                    $images[$badge->name()]=$badge->render(40,$USER->id);
                    $got[$badge->name()]=$lvl;
                }
            }
            if($this->config->showcompletion){   
                $max = max($issues);
                $out .= html_writer::start_tag('table',array('style'=>'margin:1px;padding:1px','width'=>'100%')); 
                foreach($badges as $k=>$b){
                    $out .= html_writer::tag('tr',
                                html_writer::tag('td',$k,array('style'=>'margin:1px;padding:1px')).
                                html_writer::tag('td',$b->progress($issues[$k],$max),array('style'=>'margin:1px;padding:1px','width'=>'100%')).
                                html_writer::tag('td',$issues[$k],array('style'=>'margin:1px;padding:1px')),array('style'=>'margin:1px;padding:1px'),array('style'=>'margin:1px;padding:1px'));
                }
                $out .= html_writer::end_tag('table');
            } 
            $out .= html_writer::start_tag('ul',array('style'=>'margin:0.5em')); 
            foreach($images as $k=>$n){
                 $out .= html_writer::tag('li',$n, array('style'=>'vertical-align:top;display:inline-block;border:' . $style .';height:100%;overflow:hidden;'));
                 if($got[$k]>0 && $extra[$k]){
                      $out .= html_writer::tag('li',$extra[$k], array('style'=>'vertical-align:top;display:inline-block;height:100%;overflow:hidden;'));
                 } 
            }
            $out .= html_writer::end_tag('ul') . html_writer::tag('hr',''); 
        }
        return $out;   
    }
            
    function instance_delete() {
        global $DB;
        $fs = get_file_storage();
        $fs->delete_area_files($this->context->id, 'block_badger');
        return true;
    }

    public function instance_can_be_docked() {
    	return true;
    }
}