<? $relPath='../../../pinc/';
include_once('../small_theme.inc');


// A margin
echo "<div style='margin: .5em;'>";

// put ?feedb=blah into $feedb
$feedb = $_GET[feedb];
// spaced quote
if ($feedb == 'spquote') {
  echo "<h2>Spaced double quote</h2>";
  echo "<p>You've left a closing double quote with a space before it in the text.</p>\n";
}
// the user needs to adjust proofing glasses arid try again
elseif ($feedb == 'arid') {
echo "<h2>Scanno</h2>";
echo "<p>You've missed one typical 'scanno' in the text. A 'n' mis-read as 'ri'.</p>\n";
echo "<p>Desperate? Can't find it? Get some more hints <a href='./returnfeed.php?feedb=arid2'>here</a>.</p>\n";
}
// the user request hints to find the arid scanno
elseif ($feedb == 'arid2') {
echo "<h2>Scanno: hints</h2>";
echo "<p>Read the text again, slowly and carefully. Try not to look at the words, look at the letters individually.</p>\n";
echo "<p>You are looking for an occurance of 'ri' that is wrong. There is only only words with 'ri' in the text. Once you've found it you will immediately know it is wrong.</p>\n";
echo "<p>If you can't find any word with 'ri', consider copying the text into an editor and searching for 'ri'. You'll get a result, guaranteed!</p>\n";
echo "<p>No, we won't give away the solution, after all this is a quiz!</p>\n";
}
// THE USER FORGOT TO DECAPITALISE THE TEXT
elseif ($feedb == 'capital') {
echo "<h2>Word in all capital letters</h2>";
echo "You've left a the word starting the chapter in all capital letters. While this matches the original, we still want it to be treated as normal text, so only the first letter should be a capital one.";
}
// the user cannot find their shift key
elseif ($feedb == 'lowercase') {
echo "<h2>Starting word in all lowercase letters</h2>";
echo "You've gone one step too far in de-capitalizing the chapter starting word. It's first letter should still be a capital one.";
}
// the   user   did   not   leave    four   spaces
elseif ($feedb == 'notfour') {
echo "<h2>Number of blank lines before chapter header incorrect</h2>";
echo "There should be 4 blank lines before the chapter header.";
}
// theuserdidnotleaveaspacebeforethemainbodyofthetext
elseif ($feedb == 'nottwo') {
echo "<h2>Number of blank lines between chapter header section and text incorrect</h2>";
echo "There should be 2 blank lines before the start of the text.";
}
// wrong number of blank lines within chapter header
elseif ($feedb == 'numberinheader') {
echo "<h2>Number of blank lines within chapter header section incorrect</h2>";
echo "There should be 1 blank line between different parts of the chapter header.";
}
// the user , even ignorant of the proofing guidelines , really should have known about this
elseif ($feedb == 'spcomma') {
echo "<h2>Spaced comma</h2>";
echo "You've left a comma with a space before it in the text.";
}
// long line
elseif ($feedb == '../longline') {
echo "<h2>Long line</h2>";
echo "You've probably joined two lines by deleting a line break. If you join words around hyphens or dashes, move only one word up to the end of the previous line.";
}
// The user forgot to open the quote"
elseif ($feedb == 'missingquote') {
echo "<h2>Double quote missing</h2>";
echo "It seems you haven't added a double quote at the beginning of the new chapter. Since from the context one can see there should be a double quote starting that sentence and this is only missing for typesetting reasons we insert one there. ";
}
 
// they finally got it
elseif ($feedb == 'ok') {
echo "<h2>Part 3 of quiz successfully solved</h2>";
echo "Congratulations, no errors found!<p>\n<a href='../4/tut.php' target='_top'>Next step of tutorial</a><br>\n<a href='../4/main.php' target='_top'>Next step of quiz</a>";
}
// they tried to edit the text, or something :roll:
elseif ($feedb == 'other') {
echo "<h2>Difference with expected text</h2>";
echo "<p>There is still a difference between your text and the expected one. Finding the reason for this is beyond the current scope of the analysing software.</p><p>This is the expected text:<br>";
echo "
<form action=''><textarea rows='20' cols='60' name='output' wrap='off'>
repentant and remorseful agony.\n\n\n\n
CHAPTER VII.\n
At Oakwood\n\n
\"Dearest mother, this is indeed
like some of
Oakwood's happy hours,\" exclaimed
Emmeline, that same evening, as with
childish glee she had placed herself at her
mother's feet, and raised her laughing eyes</textarea>
<p>
<a href='../4/tut.php' target='_top'>next step of tutorial</a><br>
<a href='../4/main.php' target='_top'>next step of quiz</a>
</p>";}
//  
//  
// otherwise, print the problem
else {
echo "<h2>Problem with quiz file</h2>";
echo "The checking script did not return a known value. Please use the link below to send an automated email about this. The value returned was: $feedb."; }
 
// OK, now make sure they haven't done it right before telling them to correct it 
if ($feedb != "ok") {
// correct that text, dude
echo "<p>";
echo "Try to correct that, press 'restart' to restart or have a look at the <a target='_top' href='./tut.php'>tutorial part</a> again.";
echo "</p>";
// space the scawwy email text
echo "<p>&nbsp;</p>";
// give the user a button to push if the error sense no makes
echo "<p>";
echo "The algorithm for finding errors in this quiz is a quite simple one. If you feel the ";
echo "message doesn't make any sense, please post a feedback message in <a href='";
echo $forums_url;
echo "/viewtopic.php?t=9165' target='_blank'>this forum topic</a>.";
echo "</p>"; 
}
 ?>
  </div>
</body>
</html>
