<?
$relPath="./../../pinc/";
include_once($relPath.'v_site.inc');
include_once($relPath.'dp_main.inc');
include_once($relPath.'theme.inc');
include_once($relPath.'project_states.inc');
?>
<style type="text/css">
<!--
.purple {
        background-color:#9933CC;
}
.orange {
        background-color: #FF9900;
}
.green {
        background-color: #00FF33;
}
.paleblue {
        background-color: #CCFFFF;
}
.richblue {
        background-color: #33CCFF;
}
.yellow {
        background-color: #FFFF33;
}
.grey {
        background-color: #CCCCCC;
}
.red {
        background-color: #FF0000;
}
-->
</style>
<?

if ($userP['i_newwin']==1) { include($relPath.'js_newwin.inc'); }
theme("Personal Page for $pguser", "header");

        // determine number of pages
        $result = mysql_query("SELECT pagescompleted FROM users WHERE username = '$pguser'");
        $pagesproofed = mysql_result($result,0);

        // DEMO VERSION allows people to simulate different page counts via parameter in URL
        $pagesproofed =  isset($_GET['numofpages'])?$_GET['numofpages']:0;

        echo '<br>';

	// Unread messages

        if ($pagesproofed <= 100) {

	    	$result = mysql_query("SELECT COUNT(*) FROM phpbb_privmsgs WHERE privmsgs_to_userid = $pguser_id && privmsgs_type = 1 || privmsgs_to_userid = $pguser_id && privmsgs_type = 5");
    		$numofPMs = mysql_fetch_row($result);
		if ($numofPMs > 0) {
			echo "<br><br><font color='red' size=3><b>";
			echo _("You have received a private message in your Inbox!");
			echo "</b></font><br><br><font color='red'>";
			echo _("This could be from somebody sending you feedback on some of the pages you have proofed, earlier. We strongly recommend you READ your messages. Near the upper right of this page there is a link that says Inbox, just click on that to open your Inbox.");
			echo "</font><br><br><i><font size=-1>";
			echo _("(After a while this explanatory paragraph will not appear when you have new messages, but the link to your Inbox will always be up there in the corner, and when you have new messages that will be shown in the link)");
			echo "</font></i><br><br>\n";
		}

	}

        // Beginners Info

        if ($pagesproofed <= 40) {
                echo '<font face="'. $theme['font_mainbody'] .'" color = "blue" size=+3><br><b>';
                echo _("Welcome");
                echo "</b></font><br><br>";
                echo _("Please see our");
                echo " <a href=".$beginners_site_forum_url.">";
                echo _("Beginner's Forum");
                echo "</a> ";
                echo _("for answers to common questions.");
                echo "<br><br>\n";

                if ($pagesproofed > 30) {
                        echo "<i>";
                        echo _("After you do a few more pages, the following introductory Simple Proofing Rules will be removed from this page.");
                        echo "</i><br><br>";
                }

                echo "<font face=" . $theme['font_mainbody'] ."><b>";
                echo _("Simple Proofing Rules:");
                echo "</b></font><br><br>";

                echo _("1) Don't rewrap lines. Leave the ends of lines where they are in the image.");
                echo "<br><br>&nbsp;&nbsp;&nbsp;&nbsp;";
                echo _("a) except, please put words that are broken across lines back together.");
                echo "<br><br>";
                echo _("2) Use a blank line between paragraphs and don't indent at the beginning of a paragraph.");
                echo "<br><br>";
                echo _("3) Remove spaces around punctuation and in contractions.");
                echo "<br><br>";
                echo _("4) Don't correct the original spelling.");
                echo "<br><br>";
                echo _("5) When in doubt, make it look like the original and use * to flag the spot.");
                echo "<br><br>\n\n";


                echo "<i>";
                echo _("The");
                echo " <a href='".$code_url."'/faq/summary.pdf>";
                echo _("Handy Proofing Guide");
                echo "</a> ";
                echo _("(printable) and");
                echo " <a href='".$code_url."'/faq/document.php>";
                echo _("Proofing Guidelines");
                echo "</a> ";
                echo _("(for reference) provide much more detail.");
                echo "</i><br><br>\n";
                echo _("BEGINNERS ONLY projects are reserved for new proofers. After you have done 5-15 pages from these projects, please leave them for proofers who are newer than you. EASY projects make a good next step.");
                echo "<br><br>\n";

                echo "<center><hr width='75%'></center><br>";

        }


        //Display News Updates

        if ($pagesproofed >= 20) {
                if ($pagesproofed < 40) {
                        echo "<font face=" . $theme['font_mainbody'] . "><i>";
                        echo _("Now you have proofed 20 pages you can see the Site News. This explanatory line will eventually vanish.");
                        echo "</i></font><br><br>";
                }

                $result = mysql_query("SELECT date_posted, message FROM news ORDER BY uid DESC LIMIT 1");
                $news = mysql_fetch_assoc($result);
                echo "<font size=2 face=" . $theme['font_mainbody'] . "><center><b>";
                echo _("News Update for")." ".date("l, F jS, Y", $news['date_posted'])." (<a href='$code_url/pastnews.php'>";
                echo _("archives") . "</a>)</b></font><br><br><font size=2 face=";
                echo $theme['font_mainbody'] . ">".$news['message']."<hr width='75%'></center></font><br>\n";
        }


        // Plug for providing content

        if ($pagesproofed >= 60) {
                echo "<font face=" . $theme['font_mainbody'] ."><b>";
                echo _("Providing Content");
                echo "</b></font><br>";
                echo _("Want to help out the site by providing material for us to proof?");
                echo "<a href=" . $code_url . "/faq/scan/submitting.php> ";
                echo _("Find out how!");
                echo "</a><br><br>\n";
        }

        // If Post Processor give link to post processing page.
// DEMO VERSION uses pagecount only
//      if ($userP['postprocessor'] == "yes" || $pagesproofed >= 400) {
        if ( $pagesproofed >= 400) {

                $result = mysql_query("SELECT count(projectid) FROM projects WHERE state='".PROJ_POST_FIRST_AVAILABLE."' || state='".PROJ_POST_SECOND_AVAILABLE."'");
                $numprojects = mysql_result($result,0);
                $result = mysql_query("SELECT count(projectid) FROM projects WHERE checkedoutby='$pguser' && state='".PROJ_POST_FIRST_CHECKED_OUT."'");
                $yourPPprojects = mysql_result($result,0);
                $result = mysql_query("SELECT count(projectid) FROM projects WHERE checkedoutby='$pguser' && state='".PROJ_POST_SECOND_CHECKED_OUT."'");
                $yourPPVprojects = mysql_result($result,0);

                echo "<font face=" . $theme['font_mainbody'] ."><b>";
                echo _("Post Processing");
                echo "</b></font><br>";
                echo _("After going through two rounds of proofreading, the books need to be massaged into a final e-text. You can help in the ");
                echo "<a href =" . $code_url . "/tools/post_proofers/post_proofers.php>";
                echo _("post processing");
                echo "</a> ";
                echo _(" phase of Distributed Proofreaders!  Currently there are");
                echo " <b>".$numprojects."</b> ";
                echo _("projects waiting.");

                if (($yourPPprojects + $yourPPVprojects)  > 0) {
                        echo "  ". _("You currently have") . " ";
                        if ($yourPPprojects > 0) {
                                echo " <b>".$yourPPprojects."</b> ". _("projects checked out for PPing");
                                if ($yourPPVprojects > 0) {
                                        echo " ". _("and");
                                }
                        }
                        if ($yourPPVprojects > 0) {
                                echo " <b>".$yourPPVprojects."</b> ". _("projects checked out for PPVing");
                        }
                        echo ".";
                }
                echo "<br><br>\n";
                echo "<center><hr width='75%'></center><br>";

        }


        if ($pagesproofed >= 10) {

                if ($pagesproofed < 40) {
                        echo "<font face=" . $theme['font_mainbody'] . "><i>";
                        echo _("Now you have proofed 10 pages you can see the Random Rule. This explanatory line will eventually vanish.");
                        echo "</i></font><br><br>";
                }


                echo "<font face=".$theme['font_mainbody']."><b>";
                echo _("Random Rule");
                echo "</b></font><br>";


                $result = mysql_query("SELECT subject,rule,doc FROM rules ORDER BY RAND(NOW()) LIMIT 1");
                $rule = mysql_fetch_assoc($result);
                echo "<i>".$rule['subject']."</i><br>";
                echo "".$rule['rule']."<br>";
                echo "See the <a href='$code_url/faq/document.php#".$rule['doc']."'>".$rule['subject']."</a> section of the <a href='$code_url/faq/document.php'>Proofing Guidelines</a><br><br>";

                echo "<center><hr width='75%'></center><br>";

        }

        if ($pagesproofed >= 50) {
                if ($pagesproofed < 75) {
                        echo "<font face=" . $theme['font_mainbody'] . "><br><br><i>";
                        echo _("Now you have proofed 50 pages you can see the books in Second Round. This explanatory line will eventually vanish.");
                        echo "</i></font><br><br>";
                }
        }

if ($pagesproofed >= 10) {

?>
<p>
<font face="<? echo $theme['font_mainbody']; ?>">
Legend for Special Books:
<br><br><b>
<span class="orange"> Halloween </span>&nbsp;
<span class="paleblue"> Authors with recent birthdays </span>&nbsp;
<span class="richblue"> Authors with birthdays today </span>&nbsp;
<br>
<span class="yellow"> Children's Book Week </span>&nbsp;
<span class="grey"> Native American Heritage Month </span>&nbsp;
<br>
<span class="purple"> Other Special </span>
</b></font>
</p>
<br>



<?
}


if ($pagesproofed <= 20) {

                echo "<font face=" . $theme['font_mainbody'] ."><b>";
                echo _("Click on the name of a book in the list below to start proofing.");
                echo "</b></font><br><br>\n";
}

$tList=0;
include_once('proof_list.inc');
?>

<?
theme("", "footer");
?>
