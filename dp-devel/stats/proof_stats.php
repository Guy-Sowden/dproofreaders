<?
$relPath='../pinc/';
include_once($relPath.'dp_main.inc');
include_once($relPath.'f_dpsql.inc');
include_once($relPath.'theme.inc');
include_once($relPath.'user_is.inc');

theme("Proofreading Statistics", "header");

echo "<br><br><h2>Proofreading Statistics</h2><br>\n";

echo "<a href='proj_proofed_graphs.php'>Projects Proofread Graphs</a><br>";

echo "<br>\n";

echo "<h3>Total Projects Proofread</h3>\n";

$state_selector = "
	(state LIKE 'proj_submit%'
		OR state LIKE 'proj_correct%'
		OR state LIKE 'proj_post%')
";


dpsql_dump_query("
	SELECT
		SUM(num_projects) as 'Total Projects Proofread So Far'
	FROM project_state_stats WHERE $state_selector
	GROUP BY date ORDER BY date DESC LIMIT 1
");

echo "<br>\n";
echo "<br>\n";

echo "<h3>Most Prolific Proofreaders</h3>\n";

if (isset($GLOBALS['pguser'])) 
// if user logged on
{

	// site managers get to see everyone
	if ( user_is_a_sitemanager() || user_is_proj_facilitator()) {
		dpsql_dump_ranked_query("
			SELECT
				username as 'Proofreader',
				pagescompleted as 'Pages Proofread'
			FROM users
			WHERE pagescompleted > 0
			ORDER BY 2 DESC, 1 ASC
			LIMIT 100
		");
	}
	else
	{
		// hide names of users who don't want even logged on people to see their names
		dpsql_dump_ranked_query("
			SELECT
				IF(u_privacy = 1,'Anonymous', username) as 'Proofreader',
				pagescompleted as 'Pages Proofread'
			FROM users
			WHERE pagescompleted > 0
			ORDER BY 2 DESC, 1 ASC
			LIMIT 100
		");
	}
} 
else
{

	// hide names of users who don't want unlogged on people to see their names
	dpsql_dump_ranked_query("
		SELECT
			IF(u_privacy > 0,'Anonymous', username) as 'Proofreader',
			pagescompleted as 'Pages Proofread'
		FROM users
		WHERE pagescompleted > 0
		ORDER BY 2 DESC, 1 ASC
		LIMIT 100
	");

}

echo "<br>\n";

echo "<br>\n";

theme("","footer");
?>