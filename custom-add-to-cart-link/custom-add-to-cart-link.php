<?php
/*
Plugin Name: Custom Add to Cart Link
Description: Reemplaza el botón "Agregar al carrito" por un enlace personalizado en WooCommerce.
Version: 1.0.5
Author: Abraham Bronstein
Text Domain: custom-add-to-cart-link
Domain Path: /languages
*/

if (!defined('ABSPATH')) {
    exit; // Salir si se accede directamente
}

// Incluir el campo personalizado en el formulario de producto
function cadcl_add_custom_field() {
    // Campo para el enlace personalizado
    woocommerce_wp_text_input(array(
        'id' => '_custom_add_to_cart_link',
        'label' => __('Custom Add to Cart Link', 'custom-add-to-cart-link'),
        'placeholder' => 'https://',
        'desc_tip' => 'true',
        'description' => __('Introduce el enlace que reemplazará el botón "Agregar al carrito".', 'custom-add-to-cart-link')
    ));

    // Campo para el texto personalizado del botón
    woocommerce_wp_text_input(array(
        'id' => '_custom_button_text',
        'label' => __('Custom Button Text', 'custom-add-to-cart-link'),
        'placeholder' => __('Ver más', 'custom-add-to-cart-link'),
        'desc_tip' => 'true',
        'description' => __('Introduce el texto que aparecerá en lugar de "Agregar al carrito".', 'custom-add-to-cart-link')
    ));
}
add_action('woocommerce_product_options_general_product_data', 'cadcl_add_custom_field');

// Guardar el valor del campo personalizado
function cadcl_save_custom_field($post_id) {
    $custom_link = isset($_POST['_custom_add_to_cart_link']) ? sanitize_text_field($_POST['_custom_add_to_cart_link']) : '';
    update_post_meta($post_id, '_custom_add_to_cart_link', $custom_link);
    // Guardar el texto personalizado del botón
    $custom_button_text = isset($_POST['_custom_button_text']) ? sanitize_text_field($_POST['_custom_button_text']) : '';
    update_post_meta($post_id, '_custom_button_text', $custom_button_text);
}
add_action('woocommerce_process_product_meta', 'cadcl_save_custom_field');

// Reemplazar el botón "Agregar al carrito" por el enlace personalizado
function cadcl_replace_add_to_cart_button() {
    global $product;

    $custom_link = get_post_meta($product->get_id(), '_custom_add_to_cart_link', true);
    $custom_button_text = get_post_meta($product->get_id(), '_custom_button_text', true);

    if ($custom_link) {
        echo '<a href="' . esc_url($custom_link) . '" class="wp-block-button__link wp-element-button wc-block-components-product-button__button product_type_simple has-font-size has-small-font-size has-text-align-center wc-interactive">' .$custom_button_text. '</a>';
    } else {
        woocommerce_template_loop_add_to_cart();
    }
}
add_action('woocommerce_after_shop_loop_item', 'cadcl_replace_add_to_cart_button', 10);
add_action('woocommerce_single_product_summary', 'cadcl_replace_add_to_cart_button', 30);