<ul>
<?php foreach($menu as $item => $modifiers):?>
	<ul class="<?php echo in_array('selected', $modifiers) ? 'selected' : ''; ?>">
		<a href="<?php echo site_url('user/admin/'.urlencode($item)); ?>" ><?php echo $item; ?></a>
	</ul>
<?php endforeach; ?>
</ul>
