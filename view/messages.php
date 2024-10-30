    <?php if (isset($errors) && is_array($errors) && count($errors) > 0): ?>
    <div id="setting-error-settings_updated" class="error settings-error">
        <?php foreach($errors as $error): ?>
        <p><strong><?php echo $error ?></strong></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <?php if (isset($messages) && is_array($messages) && count($messages) > 0): ?>
    <div id="setting-error-settings_updated" class="updated settings-error">
        <?php foreach($messages as $message): ?>
        <p><strong><?php echo $message ?></strong></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>