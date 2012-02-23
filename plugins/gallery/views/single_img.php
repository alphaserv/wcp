<?php $img_id = substr(uniqid(), 0, 4); ?>
<article>
	<script type="text/javascript">
		$(document).ready(function(){ 
			$("#<?php echo $img_id ?>").fancybox({
				type : 'image',
				title : '{as:img:title}',
				titlePosition : 'over'
			});
			
			var location = window.location.hash.substr(1).split("|")
			
			$.each(location, function (k, v)
			{
				console.log(k);
				console.log(v);
			});
			/*
			imgPreloader = new Image();
			
			imgPreloader.onerror = function() {
				_error();
			};
			
			imgPreloader.onload = function() {
				busy = true;
				
				imgPreloader.onerror = imgPreloader.onload = null;

				selectedOpts.width = imgPreloader.width;
				selectedOpts.height = imgPreloader.height;

				$("<img />").attr({
					'id' : 'fancybox-img',
					'src' : imgPreloader.src,
					'alt' : selectedOpts.title
				}).appendTo( tmp );
			};
		
			imgPreloader.src = href;*/
		});
	</script>
	<a href="{as:img:full_img}" id="<?php echo $img_id ?>" >
		<img src="{as:img:url}" alt="{as:img:title}" title="{as:img:title}"/>
	</a>
	<section>
		<h1>{as:img:title}</h1>
		<p>{as:img:description}</p>
		<p>
			<div class="rating" data-type="img" data-id="">
				<a href="">like</a>
				<a href="">DIE</a>
			</div>
		</p>
	</section>
<article>

