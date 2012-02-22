<article>
	<section>
		<h1>{as:page:title}</h1>
		<div>publication date: <span>{as:page:date}</span></div>
		<details>
			<span>id: #{as:page:id}</span>
	    	<span>path: {as:page:uri}</span>
	    	<span>title: {as:page:title}</span>
		</details>
	</section>
	<p>
		<?php switch($page['makeup'])
		{
			case 'none' :
				echo htmlentities($page['content']);
				break;
			
			case 'html' :
				echo $page['content'];
				break;
			
			case 'php':
				echo eval('?>'.$page['content'].'<?php ');
				break;
		}
		?>
	</p>
</article>
