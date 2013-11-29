<?php
$relPath='../../pinc/';
include_once($relPath.'base.inc');
include_once($relPath.'theme.inc');

output_header(_('Basic Proofreading Tutorial -- Step 2'));

echo "<h2>" . _("Basic Proofreading Tutorial, Step 2") . "</h2>\n";
echo "<h3>" . _("Paragraph Spacing/Indenting") . "</h3>\n";
echo "<p>" . _("Put a blank line before the start of a paragraph, even if it starts at the top of a page. You should not indent the start of the paragraph, but if it is already indented don't bother removing those spaces&mdash;that can be done automatically during post-processing.") . "</p>\n";

echo "<h3>" . _("End-of-line Dashes") . "</h3>\n";
echo "<p>" . _("As with end-of-line hyphenation, if an em-dash appears at the start or end of a line of your OCR'd text, join it with the other line so that there are no spaces or line breaks around it. See the examples below.") . "</p>\n";

echo "<h3>" . _("Dashes") . "</h3>\n";
echo "<p>" . _("<i>Em-dashes &amp; long dashes</i>. These serve as <b>separators</b> between words&mdash;sometimes for emphasis like this&mdash;or when a speaker gets a word caught in his throat&mdash;&mdash;!");
echo "<br>" . _("Proofread these as two hyphens if the dash is as long as 2-3 letters (an <i>em-dash</i>) and four hyphens if the dash is as long as 4-5 letters (a <i>long dash</i>). Don't leave a space before or after, even if it looks like there was a space in the original book image.") . "</p>\n";

echo "<table width='100%' align='center' border='1'  cellpadding='4' cellspacing='0' summary='" . _("Hyphens and Dashes") . "'>\n";
echo "  <tbody>\n    <tr>\n      <th valign='top' bgcolor='cornsilk'>" . _("Original Image:") . "</th>\n";
echo "      <th valign='top' bgcolor='cornsilk'>" . _("Correctly Proofread Text:") . "</th>\n    </tr>\n";
echo "    <tr>\n      <td valign='top'>sensations&mdash;sweet, bitter, salt, and sour<br>\n        &mdash;if even all of these are simple tastes. What</td>\n";
echo "      <td valign='top'><tt>sensations--sweet, bitter, salt, and sour--if<br>\n        even all of these are simple tastes. What</tt></td>\n    </tr>\n";
echo "    <tr>\n      <td valign='top'>senses&mdash;touch, smell, hearing, and sight&mdash;<br>\n        with which we are here concerned,</td>\n";
echo "      <td valign='top'><tt>senses--touch, smell, hearing, and sight--with<br>\n        which we are here concerned,</tt></td>\n    </tr>\n";
echo "    <tr>\n      <td valign='top'><img src='../../faq/dashes.png' width='300' height='28' alt=''></td>\n";
echo "      <td valign='top'><tt>how a--a--cannon-ball goes----\"</tt></td>\n";
echo "    </tr>\n  </tbody>\n</table>\n";

echo "<h3>" . _("End-of-page Hyphenation and Dashes") . "</h3>\n";
echo "<p>" . _("Proofread end-of-page hyphens or em-dashes by leaving the hyphen or em-dash at the end of the last line, and mark it with a <tt>*</tt> after the hyphen or dash.") . "</p>\n";

echo "<p><a href='../generic/main.php?type=p_basic_2&quiz_id=BPQ1'>" . _("Continue to quiz") . "</a></p>\n";

// vim: sw=4 ts=4 expandtab
