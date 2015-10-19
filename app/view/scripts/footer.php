</div>
<div class="footer">
	<span class="copyright"><?php
	$startYear = DateTime::createFromFormat('Y-m-d', '2015-08-13')->format('Y');
	$currentYear = (new DateTime())->format('Y');
	echo 'Copyright &copy; Hug Studios '.($currentYear <= $startYear ? $currentYear : $startYear.'-'.$currentYear);
?></span>
</div>
</div>
</body>
</html>