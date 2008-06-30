<div id="kohana-profiler">
<?php
foreach ($profiles as $profile)
{
	echo $profile->render();
}
?>
Profiler executed in <?php echo number_format($execution_time, 3) ?>s
</div>
