<?php
namespace Inboxify\Wordpress;
?>
<div class="wrap">
    <h2><?php L10n::__( 'Inboxify API Settings' ) ?></h2>
    
    <div id="inboxify-messages"></div>
    
    <form id="inboxify-settings" method="post" action="options.php">
        <?php 
        settings_fields( Settings::GROUP );
        do_settings_sections( Settings::GROUP );
        submit_button();
        settings_errors( Settings::GROUP ); // make sure the errors are displayed
        ?>
    </form>
</div>

<script type="text/javascript">
jQuery(document).on("ready", function() {
    jQuery("#inboxify-settings").inboxifySettings();
});
</script>
