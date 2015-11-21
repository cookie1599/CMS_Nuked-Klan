<?php
/**
 * admin.php
 *
 * Backend of Survey module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Survey'))
    return;


function add_sondage() {
    global $language;

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADDPOLL . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Survey.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\">\n";

        nkAdminMenu(2);

        echo "<form method=\"post\" action=\"index.php?file=Survey&amp;page=admin&amp;op=send_sondage\">\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
        . "<tr><td align=\"right\"><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"40\" /></td></tr>\n"
        . "<tr><td>&nbsp;</td></tr>\n";

    for ($r = 0; $r < 13; $r++) {
        echo "<tr><td align=\"right\">" . _CHOICE . "&nbsp;" . $r . " : <input type=\"text\" name=\"option[]\" size=\"40\" /></td></tr>\n";
    } 

    echo "<tr><td>&nbsp;</td></tr>\n"
        . "<tr><td><b>" . _LEVEL . " :</b> <select name=\"niveau\">\n"
        . "<option>0</option><option>1</option><option>2</option>\n"
        . "<option>3</option><option>4</option><option>5</option>\n"
        . "<option>6</option><option>7</option><option>8</option>\n"
        . "<option>9</option></select></td></tr>\n"
        . "</table><div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _ADDTHISPOLL . "\" />\n"
        . "<a class=\"buttonLink\" href=\"index.php?file=Survey&amp;page=admin\">" . _BACK . "</a></div></form><br /></div></div>\n";
}

function send_sondage($titre, $option, $niveau) {
    global $nuked, $user;

    $time = time();
    $titre = mysql_real_escape_string(stripslashes($titre));

    $sql = mysql_query("INSERT INTO " . SURVEY_TABLE . " ( `sid` , `titre` , `date` , `niveau` ) VALUES ( '' , '" . $titre . "' , '" . $time . "' , '" . $niveau . "' )");
    $sql2 = mysql_query("SELECT sid FROM " . SURVEY_TABLE . " WHERE titre = '" . $titre . "'");
    list($poll_id) = mysql_fetch_array($sql2);

    for ($r = 0; $r < 13; $r++) {
        $vid = $r + 1;
        $options = $option[$r];
        $options = mysql_real_escape_string(stripslashes($options));

        if (!empty($options)) {
            $sql3 = mysql_query("INSERT INTO " . SURVEY_DATA_TABLE . " ( `sid` , `optionText` , `optionCount` , `voteID` ) VALUES ( '" . $poll_id . "' , '" . $options . "' , '' , '" . $vid . "' )");
        } 
    } 
    // Action
    $texteaction = _ACTIONADDSUR . ': ' . $titre . '.';
    $acdate = time();
    $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
    //Fin action
    echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . _POLLADD . "\n"
        . "</div>\n"
        . "</div>\n";
    $sql = mysql_query("SELECT sid FROM " . SURVEY_TABLE . " WHERE titre = '" . $titre . "' AND date='".$time."'");
    list($poll_id) = mysql_fetch_array($sql);
    echo "<script>\n"
        . "setTimeout('screen()','3000');\n"
        . "function screen() { \n"
        . "screenon('index.php?file=Survey&op=sondage&poll_id=".$poll_id."', 'index.php?file=Survey&page=admin');\n"
        . "}\n"
        . "</script>\n";
}

function del_sondage($poll_id) {
    global $nuked, $user;

    $sql = mysql_query("SELECT titre FROM " . SURVEY_TABLE . " WHERE sid = '" . $poll_id . "'");
    list($titre) = mysql_fetch_array($sql);
    $titre = mysql_real_escape_string(stripslashes($titre));
    $sql = mysql_query("DELETE FROM " . SURVEY_TABLE . " WHERE sid = '" . $poll_id . "'");
    $sql2 = mysql_query("DELETE FROM " . SURVEY_DATA_TABLE . " WHERE sid = '" . $poll_id . "'");
    $del_com = mysql_query("DELETE FROM " . COMMENT_TABLE . " WHERE im_id = '" . $poll_id . "' AND module = 'Survey'");
    // Action
    $texteaction = _ACTIONDELSUR . ': ' . $titre . '.';
    $acdate = time();
    $sqlaction = mysql_query("INSERT INTO " . $nuked['prefix'] . "_action  (`date`, `pseudo`, `action`)  VALUES ('" . $acdate . "', '" . $user[0] . "', '" . $texteaction . "')");
    //Fin action
    echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . _POLLDEL . "\n"
        . "</div>\n"
        . "</div>\n";
    redirect('index.php?file=Survey&page=admin', 2);
}

function edit_sondage($poll_id) {
    global $nuked, $language;

    $sql = mysql_query("SELECT titre, niveau FROM " . SURVEY_TABLE . " WHERE sid = '" . $poll_id . "'");
    list($titre, $niveau) = mysql_fetch_array($sql);

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINPOLL . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Survey.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Survey&amp;page=admin&amp;op=modif_sondage\">\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
        . "<tr><td align=\"right\"><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"40\" value=\"" . $titre . "\" /></td></tr>\n"
        . "<tr><td>&nbsp;</td></tr>\n";

    $sql2 = mysql_query("SELECT optionText FROM " . SURVEY_DATA_TABLE . " WHERE sid = '" . $poll_id . "' ORDER BY voteID ASC");
    $r = 0;
    while (list($optiontext) = mysql_fetch_array($sql2)) {
        $r++;
        echo "<tr><td align=\"right\">" . _CHOICE . "&nbsp;" . $r . " : <input type=\"text\" name=\"option[" . $r . "]\" size=\"40\" value=\"" . $optiontext . "\" /></td></tr>\n";
    } 

    $r++;

    echo "<tr><td align=\"right\">" . _CHOICE . "&nbsp;" . $r . " : <input type=\"text\" name=\"newoption\" size=\"40\" /></td></tr>\n"
        . "<tr><td>&nbsp;<input type=\"hidden\" name=\"poll_id\" value=\"" . $poll_id . "\" /></td></tr>\n"
        . "<tr><td><b>" . _LEVEL . " :</b> <select name=\"niveau\"><option>" . $niveau . "</option>\n"
        . "<option>0</option><option>1</option><option>2</option>\n"
        . "<option>3</option><option>4</option><option>5</option>\n"
        . "<option>6</option><option>7</option><option>8</option>\n"
        . "<option>9</option></select></td></tr>\n"
        . "</table><div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _MODIFTHISPOLL . "\" />\n"
        . "<a class=\"buttonLink\" href=\"index.php?file=Survey&amp;page=admin\">" . _BACK . "</a></div></form><br /></div></div>\n";
}

function modif_sondage($poll_id, $titre, $option, $newoption, $niveau) {
    global $nuked, $user;

    $titre = mysql_real_escape_string(stripslashes($titre));

    $sql = mysql_query("UPDATE " . SURVEY_TABLE . " SET titre = '" . $titre . "' , niveau = '" . $niveau . "' WHERE sid = '" . $poll_id . "'");

    for ($r = 0; $r < 13; $r++) {
        $options = $option[$r];
        $options = mysql_real_escape_string(stripslashes($options));

        if (!empty($options)) {
            $upd = mysql_query("UPDATE " . SURVEY_DATA_TABLE . " SET optionText = '" . $options . "' WHERE sid = '" . $poll_id . "' AND voteID = '" . $r . "'");
        } else {
            $del = mysql_query("DELETE FROM " . SURVEY_DATA_TABLE . " WHERE sid = '" . $poll_id . "' AND voteID = '" . $r . "'");
        } 
    } 

    if (!empty($newoption)) {
        $newoption = mysql_real_escape_string(stripslashes($newoption));
        $sql2 = mysql_query("SELECT voteID FROM " . SURVEY_DATA_TABLE . " WHERE sid = '" . $poll_id . "' ORDER BY voteID DESC LIMIT 0, 1");
        list($voteID) = mysql_fetch_array($sql2);
        $s = $voteID + 1;
        $sql3 = mysql_query("INSERT INTO " . SURVEY_DATA_TABLE . " ( `sid` , `optionText` , `optionCount` , `voteID` ) VALUES ( '" . $poll_id . "' , '" . $newoption . "' , '' , '" . $s . "' )");
    }
    // Action
    $texteaction = _ACTIONMODIFSUR . ': ' . $titre . '.';
    $acdate = time();
    $sqlaction = mysql_query("INSERT INTO " . $nuked['prefix'] . "_action  (`date`, `pseudo`, `action`) VALUES ('" . $acdate . "', '" . $user[0]."', '" . $texteaction . "')");
    //Fin action
    echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . _POLLMODIF . "\n"
        . "</div>\n"
        . "</div>\n";
    echo "<script>\n"
        . "setTimeout('screen()','3000');\n"
        . "function screen() { \n"
        . "screenon('index.php?file=Survey&op=sondage&poll_id=" . $poll_id . "', 'index.php?file=Survey&page=admin');\n"
        . "}\n"
        . "</script>\n";
}

function main(){
    global $nuked, $language;

    echo "<script type=\"text/javascript\">\n"
        . "<!--\n"
        . "\n"
        . "function del_poll(titre, id)\n"
        . "{\n"
        . "if (confirm('" . _DELETEPOLL . " '+titre+' ! " . _CONFIRM . "'))\n"
        . "{document.location.href = 'index.php?file=Survey&page=admin&op=del_sondage&poll_id='+id;}\n"
        . "}\n"
        . "\n"
        . "// -->\n"
        . "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINPOLL . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Survey.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\">\n";

        nkAdminMenu(1);

        echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
        . "<tr>\n"
        . "<td align=\"center\"><b>" . _TITLE . "</b></td>\n"
        . "<td align=\"center\"><b>" . _DATE . "</b></td>\n"
        . "<td align=\"center\"><b>" . _LEVEL . "</b></td>\n"
        . "<td align=\"center\"><b>" . _EDIT . "</b></td>\n"
        . "<td align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

    $sql = mysql_query('SELECT sid, titre, date, niveau FROM ' . SURVEY_TABLE . ' ORDER BY sid DESC');
    $count = mysql_num_rows($sql);
    while (list($poll_id, $titre, $date, $niveau) = mysql_fetch_array($sql)) {
        $date = nkDate($date);
        $titre = printSecuTags($titre);


        echo "<tr>\n"
            . "<td>" . $titre . "</td>\n"
            . "<td align=\"center\">" . $date . "</td>\n"
            . "<td align=\"center\">" . $niveau . "</td>\n"
            . "<td align=\"center\"><a href=\"index.php?file=Survey&amp;page=admin&amp;op=edit_sondage&amp;poll_id=" . $poll_id . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISPOLL . "\" /></a></td>\n"
            . "<td align=\"center\"><a href=\"javascript:del_poll('" . mysql_real_escape_string(stripslashes($titre)) . "', '" . $poll_id . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISPOLL . "\" /></a></td></tr>\n";
    } 

    if ($count == 0) {
        echo "<tr><td colspan=\"5\" align=\"center\">" . _NOPOOL . "</td></tr>\n";
    } 

    echo "</table><div style=\"text-align: center;\"><br /><a class=\"buttonLink\" href=\"index.php?file=Admin\">" . _BACK . "</a></div><br /></div></div>";
}

function main_pref() {
    global $nuked, $language;

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINPOLL . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Survey.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\">\n";

        nkAdminMenu(3);

        echo "<form method=\"post\" action=\"index.php?file=Survey&amp;page=admin&amp;op=change_pref\">\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
        . "<tr><td>" . _POLLTIME . " :</td><td><input type=\"text\" name=\"sond_delay\" size=\"2\" value=\"" . $nuked['sond_delay'] . "\" /></td></tr>\n"
        . "</table><div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _SEND . "\" />\n"
        . "<a class=\"buttonLink\" href=\"index.php?file=Survey&amp;page=admin\">" . _BACK . "</a></div></form><br /></div></div>\n";
}

function change_pref($sond_delay) {
    global $nuked, $user;

    $upd = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $sond_delay . "' WHERE name = 'sond_delay'");
    // Action
    $texteaction = _ACTIONCONFSUR . '.';
    $acdate = time();
    $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
    //Fin action
    echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . _PREFUPDATED . "\n"
        . "</div>\n"
        . "</div>\n";
    redirect('index.php?file=Survey&page=admin', 2);
}

function nkAdminMenu($tab = 1)
{
    global $language, $user, $nuked;

    $class = ' class="nkClassActive" ';
?>
    <div class= "nkAdminMenu">
        <ul class="shortcut-buttons-set" id="1">
            <li <?php echo ($tab == 1 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Survey&amp;page=admin">
                    <img src="modules/Admin/images/icons/speedometer.png" alt="icon" />
                    <span><?php echo _POLLOF; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 2 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Survey&amp;page=admin&amp;op=add_sondage">
                    <img src="modules/Admin/images/icons/statistiques.png" alt="icon" />
                    <span><?php echo _ADDPOLL; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 3 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Survey&amp;page=admin&amp;op=main_pref">
                    <img src="modules/Admin/images/icons/process.png" alt="icon" />
                    <span><?php echo _PREFS; ?></span>
                </a>
            </li>
        </ul>
    </div>
    <div class="clear"></div>
<?php
}


switch ($_REQUEST['op']) {
    case 'add_sondage':
        add_sondage();
        break;

    case 'edit_sondage':
        edit_sondage($_REQUEST['poll_id']);
        break;

    case 'del_sondage':
        del_sondage($_REQUEST['poll_id']);
        break;

    case 'send_sondage':
        send_sondage($_REQUEST['titre'], $_REQUEST['option'], $_REQUEST['niveau']);
        break;

    case 'modif_sondage':
        modif_sondage($_REQUEST['poll_id'], $_REQUEST['titre'], $_REQUEST['option'], $_REQUEST['newoption'], $_REQUEST['niveau']);
        break;

    case 'main_pref':
        main_pref();
        break;

    case 'change_pref':
        change_pref($_REQUEST['sond_delay']);
        break;

    default:
        main();
        break;
}

?>