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
		<?php endif; ?>
		<!--section register-->
		<secton class="register">
		<?php
		
		#TODO:csr protection
	echo form_open('user/register');

		echo form_fieldset();
		echo form_label('Your website username', 'username');
		echo form_input('username', set_value('username'));
			echo form_error('username');
		echo form_fieldset_close();

		echo form_fieldset();
		echo form_label('Your email address', 'email');
		echo form_input('email', set_value('email'));
			echo form_error('email');		
		echo form_fieldset_close();
		
		echo form_fieldset();
		echo form_label('your <strong>full</strong> ingame name', 'alphaserv_username');
		echo form_input('alphaserv_username', set_value('alphaserv_username'));
			echo form_error('alphaserv_username');
		echo form_fieldset_close();
		
		echo form_fieldset();
		echo form_label('Your ingame password', 'alphaserv_password');
		echo form_password('alphaserv_password');
			echo form_error('alphaserv_password');
		echo form_fieldset_close();
		
		echo form_fieldset();
		echo form_label('retype Your ingame password', 'alphaserv_password2');
		echo form_password('alphaserv_password2');
			echo form_error('alphaserv_password2');
		echo form_fieldset_close();
		
		echo form_fieldset();
		echo form_label('Your website password', 'password');
		echo form_password('password');
			echo form_error('password');
		echo form_fieldset_close();
		
		echo form_fieldset();
		echo form_label('retype Your website password', 'password');
		echo form_password('retype_password');
			echo form_error('retype_password');
		echo form_fieldset_close();
		
		echo form_submit('submit', 'Register');

	echo form_close();
	
	?></section>
	<!--end section register-->
