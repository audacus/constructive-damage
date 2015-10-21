</div>
<div class="footer">
	<span class="copyright"><?php
	$author = $this->config->get('app.info.author');
	$date = $this->config->get('app.info.date');
	if (!empty($author)) {
		echo 'Copyright &copy; '.$author;
		if (!empty($date)) {
			$startYear = DateTime::createFromFormat('Y-m-d', $date)->format('Y');
			$currentYear = (new DateTime())->format('Y');
			echo ' '.($currentYear <= $startYear ? $currentYear : $startYear.'-'.$currentYear);
		}
	}
?></span>
</div>
</div>
</body>
</html>