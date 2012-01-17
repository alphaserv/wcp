<html>
	<head>
	
		<link href="{as:helpers:url:base url='static/css/ui-lightness/jquery-ui-1.8.17.custom.css'}" rel="stylesheet" type="text/css" />
		
		<script type="text/javascript" src="{as:helpers:url:base url='static/js/jquery-1.7.1.min.js'}"></script>
		<script type="text/javascript" src="{as:helpers:url:base url='static/js/jquery-ui-1.8.17.custom.min.js'}"></script>
	</head>
	<body>
		<header>
			{as:site:header:full}
		</header>
		<nav>
		{as:template:partial:menu}
		</nav>
		<?php
		if(isset($info) && !empty($info)): ?>
		
		<section class="ui-widget">
			<div class="ui-state-highlight ui-corner-all">
				<p>
					<span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
					{info}
				</p>
			</div>
		</section>
		<?php endif;?>

		<div>
			{main}
		</div>
		<footer>
		{as:servers:statusbar /}
		{as:template:partial:footer}
		</footer>
	</body>
</html>

