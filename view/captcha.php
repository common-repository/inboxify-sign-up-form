<div class="inboxify-form-element inboxify-captcha">
    <?php if ($label): ?>
    <label for="<?php print $id ?>"><?php print $label ?> <span class="required">*</span></label><br/>
    <?php endif; ?>
    <?php print $captcha ?>
</div>
