<?php
namespace Inboxify\Wordpress;
?>
<div class="inboxify-subscribe">
    <?php
        $i = rand(0,99);

        if ( $settings['title'] ) {
            print $args['before_title'] . apply_filters( 'widget_title', $settings['title'] ). $args['after_title'];
        }
        
        $fields = array();
    ?>
    
    <noscript><?php L10n::__( 'Inboxify Newsletter Subscription requires JavaScript.'); ?></noscript>

    <form action="<?php print admin_url( 'admin-ajax.php' ) ?>" class="comment-form inboxify-subscribe-form" id="inboxify-subscribe-form-<?php print $i ?>" method="post" novalidate="novalidate">
        <input type="hidden" name="action" value="inboxify_subscribe">
        <input class="inboxify-input" name="inboxify[post_id]" type="hidden" value="<?php echo $post_id ?>" data-name="post_id" />
        <input class="inboxify-input" name="inboxify[type]" type="hidden" value="<?php echo $type ?>" data-name="type" />
        <input class="inboxify-input" name="inboxify[widget_id]" type="hidden" value="<?php echo $widget_id ?>" data-name="widget_id" />
        <input class="inboxify-input" name="inboxify[list_id]" type="hidden" value="<?php echo $list_id ?>" data-name="list_id" />
        
        <?php if ( $settings['html_before'] ): ?>
            <div class="inboxify-before"><?php print $settings['html_before'] ?></div>
        <?php endif; ?>
    
        <?php ob_start(); ?>
        <p class="inboxify-form-element inboxify-email">
            <label for="inboxify-email-<?php print $i ?>"><?php print $settings['email'] ?> <span class="required">*</span></label>
            <input class="inboxify-input inboxify-email-input validate-email" id="inboxify-email-<?php print $i ?>" name="inboxify[email]" required="required" data-name="email" type="email" />
        </p>
        <?php $fields['email'] = ob_get_clean(); ?>
        
        <?php ob_start(); ?>
        <?php if ( $settings['company_displayed'] ): ?>
        <p class="inboxify-form-element inboxify-company">
            <label for="inboxify-company-<?php print $i ?>"><?php print $settings['company_label'] ?><?php if ( $settings['company_required'] ): echo ' <span class="required">*</span>'; endif; ?></label>
            <input class="inboxify-input inboxify-company-input" id="inboxify-company-<?php print $i ?>" type="text" name="inboxify[company]"<?php if ( $settings['company_required'] ): ?> required="required"<?php endif ?> data-name="company" />
        </p>
        <?php endif; ?>
        <?php $fields['company_label'] = ob_get_clean(); ?>
        
        <?php ob_start(); ?>
        <?php if ( $settings['first_name_displayed'] ): ?>
        <p class="inboxify-form-element inboxify-first-name">
            <label for="inboxify-first-name-<?php print $i ?>"><?php print $settings['first_name_label'] ?><?php if ( $settings['first_name_required'] ): echo ' <span class="required">*</span>'; endif; ?></label>
            <input class="inboxify-input inboxify-first-name-input" id="inboxify-first-name-<?php print $i ?>" type="text" name="inboxify[first_name]"<?php if ( $settings['first_name_required'] ): ?> required="required"<?php endif ?> data-name="first_name" />
        </p>
        <?php endif; ?>
        <?php $fields['first_name_label'] = ob_get_clean(); ?>
        
        <?php ob_start(); ?>
        <?php if ( $settings['middle_name_displayed'] ): ?>
        <p class="inboxify-form-element inboxify-middle-name">
            <label for="inboxify-middle-name-<?php print $i ?>"><?php print $settings['middle_name_label'] ?><?php if ( $settings['middle_name_required'] ): echo ' <span class="required">*</span>'; endif; ?></label>
            <input class="inboxify-input inboxify-middle-name-input" id="inboxify-middle-name-<?php print $i ?>" type="text" name="inboxify[middle_name]"<?php if ( $settings['middle_name_required'] ): ?> required="required"<?php endif ?> data-name="middle_name" />
        </p>
        <?php endif; ?>
        <?php $fields['middle_name_label'] = ob_get_clean(); ?>
        
        <?php ob_start(); ?>
        <?php if ( $settings['last_name_displayed'] ): ?>
        <p class="inboxify-form-element inboxify-last-name">
            <label for="inboxify-last-name-<?php print $i ?>"><?php print $settings['last_name_label'] ?><?php if ( $settings['last_name_required'] ): echo ' <span class="required">*</span>'; endif; ?></label>
            <input class="inboxify-input inboxify-last-name-input" id="inboxify-last-name-<?php print $i ?>" type="text" name="inboxify[last_name]"<?php if ( $settings['last_name_required'] ): ?> required="required"<?php endif ?> data-name="last_name" />
        </p>
        <?php endif; ?>
        <?php $fields['last_name_label'] = ob_get_clean(); ?>
        
        <?php ob_start(); ?>
        <?php if ( $settings['sex_displayed'] ): ?>
        <p class="inboxify-form-element inboxify-sex">
            <label for="inboxify-last-name-<?php print $i ?>"><?php print $settings['sex_label'] ?><?php if ( $settings['sex_required'] ): echo ' <span class="required">*</span>'; endif; ?></label>
            <select class="inboxify-input inboxify-sex-input" data-name="sex" id="inboxify-sex"<?php if ( $settings['sex_required'] ): ?> required="required"<?php endif ?>>
                <option value=""><?php L10n::_e( 'Select Sex') ?></option>
                <option value="1"><?php L10n::_e( 'Male') ?></option>
                <option value="2"><?php L10n::_e( 'Female') ?></option>
            </select>
        </p>
        <?php endif; ?>
        <?php $fields['sex_label'] = ob_get_clean(); ?>
        
        <?php ob_start(); ?>
        <?php if ( $settings['telephone_displayed'] ): ?>
        <p class="inboxify-form-element inboxify-telephone">
            <label for="inboxify-telephone-<?php print $i ?>"><?php print $settings['telephone_label'] ?><?php if ( $settings['telephone_required'] ): echo ' <span class="required">*</span>'; endif; ?></label>
            <input class="inboxify-input inboxify-telephone-input" id="inboxify-telephone-<?php print $i ?>" type="text" name="inboxify[telephone]"<?php if ( $settings['telephone_required'] ): ?> required="required"<?php endif ?> data-name="telephone" />
        </p>
        <?php endif; ?>
        <?php $fields['telephone_label'] = ob_get_clean(); ?>
        
        <?php ob_start(); ?>
        <?php if ( $settings['mobile_displayed'] ): ?>
        <p class="inboxify-form-element inboxify-mobile">
            <label for="inboxify-mobile-<?php print $i ?>"><?php print $settings['mobile_label'] ?><?php if ( $settings['mobile_required'] ): echo ' <span class="required">*</span>'; endif; ?></label>
            <input class="inboxify-input inboxify-mobile-input" id="inboxify-mobile-<?php print $i ?>" type="text" name="inboxify[mobile]"<?php if ( $settings['mobile_required'] ): ?> required="required"<?php endif ?> data-name="mobile" />
        </p>
        <?php endif; ?>
        <?php $fields['mobile_label'] = ob_get_clean(); ?>
        
        <?php ob_start(); ?>
        <?php if ( $settings['address_displayed'] ): ?>
        <p class="inboxify-form-element inboxify-address">
            <label for="inboxify-address-<?php print $i ?>"><?php print $settings['address_label'] ?><?php if ( $settings['address_required'] ): echo ' <span class="required">*</span>'; endif; ?></label>
            <input class="inboxify-input inboxify-address-input" id="inboxify-address-<?php print $i ?>" type="text" name="inboxify[address]"<?php if ( $settings['address_required'] ): ?> required="required"<?php endif ?> data-name="address" />
        </p>
        <?php endif; ?>
        <?php $fields['address_label'] = ob_get_clean(); ?>
        
        <?php ob_start(); ?>
        <?php if ( $settings['zip_displayed'] ): ?>
        <p class="inboxify-form-element inboxify-zip">
            <label for="inboxify-zip-<?php print $i ?>"><?php print $settings['zip_label'] ?><?php if ( $settings['zip_required'] ): echo ' <span class="required">*</span>'; endif; ?></label>
            <input class="inboxify-input inboxify-zip-input" id="inboxify-zip-<?php print $i ?>" type="text" name="inboxify[zip]"<?php if ( $settings['zip_required'] ): ?> required="required"<?php endif ?> data-name="postalCode" />
        </p>
        <?php endif; ?>
        <?php $fields['zip_label'] = ob_get_clean(); ?>
        
        <?php ob_start(); ?>
        <?php if ( $settings['city_displayed'] ): ?>
        <p class="inboxify-form-element inboxify-city">
            <label for="inboxify-city-<?php print $i ?>"><?php print $settings['city_label'] ?><?php if ( $settings['city_required'] ): echo ' <span class="required">*</span>'; endif; ?></label>
            <input class="inboxify-input inboxify-city-input" id="inboxify-city-<?php print $i ?>" type="text" name="inboxify[city]"<?php if ( $settings['city_required'] ): ?> required="required"<?php endif ?> data-name="city" />
        </p>
        <?php endif; ?>
        <?php $fields['city_label'] = ob_get_clean(); ?>
        
        <?php ob_start(); ?>
        <?php if ( $settings['country_displayed'] ): ?>
        <p class="inboxify-form-element inboxify-country">
            <label for="inboxify-country-<?php print $i ?>"><?php print $settings['country_label'] ?><?php if ( $settings['country_required'] ): echo ' <span class="required">*</span>'; endif; ?></label>
            <?php Countries::select('inboxify-input inboxify-country-input', 'countryCode', 'inboxify-country', 'inboxify[country]', $settings['country_required']) ?>
        </p>
        <?php endif; ?>
        <?php $fields['country_label'] = ob_get_clean(); ?>

        <?php ob_start(); ?>
        <?php if ( $settings['custom_date_displayed'] ): ?>
        <p class="inboxify-form-element inboxify-custom_date">
            <label for="inboxify-custom_date-<?php print $i ?>"><?php print $settings['custom_date_label'] ?><?php if ( $settings['custom_date_required'] ): echo ' <span class="required">*</span>'; endif; ?></label>
            <input class="inboxify-input inboxify-custom_date-input" id="inboxify-custom_date-<?php print $i ?>" type="date" name="inboxify[custom_date]"<?php if ( $settings['custom_date_required'] ): ?> required="required"<?php endif ?> data-name="custom_date" />
        </p>
        <?php endif; ?>
        <?php $fields['custom_date_label'] = ob_get_clean(); ?>
        
        <?php ob_start(); ?>
        <?php if ( $settings['tags_displayed'] ): ?>
        <p class="inboxify-form-element inboxify-tags">
            <?php switch($settings['tags_input']) {
                case 'input':
                ?>
                    <label for="inboxify-tags-<?php print $i ?>"><?php print $settings['tags_label'] ?><?php if ( $settings['tags_required'] ): echo ' <span class="required">*</span>'; endif; ?></label>
                    <input class="inboxify-input inboxify-tags-input" id="inboxify-tags-<?php print $i ?>" type="text" name="inboxify[tags]"<?php if ( $settings['tags_required'] ): ?> required="required"<?php endif ?> data-name="tags" />
                <?php break;
                case 'select':
                    ?>
                    <label for="inboxify-tags-<?php print $i ?>"><?php print $settings['tags_label'] ?><?php if ( $settings['tags_required'] ): echo ' <span class="required">*</span>'; endif; ?></label>
                    <select class="inboxify-input inboxify-tags-input" id="inboxify-tags-<?php print $i ?>" name="inboxify[tags]"<?php if ( $settings['tags_required'] ): ?> required="required"<?php endif ?> data-name="tags">
                        <?php foreach($settings['tags_allowed_array'] as $tag): ?>
                        <option value="<?php esc_attr_e($tag) ?>"><?php esc_html_e($tag) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php
                    break;
                case 'checkbox':
                    ?>
                    <label class="inboxify-group-label"><?php esc_html_e($settings['tags_label']) ?><?php if ( $settings['tags_required'] ): echo ' <span class="required">*</span>'; endif; ?></label>
                    
                    <?php
                        foreach($settings['tags_allowed_array'] as $j => $tag) {
                    ?>
                        <input class="inboxify-input inboxify-tags-input" id="inboxify-tags-<?php print $i ?>-<?php print $j ?>" type="checkbox" name="inboxify[tags][]" data-name="tags"<?php if ( $settings['tags_required'] ): ?> required="required" <?php endif ?>value="<?php esc_attr_e($tag) ?>" />
                        <label for="inboxify-tags-<?php print $i ?>-<?php print $j ?>"><?php esc_html_e($tag) ?></label>
                    <?php
                        } // end foreach
                    break;
                case 'radio':
                    ?>
                    <label class="inboxify-group-label"><?php esc_html_e($settings['tags_label']) ?><?php if ( $settings['tags_required'] ): echo ' <span class="required">*</span>'; endif; ?></label>
                    
                    <?php
                    foreach($settings['tags_allowed_array'] as $j => $tag) {
                    ?>
                        <input class="inboxify-input inboxify-tags-input" id="inboxify-tags-<?php print $i ?>-<?php print $j ?>" type="radio" name="inboxify[tags][]" data-name="tags"<?php if ( $settings['tags_required'] ): ?> required="required" <?php endif ?>value="<?php esc_attr_e($tag) ?>" />
                        <label for="inboxify-tags-<?php print $i ?>-<?php print $j ?>"><?php esc_html_e($tag) ?></label>
                    <?php
                    } // end foreach
                    break;
                default:
                    // INFO problem
                    break;
            } // endswitch ?>
        </p>
        <?php endif; ?>
        <?php $fields['tags_label'] = ob_get_clean(); ?>
        
        <?php
            if ($sort) {
                uksort($fields, function($a, $b) use ($sort) {
                    $ak = array_search($a, $sort);
                    $bk = array_search($b, $sort);

                    return $ak - $bk;
                });
            }
            
            foreach($fields as $field) {
                print $field;
            }
        ?>
        
        <?php if (isset($captcha)): print '<p class="inboxify-form-element inboxify-captcha">' . $captcha . '</p>'; endif; ?>
        
        <?php if ( $settings['html_after'] ): ?>
            <div class="inboxify-after"><?php print $settings['html_after'] ?></div>
        <?php endif; ?>
        
        <p class="form-submit">
            <input class="inboxify-submit" type="submit" value="<?php print $settings['submit_label'] ?>" />
        </p>
        
    </form>
    
    <script type="text/javascript">
        jQuery(document).ready(function() {
            //setTimeout(function() {
            <?php if ('invisible-recaptcha' == $captcha_type): ?>
                var ire = {
                    siteKey: "<?php print str_replace('"', '\"', \InvisibleReCaptcha\Modules\Settings\SettingsPublicModule::getInstance()->getOption(\InvisibleReCaptcha\Modules\Settings\SettingsAdminModule::OPTION_SITE_KEY)) ?>",
                    badgePosition: "bottomright"
                };
            <?php else: ?>
                var ire = {};
            <?php endif; ?>
            
                jQuery('#inboxify-subscribe-form-<?php print $i ?>').inboxifySubscribe({
                    message_invalid: "<?php echo str_replace('"', '\"', $settings['message_invalid']) ?>",
                    message_invalid_form: "<?php echo str_replace('"', '\"', $settings['message_invalid_form']) ?>",
                    message_error: "<?php echo str_replace('"', '\"', $settings['message_error']) ?>",
                    message_success: "<?php echo str_replace('"', '\"', $settings['message_success']) ?>"
                }, ire);
            //}, 500);
        });
    </script>
    
</div>
