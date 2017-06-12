<?php
/**
 * @var string[] $successes
 * @var string[] $warnings
 * @var string[] $notices
 * @var string[] $errors
 */
?>

<?php if ( isset( $successes ) && count( $successes ) > 0 ): ?>
	<?php foreach( $successes as $success ): ?>
		<div class="notice notice-success is-dismissible">
			<p><?php echo $success; ?></p>
			<button type="button" class="notice-dismiss">
				<span class="screen-reader-text"><?php echo __( 'Dismiss this notice.', 'a2-optimized' ); ?></span>
			</button>
		</div>
	<?php endforeach; ?>
<?php endif; ?>

<?php if ( isset( $errors ) && count( $errors ) > 0 ): ?>
	<?php foreach( $errors as $error ): ?>
		<div class="notice notice-error is-dismissible">
			<p><?php echo $error; ?></p>
			<button type="button" class="notice-dismiss">
				<span class="screen-reader-text"><?php echo __( 'Dismiss this notice.', 'a2-optimized' ); ?></span>
			</button>
		</div>
	<?php endforeach; ?>
<?php endif; ?>

<?php if ( isset( $warnings ) && count( $warnings ) > 0 ): ?>
	<?php foreach( $warnings as $warning ): ?>
		<div class="notice notice-warning is-dismissible">
			<p><?php echo $warning; ?></p>
			<button type="button" class="notice-dismiss">
				<span class="screen-reader-text"><?php echo __( 'Dismiss this notice.', 'a2-optimized' ); ?></span>
			</button>
		</div>
	<?php endforeach; ?>
<?php endif; ?>


<?php if ( isset( $notices ) && count( $notices ) > 0 ): ?>
	<?php foreach( $notices as $notice ): ?>
		<div class="notice is-dismissible">
			<p><?php echo $notice; ?></p>
			<button type="button" class="notice-dismiss">
				<span class="screen-reader-text"><?php echo __( 'Dismiss this notice.', 'a2-optimized' ); ?></span>
			</button>
		</div>
	<?php endforeach; ?>
<?php endif; ?>
