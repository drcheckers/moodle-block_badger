<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('block_badger_issuer_name', get_string('issuername', 'block_badger'),
                       get_string('explainissuername', 'block_badger'), get_string('defaultissuername', 'block_badger'), PARAM_TEXT));
    $settings->add(new admin_setting_configtext('block_badger_issuer_org', get_string('issuerorg', 'block_badger'),
                       get_string('explainissuerorg', 'block_badger'),get_string('defaultissuerorg', 'block_badger'), PARAM_TEXT));
    $settings->add(new admin_setting_configtext('block_badger_issuer_email', get_string('issueremail', 'block_badger'),
                       get_string('explainissueremail', 'block_badger'), get_string('defaultissueremail', 'block_badger'), PARAM_TEXT));


}
?>