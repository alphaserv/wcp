	<?php if(isset($notice) && !empty($notice)): ?>
		
		<section class="ui-widget">
			<div class="ui-state-error ui-corner-all">
				<p>
					<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
					<strong>Error:</strong>
					{notice}
				</p>
			</div>
		</section>
		<?php
		endif;
	echo form_open('user/login');
	echo form_input('username', isset($username) ? $username : '');
	echo form_password('password');
	echo form_submit('submit', 'login');
	echo form_close();
	
	?>
