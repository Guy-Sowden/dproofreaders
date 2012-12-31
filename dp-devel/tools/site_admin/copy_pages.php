<?php
$relPath='./../../pinc/';
include_once($relPath.'base.inc');
include_once($relPath.'misc.inc');
include_once($relPath.'theme.inc');
include_once($relPath.'user_is.inc');
include_once($relPath.'DPage.inc'); // project_recalculate_page_counts
include_once($relPath.'Project.inc');
include_once($relPath.'user_project_info.inc');
include_once($relPath.'wordcheck_engine.inc');

require_login();

if ( !user_is_a_sitemanager() )
{
    die( "You are not authorized to invoke this script." );
}

$copy_pages_url = "$code_url/tools/site_admin/copy_pages.php";

$no_stats=1;

$extra_args["css_data"] = "
    table.copy { margin-left: 3em;}
    table.copy th { text-align: left;}
    input[type=text] { font-family: monospace; }
";

$title = _("Copy Pages from One Project to Another");
output_header($title, NO_STATSBAR, $extra_args);
echo "<br>\n";
echo "<h1>" . $title . "</h1>\n";
echo "<hr>\n";

// Validate the $projectid_ and $from_image_ 'by hand'
$projectid_  = array_get( $_POST, 'projectid_',  NULL );
if (is_array($projectid_))
    foreach($projectid_ as $which => $projectid)
        validate_projectID("projectid_[$which]", $projectid);
$from_image_ = array_get( $_POST, 'from_image_', NULL );
if (is_array($from_image_))
    foreach($from_image_ as $which => $filename)
        validate_page_image_filename("from_image_[$which]", $filename);

$action = get_enumerated_param($_POST, 'action', 'showform', array('showform', 'showagain', 'check', 'docopy'));
$page_name_handling = get_enumerated_param($_POST, 'page_name_handling', null, array('PRESERVE_PAGE_NAMES', 'RENUMBER_PAGES'), true);
$transfer_notifications = get_integer_param($_POST, 'transfer_notifications', 0, 0, 1);
$add_deletion_reason = get_integer_param($_POST, 'add_deletion_reason', 0, 0, 1);
$merge_wordcheck_files = get_integer_param($_POST, 'merge_wordcheck_files', 0, 0, 1);
$repeat_project = get_enumerated_param($_POST, 'repeat_project', null, array('TO', 'FROM', 'NONE'), true);

switch ($action)
{
    case 'showform':
        display_form($projectid_, $from_image_, $page_name_handling, 
                     $transfer_notifications, $add_deletion_reason,
                     $merge_wordcheck_files, $repeat_project, FALSE);
        break;

    case 'showagain':
        display_form($projectid_, $from_image_, $page_name_handling, 
                     $transfer_notifications, $add_deletion_reason,
                     $merge_wordcheck_files, $repeat_project, TRUE);
        break;

    case 'check':
        do_stuff( $projectid_, $from_image_, $page_name_handling, 
                  $transfer_notifications, $add_deletion_reason, 
                  $merge_wordcheck_files, TRUE );

        echo "<form method='post' action='" . htmlspecialchars($copy_pages_url, ENT_QUOTES). "'>\n";
        display_hiddens($projectid_, $from_image_, $page_name_handling, 
                        $transfer_notifications, $add_deletion_reason, 
                        $merge_wordcheck_files);
        echo "\n<input type='hidden' name='action' value='docopy'>";
        echo "\n<input type='submit' name='submit_button' value='" . attr_safe(_("Do it!")) ."'>";
        echo "\n</form>";
        echo "<div style='height: 4em;'>&nbsp;</div>"; // Spacer
        break;

    case 'docopy':
        do_stuff( $projectid_, $from_image_, $page_name_handling, 
                  $transfer_notifications, $add_deletion_reason, 
                  $merge_wordcheck_files, FALSE );

        echo "<hr>\n";
        $url = "$code_url/tools/project_manager/page_detail.php?project={$projectid_['to']}&amp;show_image_size=0";
        echo sprintf(_("<p><a href='%s'>Go to destination project's detail page</a></p>\n"), $url);
        echo "<form method='post' action='" . htmlspecialchars($copy_pages_url, ENT_QUOTES). "'>\n";
        echo "<fieldset>\n";
        echo "<legend>" . _("Copy more pages...") . "</legend>";
        echo "<input type='radio' name='repeat_project' id='rp-1' value='FROM'>";
        echo "<label for='rp-1'>" . _("From the same project") . "</label>";
        echo "<input type='radio' name='repeat_project' id='rp-2' value='TO'>";
        echo "<label for='rp-2'>" . _("To the same project") . "</label>";
        // Or explicitly just go back to the original blank form by default
        echo "<input type='radio' name='repeat_project' id='rp-3' value='NONE' CHECKED>";
        echo "<label for='rp-3'>" . _("Neither") . "</label>";

        display_hiddens($projectid_, $from_image_, $page_name_handling, 
                        $transfer_notifications, $add_deletion_reason, 
                        $merge_wordcheck_files);
        
        echo "<input type='hidden' name='action' value='showagain'>\n";
        echo "<input type='submit' name='submit_button' value='" . attr_safe(_("Again!")) . "'>\n";
        echo "</fieldset>\n";
        echo "</form>\n";
        echo "<div style='height: 4em;'>&nbsp;</div>"; // Spacer
        break;

    default:
        echo sprintf(_("Unexpected value for action: '%s'"), htmlspecialchars($action));
        break;
}

function display_form($projectid_, $from_image_, $page_name_handling, 
                      $transfer_notifications, $add_deletion_reason, 
                      $merge_wordcheck_files, $repeat_project, $repeating)
{
    global $copy_pages_url;
    echo "<form method='post' action='" . htmlspecialchars($copy_pages_url, ENT_QUOTES). "'>\n";
    echo "<table class='copy'>\n";

    // always leave the page numbers blank
    echo "<tr>\n";
    echo "<th>" . _("Copy Page(s):") . "</th>\n";
    echo "<td><input type='text' name='from_image_[lo]' size='12'>";
    echo " &ndash; <input type='text' name='from_image_[hi]' size='12'>";
    echo " " . _("(leave these fields blank to copy all pages)");
    echo "</td></tr>\n";

    // if we are repeating, will want to fill one of these in
    $val = '';
    if ($repeating && $repeat_project == 'FROM')
    {
        $val = "value='" . attr_safe($projectid_['from']) . "'";
    }
    echo "<tr><th>" . _("Source project:") . "</th>\n";
    echo "<td><input type='text' name='projectid_[from]' size='28' $val> (projectid)</td></tr>\n";
    $val = '';
    if ($repeating && $repeat_project == 'TO')
    {
        $val = "value='" . attr_safe($projectid_['to']) . "'";
    }
    echo "<tr><th>" . _("Destination project:") . "</th>\n";
    echo "<td><input type='text' name='projectid_[to]' size='28' $val> (projectid)</td></tr>\n";

    // If we are repeating, we want the same buttons to be checked
    echo "<tr><td></td><td>\n";
    echo "<fieldset>\n";
    echo "<legend>" . _("Page number handling:") . "</legend>";

    if (!$repeating ||
        ($repeating && $page_name_handling == 'PRESERVE_PAGE_NAMES') )
    {
        $checked1 = 'CHECKED';
        $checked2 = '';
    } else {
        $checked1 = '';
        $checked2 = 'CHECKED';
    }

    echo "<input type='radio' name='page_name_handling' id='pnh-1' value='PRESERVE_PAGE_NAMES' $checked1>\n";
    echo "<label for='pnh-1'>" . _("Preserve page numbers") . "</label>";

    echo "<input type='radio' name='page_name_handling' id='pnh-2' value='RENUMBER_PAGES' $checked2>\n";
    echo "<label for='pnh-2'>" . _("Renumber pages") . "</label>\n";
    echo "</fieldset>\n";
    echo "</td></tr>\n";

    do_radio_button_pair( _("Transfer event notifications:"),
        "transfer_notifications", $repeating, $transfer_notifications );
    do_radio_button_pair( _("Add deletion reason to source project:"),
        "add_deletion_reason", $repeating, $add_deletion_reason  );
    do_radio_button_pair( _("Merge WordCheck files into destination project:"),
        "merge_wordcheck_files", $repeating, $merge_wordcheck_files );

    echo "<tr><td></td><td>";
    echo "<input type='hidden' name='action' value='check'>\n";
    echo "<input type='submit' name='submit_button' value='" . _("Check") . "'>";
    echo "</td></tr>";
    echo "</table>\n";
    echo "</form>";

    echo "<p><b>Note:</b> 'pages' are specified by their designation in the project table: e.g., '001.png'</p>\n";
    echo "<div style='height: 4em;'>&nbsp;</div>"; // Spacer
}

// Display table row with a fieldset containing a pair of radio buttons, one selected.
// NB $input_name must be a valid HTML ID (ie no spaces and shouldn't start with a number)
function do_radio_button_pair($prompt, $input_name, $repeating, $first_is_checked )
{
    if (!$repeating ||
        ($repeating && $first_is_checked) )
    {
        $checked1 = 'CHECKED';
        $checked2 = '';
    } else {
        $checked1 = '';
        $checked2 = 'CHECKED';
    }

    echo "<tr><td></td><td>\n";
    echo "<fieldset>\n";
    echo "<legend>$prompt</legend>\n";
    echo "<input type='radio' name='$input_name' id='${input_name}-1' value='1' $checked1>\n";
    echo "<label for='${input_name}-1'>" . _("Yes") . "</label>\n";
    echo "&nbsp;&nbsp;&nbsp;&nbsp; ";

    echo "<input type='radio' name='$input_name' id='${input_name}-2' value='0' $checked2>\n";
    echo "<label for='${input_name}-2'>" . _("No") . "</label>\n";
    echo "</fieldset>\n";
    echo "</td></tr>\n";
}

function display_hiddens($projectid_, $from_image_, $page_name_handling, 
                         $transfer_notifications, $add_deletion_reason,
                         $merge_wordcheck_files)
{
    echo "\n<input type='hidden' name='from_image_[lo]'        value='" . attr_safe($from_image_['lo']) . "'>";
    echo "\n<input type='hidden' name='from_image_[hi]'        value='" . attr_safe($from_image_['hi']) . "'>";
    echo "\n<input type='hidden' name='projectid_[from]'       value='" . attr_safe($projectid_['from']) . "'>";
    echo "\n<input type='hidden' name='projectid_[to]'         value='" . attr_safe($projectid_['to']) . "'>";
    echo "\n<input type='hidden' name='page_name_handling'     value='" . attr_safe($page_name_handling) . "'>";
    echo "\n<input type='hidden' name='transfer_notifications' value='" . attr_safe($transfer_notifications) . "'>";
    echo "\n<input type='hidden' name='add_deletion_reason'    value='" . attr_safe($add_deletion_reason) . "'>";
    echo "\n<input type='hidden' name='merge_wordcheck_files'  value='" . attr_safe($merge_wordcheck_files) . "'>";
}

function do_stuff( $projectid_, $from_image_, $page_name_handling, 
                   $transfer_notifications, $add_deletion_reason,
                   $merge_wordcheck_files, 
                   $just_checking )
{
    if ( is_null($projectid_) )
    {
        die( "Error: no projectid data supplied" );
    }

    $page_names_ = array();

    foreach ( array( 'from', 'to' ) as $which )
    {
        $res= mysql_query(
            sprintf("DESCRIBE %s",
            mysql_real_escape_string($projectid_[$which]))
        ) or die(mysql_error());

        $column_names = array();
        while ( $row = mysql_fetch_assoc($res) )
        {
            $column_names[] = $row['Field'];
        }
        $column_names_[$which] = $column_names;
    }
    $clashing_columns = array_intersect( $column_names_['from'], $column_names_['to'] );

    foreach ( array( 'from', 'to' ) as $which )
    {
        // clever use of $which above means we need label uses translated
        // separately, which is convenient, since 'to/from' could be mistaken
        // as indicating a range.
        echo "<h3>";
        switch ( $which )
        {
            case 'from': echo _("Source Project:");      break;
            case 'to':   echo _("Destination Project:"); break;
            default:
            // Shouldn't happen
            break;
        }
        echo "</h3>";

        echo "<table class='copy'>";

        $projectid = $projectid_[$which];

        echo "<tr><th>" . _("ProjectID:") . "</th><td>" . $projectid . "</td></tr>\n";

        $res = mysql_query(sprintf("
            SELECT nameofwork
            FROM projects
            WHERE projectid='%s'",
            mysql_real_escape_string($projectid)
        )) or die(mysql_error());

        $n_projects = mysql_num_rows($res);
        if ( $n_projects == 0 )
        {
            die( "projects table has no match for projectid='$projectid'" );
        }
        else if ( $n_projects > 1 )
        {
            die( "projects table has $n_projects matches for projectid='$projectid'. (Can't happen)" );
        }

        list($title) = mysql_fetch_row($res);

        echo "<tr><th>" . _("Title:") . "</th><td>" . $title . "</td></tr>\n";

        // ----------------------

        $res = mysql_query(sprintf("
            SELECT image, fileid
            FROM %s
            ORDER BY image",
            mysql_real_escape_string($projectid)
        )) or die(mysql_error());

        $n_pages = mysql_num_rows($res);

        echo "<tr><th>" . _("No. of pages:") . "</th><td>" . $n_pages . "</td></tr>\n";

        if ( $which == 'from' && $n_pages == 0 )
        {
            die( "project has no page data to extract" );
        }

        $all_image_values = array();
        $all_fileid_values = array();
        while ( list($image,$fileid) = mysql_fetch_row($res) )
        {
            $all_image_values[] = $image;
            $all_fileid_values[] = $fileid;
        }

        $all_image_values_[$which] = $all_image_values;
        $all_fileid_values_[$which] = $all_fileid_values;

        // ----------------------

        $n_columns = count($column_names_[$which]);
        echo "<tr><th>";
        echo "No. of columns:" . "</th><td>" . $n_columns . "</td></tr>\n";

        $extra_columns_[$which] = array_diff( $column_names_[$which], $clashing_columns );
        if ( count($extra_columns_[$which]) > 0 )
        {
            echo "<tr><th>" . _("Extra columns:");
            echo "<td><code>" . htmlspecialchars(implode( " ", $extra_columns_[$which])) . "</code></td></tr>\n";

            echo "<tr><td></td><td>";
            if ( $which == 'from' )
            {
                echo _("(These columns will simply be ignored.)");
            }
            else
            {
                echo _("(These columns will be given their default value.)");
            }
             echo "</td></tr>";
        }

        echo "</table>\n";

        // ----------------------

        if ( $which == 'from' )
        {
            $lo = trim($from_image_['lo']);
            $hi = trim($from_image_['hi']);

            if ( $lo == '' && $hi == '' )
            {
                $lo = $all_image_values[0];
                $hi = $all_image_values[ count($all_image_values) - 1 ];
            }
            elseif ( $hi == '' )
            {
                $hi = $lo;
            }

            $lo_i = array_search( $lo, $all_image_values );
            $hi_i = array_search( $hi, $all_image_values );

            if ( $lo_i === FALSE )
            {
                die( "project does not have a page with image='$lo'" );
            }

            if ( $hi_i === FALSE )
            {
                die( "project does not have a page with image='$hi'" );
            }

            if ( $lo_i > $hi_i )
            {
                die( "low end of range ($lo) is greater than high end ($hi)" );
            }

            $n_pages_to_copy = 1 + $hi_i - $lo_i;

            echo "<p>";
            echo sprintf( _("Pages to copy: %s &ndash; %s"),$lo, $hi );
            echo " " . sprintf(_("(%d pages)"), $n_pages_to_copy);
            echo "</p>\n";
        }
    }

    if ( $projectid_['from'] == $projectid_['to'] )
    {
        die( "You can't copy a project into itself." );
    }

    // ----------------------------------------------------

    if ( $page_name_handling == 'PRESERVE_PAGE_NAMES' )
    {
        // fine
    }
    elseif ( $page_name_handling == 'RENUMBER_PAGES' )
    {
        if ( count($all_fileid_values_['to']) == 0 )
        {
            $c_dst_format = '%03d';
            $c_dst_start_b = 1;
        }
        else
        {
            $max_dst_fileid = str_max( $all_fileid_values_['to'] );
            $max_dst_image  = str_max( $all_image_values_['to'] );
            $max_dst_image_base = preg_replace( '/\.[^.]+$/', '', $max_dst_image );
            $max_dst_base = (
                strcmp( $max_dst_fileid, $max_dst_image_base ) > 0
                ? $max_dst_fileid
                : $max_dst_image_base );
            $c_dst_format = '%0' . strlen($max_dst_base) . 'd';
            $c_dst_start_b = 1 + intval($max_dst_base);
        }
    }
    else
    {
        die( "bad page_name_handling" );
    }

    // The c_ prefix means that it only pertains to *copied* pages.

    $c_src_image_  = array();
    $c_src_fileid_ = array();
    $c_dst_image_  = array();
    $c_dst_fileid_ = array();

    for ( $i = $lo_i; $i <= $hi_i; $i++ )
    {
        $c_src_image = $all_image_values_['from'][$i];
        $c_src_fileid = $all_fileid_values_['from'][$i];

        if ( $page_name_handling == 'PRESERVE_PAGE_NAMES' )
        {
            $c_dst_fileid = $c_src_fileid;
            $c_dst_image  = $c_src_image;
        }
        elseif ( $page_name_handling == 'RENUMBER_PAGES' )
        {
            $c_src_image_ext = preg_replace( '/.*\./', '', $c_src_image );
            $c_dst_b = ( $i - $lo_i + $c_dst_start_b );
            $c_dst_fileid = sprintf( $c_dst_format, $c_dst_b );
            $c_dst_image  = "$c_dst_fileid.$c_src_image_ext";
        }
        else
        {
            assert( FALSE );
        }

        $c_src_image_[]  = $c_src_image;
        $c_src_fileid_[] = $c_src_fileid;
        $c_dst_image_[]  = $c_dst_image;
        $c_dst_fileid_[] = $c_dst_fileid;
    }

    $clashing_image_values = array_intersect( $c_dst_image_, $all_image_values_['to'] );
    if ( count($clashing_image_values) > 0 )
    {
        echo "<p><b>" . _("Page name collisions!") . "</b><br>";
        echo _("The destination project already has pages with these 'image' values:");
        echo "</p>\n";
        echo "<pre>\n";
        foreach ( $clashing_image_values as $clashing_image_value )
        {
            echo htmlspecialchars("    $clashing_image_value\n");
        }
        echo "</pre>\n";
        die( _("Aborting due to page name collisions!") );
    }

    $clashing_fileid_values = array_intersect( $c_dst_fileid_, $all_fileid_values_['to'] );
    if ( count($clashing_fileid_values) > 0 )
    {
        echo "<p><b>" . _("Page name collisions!") . "</b><br>";
        echo _("The destination project already has pages with these 'fileid' values:");
        echo "</p>\n";
        echo "<pre>\n";
        foreach ( $clashing_fileid_values as $clashing_fileid_value )
        {
            echo htmlspecialchars("    $clashing_fileid_value\n");
        }
        echo "</pre>\n";
        die( _("Aborting due to page name collisions!") );
    }

    echo "<p>";
    if ( $page_name_handling == 'PRESERVE_PAGE_NAMES' )
    {
        echo _("There don't appear to be any page name collisions.");
    }
    elseif ( $page_name_handling == 'RENUMBER_PAGES' )
    {
        echo _("As expected, there aren't any page name collisions.");
    }
    echo "</p>";

    // Report the settings/selections that were chosen

    echo "<h3>" . _("Per your request:") . "</h3>";

    echo "<ul>";
    echo "<li>" . _("Page Name Handling:");
    echo "&nbsp;<code>" . htmlspecialchars($page_name_handling) . "</code>";
    echo "</li>\n";

    echo "<li>";
    if ($transfer_notifications) 
    {
        echo _("Event notifications WILL be transferred");
    }
    else
    {
        echo _("Event notifications WILL NOT be transferred");
    }
    echo "</li>\n";

    echo "<li>\n";
    if ($add_deletion_reason) 
    {
        echo _("The following deletion reason will be added to the source project:");
        echo "&nbsp;&nbsp;<code>" . sprintf(_("'merged into %s'"), htmlspecialchars($projectid_['to'])) . "</code>";
    }
    else
    {
        echo _("A deletion reason WILL NOT be added to the source project");
    }
    echo "</li>\n";

    echo "<li>\n";
    if ($merge_wordcheck_files) 
    {
        echo _("The WordCheck files from the source project WILL be merged into the destination project");
    }
    else
    {
        echo _("The WordCheck files WILL NOT be merged");
    }
    echo "</li>\n";
    echo "</ul>\n";

    if ( $just_checking )
    {
        return;
    }

    // ---BEGIN COPY OPERATIONS-----------------------------------------

    // Emit a nice big heads-up notification/separator
    echo "<hr><h2>" . _("Copying Pages...") . "</h2>\n";

    $for_real = 1;

    // cd to projects dir to simplify filesystem moves
    global $projects_dir;
    echo "<p>" . _("Changing into projects directory:");
    echo " (<code>cd " . htmlspecialchars($projects_dir) . "</code>)" . "</p>\n";
    if ( ! chdir( $projects_dir ) )
    {
        die( "Unable to 'cd " . htmlspecialchars($projects_dir) . "'");
    }

    $items_array = array();
    foreach ( $column_names_['to'] as $col )
    {
        $items_array[] = (
            in_array( $col, $extra_columns_['to'] )
            ? '""' // (assuming that always works as a default value)
            : $col
        );
    }

    $items_list_template = join( $items_array, ',' );

    // Switch to <pre> for the 'technical' output section
    echo "<pre>\n";

    for ( $j = 0; $j < $n_pages_to_copy; $j++ )
    {
        $c_src_image  = $c_src_image_[$j];
        $c_src_fileid = $c_src_fileid_[$j];
        $c_dst_image  = $c_dst_image_[$j];
        $c_dst_fileid = $c_dst_fileid_[$j];

        echo "\n";
        echo htmlspecialchars("    $c_src_image ...\n");

        $items_list = str_replace(
            array(      'image',       'fileid' ),
            array("'$c_dst_image'","'$c_dst_fileid'"),
            $items_list_template );

        // This ignores $writeBIGtable
        $query = sprintf("
            INSERT INTO %s
            SELECT %s
            FROM %s
            WHERE image = '%s'",
            mysql_real_escape_string($projectid_['to']),
            $items_list,
            mysql_real_escape_string($projectid_['from']),
            mysql_real_escape_string($c_src_image)
        );
        // FIXME These are very long and should perhaps be suppressed, wrapped or made smaller.
        echo htmlspecialchars($query) . "\n";
        if ($for_real)
        {
            mysql_query($query) or die(mysql_error());
            $n = mysql_affected_rows();
            echo sprintf(_("%d rows inserted."), $n) . "\n";
            if ( $n != 1 )
            {
                die( "unexpected number of rows inserted" );
            }
        }

        $c_src_path = "{$projectid_['from']}/$c_src_image";
        $c_dst_path = "{$projectid_['to']}/$c_dst_image";

        echo "\n" . htmlspecialchars(sprintf( _("Copying %s to %s..."), $c_src_path, $c_dst_path)) . " ";

        if ($for_real)
        {
            $success = copy( $c_src_path, $c_dst_path );
            $s = ( $success ? _('Copy succeeded.') : _('<b>Copy failed!</b>') ); 
            echo $s . "\n";
        }
    }
    echo "</pre>\n";

    project_recalculate_page_counts( $projectid_['to'] );
    echo "<p>" . _("Page counts recalculated") . "</p>\n";

    if ($transfer_notifications && $for_real) {
        echo "<p>" . _("Transferring event notifications...") . "</p>\n";

        // for each subscribable event
        //   for each user subscribed to "from" project
        //      subscribe user to "to" project
        global $subscribable_project_events;
        $count = 0;
        foreach ( $subscribable_project_events as $event => $label )
        {
            $query = sprintf("
                      SELECT username FROM user_project_info
                      WHERE projectid = '%s' AND
                            iste_$event = 1",
                    mysql_real_escape_string($projectid_['from'])
            );
            $res1 = mysql_query($query) or die(mysql_error());
            while ( list($username) = mysql_fetch_row($res1) )
            {
                set_user_project_event_subscription( $username, 
                                                     $projectid_['to'], 
                                                     $event, 1 );
                $count++;
            }
        }
        echo "<p>" . sprintf(_("%d notifications transferred."), $count) . "</p>\n";
    }

    if ($add_deletion_reason) {
        echo "<p>" . _("Adding deletion reason to source project.") . "</p>\n";
        $query = sprintf("
              UPDATE projects
              SET deletion_reason = 'merged into %s'
              WHERE projectid = '%s'",
              mysql_real_escape_string($projectid_['to']),
              mysql_real_escape_string($projectid_['from'])
        );
        echo "<code>" . htmlspecialchars($query) . "</code>";
        if ($for_real)
        {
            mysql_query($query) or die(mysql_error());
            $n = mysql_affected_rows();
            echo "<p>" . sprintf(_("%d rows updated."), $n) . "</p>\n";
        }
    }

    if ($merge_wordcheck_files) {
        echo "<p>" . _("Merging wordcheck files.") . "</p>\n";
        if ($for_real)
        {
            merge_wordcheck_files($projectid_['from'], $projectid_['to']);
        }
    }
}

function merge_wordcheck_files($from_id, $to_id)
{
    global $projects_dir;

    // good words
    $from_words = load_project_good_words( $from_id );
    $to_words = load_project_good_words( $to_id );
    $to_words = array_merge($to_words, $from_words);
    save_project_good_words( $to_id, $to_words );

    // crying out for some abstraction here?

    // bad words
    $from_words = load_project_bad_words( $from_id );
    $to_words = load_project_bad_words( $to_id );
    $to_words = array_merge($to_words, $from_words);
    save_project_bad_words( $to_id, $to_words );

    // suggestions
    // the file format is complicated and may change
    // so we take the sledgehammer approach, as suggested by cpeel...
    $from_path = "$projects_dir/$from_id/good_word_suggestions.txt";
    if ( !is_file($from_path) )
    {
        // The file does not exist.
        // Treat that the same as if it existed and was empty.
        $from_suggs = "";
    }
    else 
    {
        $from_suggs = file_get_contents($from_path);
    }
    $to_path = "$projects_dir/$to_id/good_word_suggestions.txt";
    if ( !is_file($to_path) )
    {
        // The file does not exist.
        // Treat that the same as if it existed and was empty.
        $to_suggs =  "";
    }
    else 
    {
        $to_suggs = file_get_contents($to_path);
    }
    file_put_contents($to_path, $to_suggs . $from_suggs);
    // we're assuming the projects are in unavailable or waiting, so there
    // is going to be no need to put locks on the files or anything fancy

}

function str_max( & $arr )
{
    $max_so_far = NULL;
    foreach ( $arr as $s )
    {
        if ( is_null($max_so_far) || strcmp( $s, $max_so_far ) > 0 )
        {
            $max_so_far = $s;
        }
    }
    return $s;
}

// vim: sw=4 ts=4 expandtab