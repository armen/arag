<div id="kohana-profiler">

<?php if (isset($benchmarks)): ?>
	<table id="kp-benchmarks">
		<tr>
			<th><?php echo Kohana::lang('profiler.benchmarks') ?></th>
			<th class="kp-column">Time</th>
			<th class="kp-column">Memory</th>
		</tr>
<?php

// Moves the first benchmark (total execution time) to the end of the array
$benchmarks = array_slice($benchmarks, 1) + array_slice($benchmarks, 0, 1);

foreach ($benchmarks as $name => $benchmark):

	$class = ($name == 'total_execution') ? ' class="kp-totalrow"' : text::alternate('', ' class="kp-altrow"');
	$name = ucwords(str_replace(array('_', '-'), ' ', $name));

?>
		<tr<?php echo $class ?>>
			<td><?php echo $name ?></td>
			<td class="kp-column kp-data"><?php echo number_format($benchmark['time'], 4) ?></td>
			<td class="kp-column kp-data"><?php echo number_format($benchmark['memory'] / 1024 / 1024, 2) ?> MB</td>
		</tr>
<?php

endforeach;

?>
	</table>
<?php endif; ?>

<?php if (isset($queries)): ?>
	<table id="kp-queries">
		<tr>
			<th><?php echo Kohana::lang('profiler.queries') ?></th>
			<th class="kp-column">Time</th>
			<th class="kp-column">Rows</th>
		</tr>
<?php

if ($queries === FALSE):

?>
		<tr><td colspan="3"><?php echo Kohana::lang('profiler.no_database') ?></td></tr>
<?php

else:

	if (count($queries) == 0):

?>
		<tr><td colspan="3"><?php echo Kohana::lang('profiler.no_queries') ?></td></tr>
<?php

	else:
		text::alternate();
		$total_time = 0;
		foreach($queries as $query):
			$total_time += $query['time'];
?>
		<tr<?php echo text::alternate('', ' class="kp-altrow"') ?>>
			<td><?php echo html::specialchars($query['query']) ?></td>
			<td class="kp-column kp-data"><?php echo number_format($query['time'], 4) ?></td>
			<td class="kp-column kp-data"><?php echo $query['rows'] ?></td>
		</tr>
<?php

		endforeach;
?>
		<tr class="kp-totalrow">
			<td>Total: <?php echo count($queries) ?></td>
			<td class="kp-column kp-data"><?php echo number_format($total_time, 4) ?></td>
			<td class="kp-column kp-data">&nbsp;</td>
		</tr>
<?php

	endif;
endif;

?>
	</table>
<?php endif; ?>

<?php if (isset($post)): ?>
	<table id="kp-postdata">
		<tr>
			<th colspan="2"><?php echo Kohana::lang('profiler.post_data') ?></th>
		</tr>
<?php

if (count($_POST) == 0):

?>
		<tr><td colspan="2"><?php echo Kohana::lang('profiler.no_post') ?></td></tr>
<?php

else:
	text::alternate();
	foreach($_POST as $name => $value):

?>
		<tr<?php echo text::alternate('', ' class="kp-altrow"') ?>>
			<td class="kp-name"><?php echo $name ?></td>
			<td>
				<?php echo is_array($value) ? '<pre>'.html::specialchars(print_r($value, TRUE)).'</pre>' : html::specialchars($value) ?>
			</td>
		</tr>
<?php

	endforeach;
endif;

?>
	</table>
<?php endif; ?>

<?php if (isset($session)): ?>
	<table id="kp-sessiondata">
		<tr>
			<th colspan="2"><?php echo Kohana::lang('profiler.session_data') ?></th>
		</tr>
<?php

if ( ! isset($_SESSION)):

?>
		<tr><td colspan="2"><?php echo Kohana::lang('profiler.no_session') ?></td></tr>
<?php

else:
	text::alternate();
	foreach($_SESSION as $name => $value):

?>
		<tr<?php echo text::alternate('', ' class="kp-altrow"') ?>>
			<td class="kp-name"><?php echo $name ?></td>
			<td>
				<?php echo is_array($value) ? '<pre>'.html::specialchars(print_r($value, TRUE)).'</pre>' : html::specialchars($value) ?>
			</td>
		</tr>
<?php

	endforeach;
endif;

?>
	</table>
<?php endif; ?>

</div>
