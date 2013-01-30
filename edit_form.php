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
 * Form for editing badger block instances.
 *
 * @copyright 2009 Iain Checkland
 * @license   http://www.gnu.org/copyleft/gpl.banners GNU GPL v3 or later
 */
class block_badger_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        // Fields for editing badger block title and contents.
              
        // comma seperated list of display groups - otherwise just badges from course  
        $mform->addElement('text', 'config_collections', get_string('collections', 'block_badger'));
        $mform->setType('config_collections', PARAM_TEXT); 
        $mform->setDefault('config_collections', '');
        
        $mform->addElement('selectyesno', 'config_showcompletion', get_string('showcompletion', 'block_badger'));
        $mform->setDefault('config_showcompletion', 0);
        
    }

}
