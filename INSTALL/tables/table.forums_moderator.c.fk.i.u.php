<?php
/**
 * table.forums_moderator.c.fk.i.u.php
 *
 * `[PREFIX]_forums_moderator` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable(FORUM_MODERATOR_TABLE);

require_once 'includes/fkLibs/authorForeignKey.php';

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$forumModeratorTableCfg = array(
    'fields' => array(
        'id'       => array('type' => 'int(11)',     'null' => false, 'unsigned' => true, 'autoIncrement' => true),
        'userId'   => array('type' => 'varchar(20)', 'null' => true,  'default' => '\'\''),
        'forum'    => array('type' => 'int(5)',      'null' => false, 'unsigned' => true),
    ),
    'primaryKey' => array('id'),
    'index' => array(
        'userId'   => 'userId'
    ),
    'engine' => 'InnoDB'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    if ($dbTable->tableExist())
        $dbTable->checkIntegrity();
    else
        $dbTable->setJqueryAjaxResponse('NO_TABLE_TO_CHECK_INTEGRITY');
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Convert charset and collation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkAndConvertCharsetAndCollation') {
    if ($dbTable->tableExist())
        $dbTable->checkAndConvertCharsetAndCollation();
    else
        $dbTable->setJqueryAjaxResponse('NO_TABLE_TO_CONVERT');
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table drop
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'drop' && $dbTable->tableExist())
    $dbTable->dropTable();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

// install /update 1.8
if ($process == 'install' || ($process == 'createTable' && ! $dbTable->tableExist()))
    $dbTable->createTable($forumModeratorTableCfg);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Add foreign key of table
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'addForeignKey') {
    if (! $dbTable->foreignKeyExist('FK_forumModerator_userId'))
        addAuthorIdForeignKey('forumModerator', 'userId', $keepUserId = false);
}

?>