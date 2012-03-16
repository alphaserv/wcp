<p>
	server status:
	<?php foreach ($serverlist_status as $server): ?>
		online: <?php echo ($server['online']) ? '<span class="green-ok">online</span>' : '<span class="red-error">offline</span>'; ?>
		<?php if($server['online']): ?>
			<?php if(defined('OMG_DEBUG')) print_r($server); ?>
			players: <?php echo $server['status']['playercount']; ?> / <?php echo $server['status']['maxplayers']; ?> ( <?php echo $this->security->xss_clean($server['status']['servername']); ?> )
		<?php endif; ?>
	<?php endforeach; ?>
</p>
