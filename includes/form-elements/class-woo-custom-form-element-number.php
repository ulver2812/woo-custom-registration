<?php
/**
 * Created by PhpStorm.
 * User: ulver
 * Date: 13/02/2019
 * Time: 11:49
 */

class Woo_Custom_Form_Element_Number implements Woocommerce_Custom_Registration_iForm_Element
{

    public static function getType()
    {
        return 'number';
    }

    public static function getLabel()
    {
        return 'Number';
    }

    public static function getSettings($current_settings)
    {

        $label = !empty($current_settings) ? $current_settings['label'] : '';
        $name = !empty($current_settings) ? $current_settings['name'] : '';
        $min = !empty($current_settings) ? $current_settings['min'] : '';
        $max = !empty($current_settings) ? $current_settings['max'] : '';
        $element_description = !empty($current_settings) ? $current_settings['description'] : '';
        $required = !empty($current_settings) ? $current_settings['required'] : '0';

        $input_name_description = __('This field will be used as usermeta key name.', Woocommerce_Custom_Registration_i18n::$textdomain);
        $input_description = __('This is the field description.', Woocommerce_Custom_Registration_i18n::$textdomain);

        $input_required = checked('1', $required, false);

        $settings = <<<HTML
        <div class="woocommerce-custom-registration-form-block">
            <label>Label</label>
            <input type="text" name="woo_custom_registration_element_label" value="$label" required />
        </div>

        <div class="woocommerce-custom-registration-form-block">
            <label>Input name</label>
            <input type="text" name="woo_custom_registration_element_name" value="$name" required />
            <p>$input_name_description</p>
        </div>
        
        <div class="woocommerce-custom-registration-form-block">
            <label>Description</label>
            <input type="text" name="woo_custom_registration_element_description" value="$element_description" />
            <p>$input_description</p>
        </div>
        
        <div class="woocommerce-custom-registration-form-block">
            <label>Required</label>
            <input $input_required type="checkbox" name="woo_custom_registration_element_required" value="1" />           
        </div>

        <div class="woocommerce-custom-registration-form-block">
            <label>Min</label>
            <input step="0.01" type="number" name="woo_custom_registration_element_number_min" value="$min" />
        </div>

        <div class="woocommerce-custom-registration-form-block">
            <label>Max</label>
            <input step="0.01" type="number" name="woo_custom_registration_element_number_max" value="$max" />
        </div>
HTML;

        return $settings;
    }

    public static function saveSettings($post_id)
    {

        if (!isset($_POST['woo_custom_registration_element_label'])) {
            return;
        }

        if (!isset($_POST['woo_custom_registration_element_name'])) {
            return;
        }

        if (!isset($_POST['woo_custom_registration_element_number_min'])) {
            return;
        }

        if (!isset($_POST['woo_custom_registration_element_number_max'])) {
            return;
        }

        if (!isset($_POST['woo_custom_registration_element_description'])) {
            return;
        }

        if (!isset($_POST['woo_custom_form_element_order'])) {
            return;
        }

        update_post_meta($post_id, 'woo_custom_register_element_order', sanitize_text_field($_POST['woo_custom_form_element_order']));

        update_post_meta($post_id, 'woo_custom_register_element', array(
            'type' => self::getType(),
            'label' => sanitize_text_field($_POST['woo_custom_registration_element_label']),
            'name' => Woocommerce_Custom_Registration_Utils::slugify($_POST['woo_custom_registration_element_name']),
            'min' => sanitize_text_field($_POST['woo_custom_registration_element_number_min']),
            'max' => sanitize_text_field($_POST['woo_custom_registration_element_number_max']),
            'description' => sanitize_text_field($_POST['woo_custom_registration_element_description']),
            'required' => isset($_POST['woo_custom_registration_element_required']) ? '1' : '0',
        ));

    }

    public static function elementRendering($element)
    {
        if (empty($element)) {
            return;
        }

        $type = isset($element['type']) ? $element['type'] : '';
        $name = isset($element['name']) ? $element['name'] : '';
        $label = isset($element['label']) ? $element['label'] : '';
        $min = isset($element['min']) ? $element['min'] : '';
        $max = isset($element['max']) ? $element['max'] : '';
        $description = isset($element['description']) ? $element['description'] : '';
        $required = isset($element['required']) ? $element['required'] : '0';
        ?>
        <p class="form-row form-row-wide">
            <label for="<?php echo $name; ?>"><?php echo $label; ?> <?php echo $required === '1' ? '<span class="required">*</span></label>' : ''; ?>
                <input min="<?php echo $min; ?>" max="<?php echo $max; ?>" type="<?php echo $type; ?>"
                       class="input-text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value=""/>
                <span><?php echo $description; ?></span>
        </p>
        <?php
    }

    public static function elementValidation($element, $validation_errors)
    {

        if (empty($element)) {
            return;
        }

        if (isset($_POST[$element['name']]) && empty($_POST[$element['name']]) && $element['required'] === '1') {
            $validation_errors->add($element['name'] . '_error', $element['label'] . __(' is required', Woocommerce_Custom_Registration_i18n::$textdomain));
        }
    }

    public static function elementSave($element, $customer_id)
    {

        if (empty($element)) {
            return;
        }

        if (isset($_POST[$element['name']])) {
            $field = sanitize_text_field($_POST[$element['name']]);
            update_user_meta($customer_id, $element['name'], $field);
        }
    }

    public static function elementAdminUserProfileRendering($element, $customer_id)
    {

        if (empty($element)) {
            return;
        }

        $value = get_user_meta($customer_id, $element['name'], true);

        ?>
        <tr>
            <th><label for="address"><?php echo $element['label'] ?></label></th>
            <td>
                <input <?php echo $element['required'] === '1' ? 'required' : ''; ?>
                        min="<?php echo $element['min']; ?>" max="<?php echo $element['max']; ?>"
                        type="<?php echo $element['type']; ?>" name="<?php echo $element['name']; ?>"
                        id="<?php echo $element['name']; ?>" value="<?php echo $value; ?>" class="regular-text"/><br/>
                <span class="description"><?php echo $element['description']; ?></span>
            </td>
        </tr>
        <?php
    }

    public static function elementPublicUserProfileRendering($element, $customer_id)
    {

        if (empty($element)) {
            return;
        }

        $value = get_user_meta($customer_id, $element['name'], true);

        ?>
        <p class="form-row form-row-wide">
            <label for="<?php echo $element['name']; ?>"><?php echo $element['label']; ?> <?php echo $element['required'] === '1' ? '<span class="required">*</span></label>' : ''; ?></label>
                <input <?php echo $element['required'] === '1' ? 'required' : ''; ?>
                        min="<?php echo $element['min']; ?>" max="<?php echo $element['max']; ?>"
                        type="<?php echo $element['type']; ?>" class="input-text" name="<?php echo $element['name']; ?>"
                        id="<?php echo $element['name']; ?>" value="<?php echo $value; ?>"/>
                <span><?php echo $element['description']; ?></span>
        </p>
        <?php
    }

    public static function elementRenderingCheckout($element, &$fields)
    {

        if (empty($element)) {
            return;
        }

        $fields['account'][$element['name']] = array(
            'label' => $element['label'],
            'type' => $element['type'],
            'custom_attributes' => array(
                'max' => $element['max'],
                'min' => $element['min'],
            ),
            'description' => $element['description'],
            'required' => $element['required'] === '1' ? true : false,
            'class' => array('form-row-wide'),
            'clear' => true
        );

        return $fields;

    }
}