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
 * minimalistic edit form
 *
 * @package   block_private_files
 * @2013 Iain Checkland (@iaincheckland)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

class badge_upload_form extends moodleform {
    function definition() {
        global $DB;
        $mform = $this->_form;

        $data           = $this->_customdata['data'];
        
        if(!isset($data->image)){
            $mform->addElement('html', html_writer::tag('p',get_string('spec','block_badger')));
            $filemanager_options = array();
            $filemanager_options['return_types'] = 3;
            $filemanager_options['accepted_types'] = array('.png');
            $filemanager_options['maxbytes'] = 0;
            $filemanager_options['maxfiles'] = 1;
            $filemanager_options['mainfile'] = false;        
            
            $mform->addElement('filepicker', 'files', get_string('uploadafile'), null, $filemanager_options);
        }else{
            $mform->addElement('html', html_writer::tag('img','',array('src'=>$data->image)));
        }
        
        
        $mform->addElement('text', 'name', get_string('name','block_badger'), array('size'=>12));
        if($data->edit && isset($data->name)){
            $mform->setDefault('name', $data->name);
            $mform->hardFreeze('name');
            $mform->addElement('hidden', 'name', $data->name);
        }else{
            $mform->setDefault('name', '');
        }
        $mform->addHelpButton('name', 'name', 'block_badger');
        
        $mform->addElement('text', 'description', get_string('description','block_badger'), array('size'=>50));
        $mform->setDefault('description', isset($data->description)?$data->description:'');
        $mform->addHelpButton('description', 'description', 'block_badger');
        
        $mform->addElement('editor', 'criteria', get_string('criteria', 'block_badger'))->setValue( array('text' => isset($data->criteria)?$data->criteria:''));
        $mform->setType('criteria', PARAM_RAW);
        $mform->addHelpButton('criteria', 'criteria', 'block_badger');
        
        $mform->addElement('select', 'level', get_string('level','block_badger'), array(0,1,2,3,4,5,6,7,8,9,10));
        $mform->setType('level', PARAM_INT); 
        $mform->setDefault('level', isset($data->level)?$data->level:0);
        $mform->addHelpButton('level', 'level', 'block_badger');
        
        $mform->addElement('text', 'nickname', get_string('nickname','block_badger'), array('size'=>12));
        $mform->setDefault('nickname', '');
        $mform->addHelpButton('nickname', 'nickname', 'block_badger');
        
        $mform->addElement('text', 'collection', get_string('collection','block_badger'), array('size'=>50));
        $mform->setDefault('collection', isset($data->collection)?$data->collection:'');
        $mform->addHelpButton('collection', 'collection', 'block_badger');
        
        
        
        $courses = $DB->get_records('course',null,'','id,fullname');
        foreach($courses as $c){
            $cs[$c->id]=$c->fullname;
        }
        $select = $mform->addElement('select', 'courserestrictions', get_string('courserestrictions','block_badger'), $cs);
        $select->setMultiple(true);
        $select->setSelected(explode(',',isset($data->courserestrictions)?$data->courserestrictions:$data->courseid));
        $mform->addHelpButton('courserestrictions', 'courserestrictions', 'block_badger');
        
        
        $mform->addElement('hidden', 'id', $data->block_id);
        $mform->addElement('hidden', 'courseid', $data->courseid);
        $mform->addElement('hidden', 'bid', $data->id);
        $mform->addElement('hidden', 'returnurl', $data->returnurl);
        $this->add_action_buttons(true, get_string('savechanges'));

        $this->set_data($data);
    }
    
    function validation($data, $files) {
        global $CFG;
        $errors = array();
        return $errors;
    }
}