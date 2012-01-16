<article>
	<section>
	<?/*		{if {as:page:is_homepage} == 1}
			<h2>{as:page:title}</h2>
		{else}
			<h1>{as:page:title}</h1>
		{/endif}*/?>
		<span>{as:page:date}</span>
		<details>
			id: #{as:page:id}
	    	path: {as:page:uri}
	    	title: {as:page:title}
	    	version: {as:page:revision}
			data: {as:page:date}
		</details>
	</section>
	<p>
		{as:page:content}
	</p>
	

</article>
