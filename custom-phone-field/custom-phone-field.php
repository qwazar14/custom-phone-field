<?php
/*
Plugin Name: Пользовательское поле телефона для WooCommerce
Description: Добавляет пользовательское поле телефона с маской укр номера на страницу оформления заказа WooCommerce.
Version: 1.1
Author: Maksym "Qwazar" Mezhyrytskyi
Author URI: https://github.com/qwazar14/
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Add custom phone field to WooCommerce checkout
add_filter('woocommerce_checkout_fields', 'add_custom_phone_field');

function add_custom_phone_field($fields) {
    $fields['billing']['billing_phone_ua'] = array(
        'type'        => 'tel',
        'label'       => __('Телефон', 'woocommerce'),
        'placeholder' => _x('+380-xx-xxx-xx-xx', 'placeholder', 'woocommerce'),
        'required'    => true,
        'class'       => array('form-row-wide'),
        'clear'       => true,
        'priority'    => 22,
    );

    return $fields;
}

// Enqueue jQuery Mask Plugin and custom script
add_action('wp_enqueue_scripts', 'enqueue_custom_phone_field_scripts');

function enqueue_custom_phone_field_scripts() {
    if (is_checkout()) {
        wp_enqueue_script('jquery-mask-plugin', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js', array('jquery'), '1.14.16', true);
        wp_enqueue_script('custom-phone-field-script', plugins_url('custom-phone-field.js', __FILE__), array('jquery-mask-plugin'), '1.0', true);
    }
}

// Save custom phone field without dashes
add_action('woocommerce_checkout_update_order_meta', 'save_custom_phone_field');

function save_custom_phone_field($order_id) {
    if (isset($_POST['billing_phone_ua'])) {
        $phone = str_replace('-', '', sanitize_text_field($_POST['billing_phone_ua']));
        update_post_meta($order_id, '_billing_phone_ua', $phone);
    }
}