<?php
namespace Inboxify\Wordpress;
?>

<h3><?php L10n::_e( $title ) ?></h3>
<p>
    <label>
        <input class="inboxify-subscribe" name="inboxify_subscribe" type="checkbox" value="1"<?php echo $checked ?>/> 
        <?php print L10n::_e( $label ) ?>
    </label><br/>
</p>
