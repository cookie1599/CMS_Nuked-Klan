<?php
/**
 * action.php
 *
 * Backend of Admin module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Admin', ADMINISTRATOR_ACCESS))
    return;


function main()
{
    global $user, $nuked, $language;

    $nbActions = 50;

    $sqlNbActions = mysql_query("SELECT id FROM " . $nuked['prefix'] . "_action");
    $count = mysql_num_rows($sqlNbActions);

    if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
    $start = $_REQUEST['p'] * $nbActions - $nbActions;

    echo '<div class="content-box">',"\n" //<!-- Start Content Box -->
    . '<div class="content-box-header"><h3>' . _ADMINACTION . '</h3>',"\n"
    . '<div style="text-align:right"><a href="help/' . $language . '/Action.php" rel="modal">',"\n"
    . '<img style="border: 0" src="help/help.gif" alt="" title="' . _HELP . '" /></a>',"\n"
    . '</div></div>',"\n"
    . '<div class="tab-content" id="tab2">',"\n";

    printNotification(_INFOACTION, '', $type = 'information', $back = false, $redirect = false);

    if ($count > $nbActions){
        echo "<table width=\"100%\"><tr><td>";
        number($count, $nbActions, "index.php?file=Admin&page=action");
        echo"</td></tr></table>\n";
    }

    echo '<br /><table><tr><td><b>' . _DATE . '</b>',"\n"
    . '</td><td><b>' . _INFORMATION . '</b>',"\n"
    . '</td></tr>',"\n";

    $sql = mysql_query("SELECT date, pseudo, action  FROM " . $nuked['prefix'] . "_action ORDER BY date DESC LIMIT " . $start . ", " . $nbActions);
    while (list($date, $users, $texte) = mysql_fetch_array($sql))
    {
        if($users != '')
        {
            $users = mysql_real_escape_string($users);
        
            $sql2 = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . $users . "'");
            list($pseudo) = mysql_fetch_array($sql2);
        }
        else $pseudo = 'N/A';

        $date = nkDate($date);
        $texte = $pseudo . ' ' . $texte;

        echo '<tr><td>' . $date . '</td>',"\n"
        . '<td>' . $texte . '</td></tr>',"\n";

    }

    echo '</table>';

    if ($count > $nbActions){
        echo "<table width=\"100%\"><tr><td>";
        number($count, $nbActions, "index.php?file=Admin&page=action");
        echo "</td></tr></table>";
    }

    echo '<div style="text-align: center"><br /><a class="buttonLink" href="index.php?file=Admin">' . _BACK . '</a></div></form><br /></div></div>',"\n";
    $theday = time();
    $compteur = 0;
    $delete = mysql_query("SELECT id, date  FROM " . $nuked['prefix'] . "_action ORDER BY date DESC");
    while (list($id, $date) = mysql_fetch_array($delete))
    {
        $limit_time = $date + 1209600;

        if ($limit_time < $theday)
        {
            $del = mysql_query("DELETE FROM " . $nuked['prefix'] . "_action WHERE id = '" . $id . "'");
            $compteur++;
        }
    }
    if ($compteur > 0)
    {
        if($compteur ==1) $text = $compteur. ' ' ._1NBRNOTACTION;
        else $text = $compteur . ' ' . _NBRNOTACTION;

        $upd = mysql_query("INSERT INTO ". $nuked['prefix'] ."_notification  (`date` , `type` , `texte`)  VALUES ('" . $theday . "', '3', '" . $text . "')");
    }
}


switch ($_REQUEST['op']) {
    case 'main':
        main();
        break;
    default:
        main();
        break;
}

?>