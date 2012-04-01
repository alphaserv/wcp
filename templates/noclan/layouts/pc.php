<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" itemscope itemtype="http://schema.org/Blog">
<html>
	<head>
		<base href="<?php site_url(''); ?>"/>
		<title>{title}</title>
		<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
		<meta name="Language" content="EN"/>
		<meta name="Keywords" content="NoClan, No Clan, sauerbraten, cube2, clan, fun"/>
		<meta name="Description" content="No Clan: Sauerbraten Clan, since 2011"/>
		<meta name="Distribution" content="Global"/>
		<meta name="Robots" content="All"/>
		<link rel="shortcut icon" href="{as:helpers:url:base url='templates/noclan/img/favicon.ico'}">
		
		<!--Jquery ui (Css)-->
		<link href="{as:helpers:url:base url='static/css/ui-lightness/jquery-ui-1.8.17.custom.css'}" rel="stylesheet" type="text/css" />
		<link href="{as:helpers:url:base url='static/fancybox/jquery.fancybox-1.3.4.css'}" rel="stylesheet" type="text/css"/><!-- fancy css -->
		<!--/jqueryui -->
		
		<!-- STYLE -->
		<link href='http://fonts.googleapis.com/css?family=Orbitron:400,500,700,900|Aldrich|Gochi+Hand' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" type="text/css" href="{as:helpers:url:base url='templates/noclan/css/default.css'}"/>
		<link rel="stylesheet" type="text/css" href="{as:helpers:url:base url='templates/noclan/css/noclan.css'}"/>
		<!-- /style -->
		
		<!-- Jquery -->
		<script type="text/javascript" src="{as:helpers:url:base url='static/js/jquery-1.7.1.min.js'}"></script>
		<script type="text/javascript" src="{as:helpers:url:base url='static/js/jquery-ui-1.8.17.custom.min.js'}"></script>
		<!-- /jquery -->

		<!-- Fancy -->
		<script src="{as:helpers:url:base url='static/fancybox/jquery.fancybox-1.3.4.js'}" type="text/javascript"></script>
		
		<script type="text/javascript">
			$(document).ready(function() {
				$(".fancy").fancybox({
					'overlayColor'	: '#000',
					'overlayOpacity'	: 0.7,
					'padding'   	: 1,
					'transitionIn'	:'elastic',
					'transitionOut'	:'fade',
					'titleShow'		: false
				});
		
				$(".fancy_mini_main").fancybox({
				'overlayColor'	: '#000',
					'overlayOpacity': 0.7,
					'padding'   	: 1,
					'hideOnOverlayClick':false,
					'transitionIn'	:'elastic',
					'transitionOut'	:'fade',
					'titleShow'	: false,
					'type'		: 'iframe',
					'width':673,
					'scrolling': 'auto'
					
				});
			});
		</script>
		<!-- /Fancy -->

		<!-- private messages -->
		<script type="text/javascript">
			(function($){
				$(document).ready(function() {
					$.get("<?php echo site_url('pm/update'); ?>", function(result)
					{
					
					});
				})
			})($);
		</script>
		<!-- /private messages -->
<?php
/*
<!-- ANALYTICS -->
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-27511799-1']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
<!-- /ANALYTICS -->*/?>

<!-- RSS -->
<link rel="alternate" title="No Clan: News" href="{as:helpers:url:site url='news.rss'}" type="application/rss+xml">
<!-- /RSS -->

<!-- G+ -->
<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
<!-- /being social -->

		
<?php /*
	<!-- FaceBook opengraph TAGS-->
	<meta property="og:title" content="<?p hp echo $social_title;? >" />
	<meta property="og:url" content="<?p hp echo $social_url;? >" />
	<meta property="og:image" content="<?p hp echo $social_image;? >" />
	<meta property="og:type" content="website" />
	<meta property="og:site_name" content="<?p hp echo $social_description;? >" />
	<meta property="fb:admins" content="100003397471644" />*/ ?>
	{head}
</head>

<body>
<?php /*
<!-- G+ TAGS-->
<span itemprop="name" style="display: none"><?p hp echo $social_title;? ></span>
<span itemprop="description" style="display: none"><?p hp echo $social_description;? ></span>
<img itemprop="image" src="<?p hp echo $social_image;? >" style="display: none"/>*/ ?>
    <div id="wrapper">
    	<div id="container">
    		<div id="header">
    			{as:template:partial:header}
    		</div>
    		
    		<div id="menu">
				{as:template:partial:menu}
    		</div>

			<div id="content">
				<div id="sidepanel">
					<!-- ERRORS? -->
						<?php if (isset($error) && !empty($error)): ?>
							<div class="error">
								<h3>Login problems:</h3>
								
								<?php foreach ($error as $e):?>
									<p><?php echo $e;?></p>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
				<!-- /errors -->
				
				<div>
					{as:template:partial:sidebar}
				</div>
				
				{as:widget:noclan_sidebar}
			</div><!-- /"sidepanel" -->

			<div id="main">
				{main}
			
			</div><!-- /main -->
	    </div><!-- /content-->
	    
	    <div id="footer">
		    {as:template:partial:footer}
	    </div> <!-- /footer -->

	</div><!-- /container -->
    </div><!-- /wrapper -->

</body>
</html>

