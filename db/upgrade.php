<?php
  function xmldb_block_badger_upgrade($oldversion = 0) {
    global $DB;
    $dbman = $DB->get_manager();

    $result = true;
    /// Add a new column nickname
    if ($result && $oldversion < 2013012803) {
        $table = new xmldb_table('block_badger_badges');
        $field = new xmldb_field('nickname', XMLDB_TYPE_CHAR, 50, null, null, null, null, 'name');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('courserestrictions', XMLDB_TYPE_TEXT, null, null, null, null, null, 'courseid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            $DB->execute("update {block_badger_badges} set courserestrictions=courseid where courserestrictions is null");
        }
        upgrade_plugin_savepoint(true, 2013012803, 'block','badger');

    }
    return $result;
}
?>
