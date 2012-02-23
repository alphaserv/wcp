<script type="text/javascript" >
	$('.boxgrid').hover(function(){

		$(".cover", this).stop().animate({top:"160px"},{queue:false,duration:160});
	}, function() {
		$(".cover", this).stop().animate({top:"260px"},{queue:false,duration:160});
	});
</script>
<?php foreach($images as $image): 
$image = (object) $image;
?>
<div class="boxgrid captionfull">  
   	<a href="<?php echo site_url('gallery/img/'.(int)$image->id.'/thumb/view'); ?>" >
   		<img src="<?php echo site_url('gallery/img/'.(int)$image->id.'/thumb/raw'); ?>" alt="<?php echo $image->name; ?>" title="<?php echo $image->name; ?>"/>
   	</a>
<!--    <div class="cover boxcaption">-->

<!--        <h3>Jarek Kubicki</h3>  -->
<!--        <p>text</p>  -->
        

<!--    </div>  -->
</div>  
	
<?php endforeach; ?>
