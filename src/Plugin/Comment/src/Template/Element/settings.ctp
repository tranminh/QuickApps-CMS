<fieldset>
	<legend><?php echo __d('comment', 'Commenting Options'); ?></legend>

	<?php echo $this->Form->input('auto_approve', ['type' => 'checkbox', 'label' => __d('comment', 'Auto approve comments')]); ?>
	<em class="help-block"><?php echo __d('comment', 'Comments will automatically approved an published.'); ?></em>

	<?php echo $this->Form->input('allow_anonymous', ['type' => 'checkbox', 'label' => __d('comment', 'Anonymous commenting'), 'id' => 'allow-anonymous-comments', 'onclick' => 'toggleAnonymousCommentOptions();']); ?>
	<em class="help-block"><?php echo __d('comment', 'Anonymous users can comment.'); ?></em>

	<div class="anonymous-comments-options">
		<?php echo $this->Form->input('anonymous_name', ['type' => 'checkbox', 'label' => __d('comment', "Anonymous's name")]); ?>
		<em class="help-block">
			<?php
				echo __d(
					'node',
					'Anonymous users %s leave their name.', 
					$this->Form->input('anonymous_name_required', [
						'type' => 'select',
						'label' => false,
						'bootstrap' => false,
						'options' => [
							1 => __d('comment', 'Must'),
							0 => __d('comment', 'May'),
						],
					])
				);
			?>
		</em>

		<?php echo $this->Form->input('anonymous_email', ['type' => 'checkbox', 'label' => __d('comment', "Anonymous's email")]); ?>
		<em class="help-block">
			<?php
				echo __d(
					'node',
					'Anonymous users %s leave an email address.', 
					$this->Form->input('anonymous_email_required', [
						'type' => 'select',
						'label' => false,
						'bootstrap' => false,
						'options' => [
							1 => __d('comment', 'Must'),
							0 => __d('comment', 'May'),
						]
					])
				);
			?>
		</em>

		<?php echo $this->Form->input('anonymous_web', ['type' => 'checkbox', 'label' => __d('comment', "Anonymous's website")]); ?>
		<em class="help-block">
			<?php
				echo __d(
					'node',
					'Anonymous users %s leave a website URL.', 
					$this->Form->input('anonymous_web_required', [
						'type' => 'select',
						'label' => false,
						'bootstrap' => false,
						'options' => [
							1 => __d('comment', 'Must'),
							0 => __d('comment', 'May'),
						]
					])
				);
			?>
		</em>
	</div>
</fieldset>

<hr />

<fieldset>
	<legend><?php echo __d('comment', 'Human Verification'); ?></legend>

	<?php echo $this->Form->input('use_ayah', ['id' => 'use-ayah', 'type' => 'checkbox', 'label' => __d('comment', 'Enable Human Verification'), 'onclick' => 'toggleAyahOptions();']); ?>
	<em class="help-block"><?php echo __d('comment', 'Service provided by "Are You A Human", <a href="%s" target="_blank">register</a> and get your keys', 'http://areyouahuman.com/'); ?></em>
	<div class="ayah-options">
		<?php echo $this->Form->input('ayah_publisher_key', ['type' => 'text', 'label' => __d('comment', 'Publisher Key *')]); ?>
		<em class="help-block"><?php echo __d('comment', 'e.g. 310203ef720d21451c2516f2633c645acadc225a'); ?></em>

		<?php echo $this->Form->input('ayah_scoring_key', ['type' => 'text', 'label' => __d('comment', 'Scoring Key *')]); ?>
		<em class="help-block"><?php echo __d('comment', 'e.g. 6233426d2e41a5c37d11c65202fa23c1fca50520'); ?></em>
	</div>
</fieldset>

<hr />

<fieldset>
	<legend><?php echo __d('comment', 'SPAM Protection'); ?></legend>

	<?php echo $this->Form->input('use_akismet', ['id' => 'use-akismet', 'type' => 'checkbox', 'label' => __d('comment', 'Use Akismet'), 'onclick' => 'toggleAkismetOptions();']); ?>

	<div class="akismet-options">
		<?php echo $this->Form->input('akismet_key', ['type' => 'text', 'label' => __d('comment', 'Akismet API Key *')]); ?>
		<em class="help-block"><?php echo __d('comment', 'Sign up for an Akismet <a href="" target="_blank">API key here</a>.', 'http://akismet.com/wordpress/'); ?></em>

		<?php echo $this->Form->label(__d('comment', 'On Spam detected')); ?><br />
		<?php
			echo $this->Form->radio('akismet_action', [
				'mark' => __d('comment', 'Mark as SPAM'),
				'delete' => __d('comment', 'Delete comment'),
			]);
		?>
	</div>
</fieldset>

<script>
	function toggleAkismetOptions() {
		if ($('#use-akismet').is(':checked')) {
			$('.akismet-options').show();
			$('#akismet-key').attr('required', 'required');
			$("[name='akismet_action']").attr('required', 'required');
		} else {
			$('.akismet-options').hide();
			$('#akismet-key').removeAttr('required');
			$("input:radio[name='akismet_action']").removeAttr('required');
		}
	}

	function toggleAnonymousCommentOptions() {
		if ($('#allow-anonymous-comments').is(':checked')) {
			$('.anonymous-comments-options').show();
		} else {
			$('.anonymous-comments-options').hide();
		}
	}

	function toggleAyahOptions() {
		if ($('#use-ayah').is(':checked')) {
			$('.ayah-options').show();
			$('#ayah-publisher-key').attr('required', 'required');
			$('#ayah-scoring-key').attr('required', 'required');
		} else {
			$('.ayah-options').hide();
			$('#ayah-publisher-key').removeAttr('required');
			$('#ayah-scoring-key').removeAttr('required');
		}
	}

	$(document).ready(function () {
		toggleAyahOptions();
		toggleAnonymousCommentOptions();
		toggleAkismetOptions();
	});
</script>

<style>
	.anonymous-comments-options div.select { display:inline; }
</style>