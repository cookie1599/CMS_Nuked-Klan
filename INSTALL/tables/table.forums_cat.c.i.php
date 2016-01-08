<?php
/**
 * table.forums_cat.c.i.php
 *
 * `[PREFIX]_forums_cat` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_forums_cat');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$forumCatTableCfg = array(
    'fields' => array(
        'id'     => array('type' => 'int(11)',      'null' => false, 'autoIncrement' => true),
        'nom'    => array('type' => 'varchar(100)', 'default' => 'NULL'),
        'image'  => array('type' => 'varchar(200)', 'null' => false, 'default' => '\'\''),
        'ordre'  => array('type' => 'int(5)',       'null' => false, 'default' => '\'0\''),
        'niveau' => array('type' => 'int(1)',       'null' => false, 'default' => '\'0\'')
    ),
    'primaryKey' => array('id'),
    'engine' => 'MyISAM'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table exist in 1.6.x version
    $dbTable->checkIntegrity();
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Convert charset and collation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkAndConvertCharsetAndCollation')
    $dbTable->checkAndConvertCharsetAndCollation();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table drop
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'drop' && $dbTable->tableExist())
    $dbTable->dropTable();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'install') {
    $dbTable->createTable($forumCatTableCfg);

    $sql='INSERT INTO `'. $this->_session['db_prefix'] .'_forums_cat` VALUES
        (1, \''. $this->_db->quote($this->_i18n['CATEGORY']) .' 1\', \'\', 0, 0);';

    $dbTable->insertData('INSERT_DEFAULT_DATA', $sql);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install / update 1.8
    if (! $dbTable->fieldExist('image'))
        $dbTable->addField('image', $forumCatTableCfg['fields']['image']);
}

?>