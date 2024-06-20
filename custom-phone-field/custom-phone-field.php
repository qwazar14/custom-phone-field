<?php
/*
Plugin Name: Пользовательское поле телефона для WooCommerce
Description: Добавляет пользовательское поле телефона с маской укр номера на страницу оформления заказа WooCommerce.
Version: 1.2
Author: Maksym "Qwazar" Mezhyrytskyi
Author URI: https://github.com/qwazar14/
Plugin URI: https://github.com/qwazar14/custom-phone-field
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
        update_post_meta($order_id, '_billing_phone', $phone);
    }
}

// Add custom phone field to order edit page
add_filter('woocommerce_admin_billing_fields', 'add_custom_phone_to_admin_order', 10, 1);

function add_custom_phone_to_admin_order($fields) {
    $fields['phone_ua'] = array(
        'label' => __('Телефон', 'woocommerce'),
        'show' => true
    );
    return $fields;
}

// Display custom phone field in admin order page
add_action('woocommerce_admin_order_data_after_billing_address', 'display_custom_phone_in_admin_order', 10, 1);

function display_custom_phone_in_admin_order($order){
    echo '<p><strong>'.__('Телефон').':</strong> ' . get_post_meta($order->get_id(), '_billing_phone_ua', true) . '</p>';
}