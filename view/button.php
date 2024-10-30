<?php
use Inboxify\Wordpress\L10n;
include( ABSPATH . 'wp-admin/admin-header.php' );
?>
<script type="text/javascript" src="<?php print get_option('siteurl') ?>/wp-includes/js/tinymce/tinymce.min.js?ver=4603-20170530"></script>
<script type="text/javascript" src="<?php print get_option('siteurl') ?>/wp-includes/js/tinymce/plugins/compat3x/plugin.js?ver=4603-20170530"></script>
<script type="text/javascript" src="<?php print get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
<script type="text/javascript" src="<?php print get_option('siteurl') ?>/wp-includes/js/underscore.min.js"></script>
<script type="text/javascript" src="<?php print get_option('siteurl') ?>/wp-includes/js/shortcode.js"></script>
<style type="text/css">
    #adminmenumain, #wpfooter {
        display: none;
    }
    #wpbody {
        padding-top: 5px!important;
    }
    #wpcontent {
        margin-left: 0;
        padding: 0 15px 0 15px!important;
    }
    #wpbody-content {
        padding-bottom: 15px!important;
    }
</style>

<form id="iy-shortcode-generator" method="post">
<?php
$widget = new Inboxify\Wordpress\Widget\Subscribe();
$widget->form($instance);
?>
    <input type="submit" value="<?php L10n::_e('Insert') ?>">
</form>

<script type="text/javascript">
var iy_map = <?= json_encode(array_flip($map)) ?>;
jQuery(document).on("ready", function() {
    jQuery("#iy-shortcode-generator").inboxifyShortcodeGenerator();
});
</script>

<?php
require( ABSPATH . 'wp-admin/admin-footer.php' );
