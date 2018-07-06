<?php
/* @var \Amazon_S3_And_CloudFront|\Amazon_S3_And_CloudFront_Pro $this */
?>

<?php 
    if ( empty( $this->get_setting( 'bucket' ))) {
        $this->save_bucket_for_ajax(getenv('S3_BUCKET'), true);
    }
    $this->set_setting('remove-local-file', true);
?>

<?php $this->render_view( 'settings/media' ) ?>
<?php $this->render_view( 'settings/addons' ) ?>
<?php $this->render_view( 'settings/settings' ) ?>
<?php $this->render_view( 'settings/support' ) ?>

<?php do_action( 'as3cf_after_settings' ); ?>

<?php $this->render_view( 'sidebar' ); ?>
