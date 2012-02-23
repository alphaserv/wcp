<script type="text/javascript" >
	$('.boxgrid').hover(function(){

		$(".cover", this).stop().animate({top:"160px"},{queue:false,duration:160});
	}, function() {
		$(".cover", this).stop().animate({top:"260px"},{queue:false,duration:160});
	});
</script>
<?php foreach($images as $image): ?>
<div class="boxgrid captionfull">  
    <img src="<?php echo site_url('gallery/img/'.(int)$image->id.'/thumb/raw'); ?>" alt="<?php echo $image->name; ?>" title="<?php echo $image->name; ?>"/>
    <div class="cover boxcaption">
<!--    	<a href="<?php echo site_url('gallery/img/'.(int)$image->id.'/thumb/view'); ?>" >-->
        <h3>Jarek Kubicki</h3>  
        <p>text</p>  
        

    </div>  
</div>  
	
<?php endforeach; ?>
