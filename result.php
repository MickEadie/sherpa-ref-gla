<?php
$issn = $_GET['issn'];
$start_time = isset($_GET['date']);
if (!$start_time) { $start_time = 'Acceptance'; }
$pn = $_GET['pn'];
if (!$pn) { $pn = 'both'; }
$json = file_get_contents("https://ref.sherpa.ac.uk/id/journal/$issn");
$journal = json_decode($json);
if (!$journal)
{
	echo('<h1>OH NO, No Journal!!!</h1>');
	exit;
}
?>


<!doctype html>
<head>
  <meta charset="utf-8">
  <title>REF using Acceptance Date</title>
  <meta name="description" content="Alternative Sherpa REF Tool">
  <meta name="author" content="University of Glasgow">

<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" type="text/css" href="styles/style1.css" />
<link href='https://fonts.googleapis.com/css?family=Hind:600' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>

<script src="js/utils.js"></script>
</head>
<body>
<?php include('includes/header.inc'); ?>

<section id="content-wrapper">
	<section id="content-main">
	<?php
			if ($pn == 'AB')
			{
				echo ('<h2>Compliance for REF Panels A &amp; B</h2>');
				echo ('<ul id="journal-info">');
				echo ('<li><strong>Title:</strong> ' . $journal->title .  '</li>');
				echo ('<li><strong>ISSN:</strong> ' . $issn .'</li>');
				echo ('<li><strong>Publisher:</strong> ' . $journal->publisher->name . '</li>');
				//echo ('<!--li>route: $journal->publisher_policy->permitted_actions[0]->open_access_route</li-->');
				echo ('</ul>');

				$counts;
				foreach ($journal->advised_actions->AB as $action)
				{
					$counts[$action->open_access_route]++;
				}
					if ($counts['Archive'] > 0 || $counts['Publish'] > 0 || $counts['Hybrid'] > 0)
					{

							if ($action->embargo_period > 0 && $action->embargo_period > 12)
							{
								echo('"<div class="payment-required"><p>This Journal requires payment as it has an embargo period of <strong>" . $action->embargo_period .  "</strong>months</p></div>"');
							}
							else
							{
								echo('<div class="compliant"><p>This Journal is REF Compliant</p></div>');
								echo('<p class="comply-text">To ensure compliance with the OA requirements for REF, you must satisfy the following requirements:</p>');
								echo('<ol id="compliance-info">');
								echo('<li>Deposit the <strong>' . implode(' or ',$action->article_versions) . '</strong> in: <strong>' . implode(' or ',$action->repository_types) . '</strong>  within 3 months of <span style="font-size: bigger"><strong>Date of ' . $start_time . '</strong></span></li>');
								if ($action->embargo_period > 0)
								{
									echo ('<li>This journal has a ' . $action->embargo_period . ' month embargo period.  You must make the full text of your article open access in an Insitutional Repository immediately after this embargo period ends.</li>');
								}
								echo ('</ol>');
							}
					}
 					else
					{
						echo('<div class="non-compliant"><p>This Journal is not REF Compliant</p></div>');
						echo('<p class="comply-text">Put some information in here about what author can do now</p>');
					}

			}
			if ($pn == 'CD')
			{
				echo ('<h2>Compliance for REF Panels C &amp; D</h2>');
				echo ('<ul id="journal-info">');
				echo ('<li><strong>Title:</strong> ' . $journal->title .  '</li>');
				echo ('<li><strong>ISSN:</strong> ' . $issn .'</li>');
				echo ('<li><strong>Publisher:</strong> ' . $journal->publisher->name . '</li>');
				//echo ('<!--li>route: $journal->publisher_policy->permitted_actions[0]->open_access_route</li-->');
				echo ('</ul>');

				$counts;
				foreach ($journal->advised_actions->CD as $action)
				{
					$counts[$action->open_access_route]++;
				}
				if ($counts['Archive'] > 0 || $counts['Publish'] > 0 || $counts['Hybrid'] > 0)
				{
					if ($action->embargo_period > 0 && $action->embargo_period > 24)
					{
						echo('<div class="payment-required"><p>This Journal requires payment as it has an embargo period longer than 12 months</p></div>');
					}
					else
					{
						echo('<div class="compliant"><p>This Journal is REF Compliant</p></div>');
						echo('<p class="comply-text">To ensure compliance with the OA requirements for REF, you must satisfy the following requirements:</p>');
						echo('<ol id="compliance-info">');
						echo('<li>Deposit the <strong>' . implode(' or ',$action->article_versions) . '</strong> in: <strong>' . implode(' or ',$action->repository_types) . '</strong>  within 3 months of <span style="font-size: bigger"><strong>Date of ' . $start_time . '</strong></span></li>');
						if ($action->embargo_period > 0)
						{
							echo ('<li>This journal has a ' . $action->embargo_period . ' month embargo period.  You must make the full text of your article open access in an Insitutional Repository immediately after this embargo period ends.</li>');
						}
						echo ('</ol>');
					}
				}
				else
				{
					echo('<div class="non-compliant"><p>This Journal is not REF Compliant</p></div>');
					echo('<p class="comply-text">Put some information in here about what author can do now</p>');
				}
			}

			if (isset ($journal->publisher_policy->information_urls))
			{
				echo ('<h3>More Information</h3>');
				echo ('<ul id = "more-info">');
				foreach ($journal->publisher_policy->information_urls as $url)
				{
					echo ('<li><a target="_blank" href="' . $url . '">' . $url . '</a></li>');
				}
				echo ('</ul>');
			}

			/*
			 if ($pn == 'both')
			 {
				foreach (array('AB', 'CD') as $panel)
				{
					echo ('<div style="float:left;width:45%;">');
					echo ("<h3 style='text-align: center; text-size: bigger;'>REF Panels $panel</h3>");
					echo("<ul>\n");
					$count = 0;
					$counts;
					foreach ($journal->advised_actions->$panel as $action)
					{
						$count++;
						$counts[$action->open_access_route]++;
						echo("<li>$count:<ul>");
						echo('<li>' . 'Route: ' . $action->open_access_route . '</li>');
						echo('<li>' . 'Required Embargo: ' . $action->embargo_period . '</li>');
						echo('<li>' . 'Allowed Version(s): ' . implode(' OR ',$action->article_versions) . '</li>');
						echo('<li>' . 'Place you can put it: ' . implode(' OR ',$action->repository_types) . '</li>');
						echo ('</ul></li>');
					}
					echo("</ul>\n");
					if ($counts['Archive'] > 0 || $counts['Publish'] > 0 || $counts['Hybrid'] > 0)
					{
						echo('<p>Looks like this is eligible</p>');
					}
					else
					{
						echo("<p>It doesn't look like this is eligible</p>");
					}
					echo('</div>');
			   }
			}
			*/
?>
	</section> <!-- /main -->
</section> <!-- /wrapper -->

<?php include('includes/footer.inc'); ?>

</body>
</html>