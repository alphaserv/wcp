<style>

	.server.online .status
	{
		color: green;
	}
	
	.server.offline .status
	{
		color: red;
	}
	.server.online
	{
		height:15px;
		border-color:green;
		border-left-color:green;
		border-left-width:2px;
		border-left-style:solid;
		margin-left: 10px;
-webkit-border-radius: 5px;
-moz-border-radius: 5px;
border-radius: 5px;
	}
	/* not working :( */
	.server.offline .status.offline
	{
		content ":(";
		border-color:red;
		border-width:2px;
		border-style:solid;
		border-radius:2px;
	}
</style>

<?php foreach($servers as $server): ?>
	<?php if($server['online'] == 1): ?>
	<div class="server online">
	<?php else: ?>
	<div class="server offline">
	<?php endif;?>
		<?php echo $server['game']; ?>
		<?php echo $server['host'],' : ',$server['port']; ?>
		<?php if($server['online'] == 1): ?>
			<span class="status">Online</span>
		<?php else: ?>
			<span class="status">Offline</span>
		<?php endif; ?>
	</div>
<?php endforeach; ?>
