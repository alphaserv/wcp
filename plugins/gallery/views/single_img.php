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
		<article>
			<h1>{as:img:title}</h1>
			<p>{as:img:description}</p>
		</article>
		<p>
			<details>
				<ul>
					<li>id: #{as:img:id}</li>
					<li>name: {as:img:title}</li>
					<li>url small	: <a href="{as:img:url}" >{as:img:url}</a></li>
					<li>url large: <a href="{as:img:full_img}">{as:img:full_img}</a></li>
					<li>date added: {as:img:date_added}</li>
				</ul>
			</details>
			<div data-rating="{as:img:rating}" class="rating" data-type="img" data-id="">
				Current rating: <span>{as:img:rating}</span>
				<a href="{as:helpers:url:site url='gallery/rate/{as:img:id /}/up' /}">like</a>
				<a href="{as:helpers:url:site url='gallery/rate/{as:img:id /}/down' /}">DIE</a>
			</div>
		</p>
	</section>
<article>

