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
 * Strings for component 'block_badger', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   block_badger
 * @2013 Iain Checkland (@iaincheckland)
 * @license   http://www.gnu.org/copyleft/gpl.badger GNU GPL v3 or later
 */

$string['pluginname'] = 'Badger';
$string['addbadge'] = 'Add Badge';
$string['editbadge'] = 'Edit Badge';
$string['badgereport'] = 'Badge Report';
$string['badge'] = 'Badge';
$string['congratulations'] = 'Congratulations';

$string['newbadgerblock'] = '(new badger block)';
$string['justify']='Justify your claim to this badge:';
$string['spec']='Uploaded badges must by <b>square png files</b>';
$string['criteria']='Award Criteria';
$string['criteria_help']='Detailed explaination of the criteria for awarding the badge ';
$string['description']='Description of badge';
$string['description_help']='A brief sentence of what the badge is awarded for';
$string['name']='Badge Short Name';
$string['name_help']='Give a short (preferably one word) name for the badge';
$string['nickname']='Badge preferred nickname (optional)';
$string['nickname_help']='Useful if you want a badge to be labelled, say, Assistant Producer instead of Producer 1';
$string['level']='Level of badge';  
$string['level_help']='Set at 0 unless part of a multi-levelled award. Higher level badges will supercede lower level badges of same name.';  
$string['collection']='Name of collection (optional)';
$string['collection_help']='Badge in collections can be awarded and summarised outside of this course';
$string['courserestrictions_help']='Badge additionally claimable in selected courses (hold down ctrl key to select more than one listed course)';
$string['courserestrictions']='Additionally Claimable from';
$string['alsoclaimablefrom']='Also Claimable from: ';
$string['definedin']='Defined in';
$string['collections']='Collections to display (optional, comma seperated)';
$string['showcompletion']='Show completion progress bars';
$string['nobadgeerror']='No badge exists: ';
$string['notallowed']='Error: you are not entitled to claim this badge';
$string['cannotcreatebadge']='You cannot currently create badges in this collection here';
$string['incorrectbadgespec']='<h2>Your file is not the correct type/dimensions</h2>';
$string['badgereview']='Badge Review';
$string['badgepack']='Your Badgepack';
$string['managebadges']='Manage Badges';
$string['notawarded']='This badge hasn\'t been awarded to anyone yet';
$string['youmay']='You may {$a->action} or ';
$string['deleteentirely']='delete the badge entirely';
$string['claimingurl']='Claiming URL: ';
$string['awarded']='You have been awarded the {$a->badge} badge';
$string['clickclaim']='Click on the badge to send it to your badgepack.';
$string['alreadyawarded']='This badge has already been issued to {$a->count} people.';
$string['retirebadge']='retire the badge';
$string['minorchanges']='minor changes to the description/criteria';
$string['make']='make {$a->action}';
$string['clickdetails']=': Click for details';
$string['makechanges']='make changes to badge details';
$string['help2a']='>> useful help image';
$string['retirement']='This badge is currently in retirement';
$string['previouslyawarded']='It was previously awarded to {$a->count} people. You may bring it ';
$string['confirmrevoke']='Confirm revoke badge ';
$string['from']='from';
$string['badgedeleted']='Badge Deleted!';
$string['issuername']='Badge Issuer Name: ';
$string['issuerorg']='Badge Issuer Organisation: ';
$string['issueremail']='Badge Issuer Name: ';
$string['defaultissuername']='Moodle';
$string['defaultissuerorg']='Your Organisation';
$string['defaultissueremail']='Badger@yourhost.com';
$string['explainissuername']='Enter, say, the name of your moodle server that will be baked into badges for external backpacks';
$string['explainissuerorg']='Enter the name of your organisation that will be baked into badges for external backpacks';
$string['explainissueremail']='Enter a static email address that will be baked into badges for external backpacks e.g. office@your.org';
$string['modernbrowser']='You may not be able push your badge to an external badgepack using an old browser, recommend a modern browser like Firefox v18.0 or Chrome v24.0';