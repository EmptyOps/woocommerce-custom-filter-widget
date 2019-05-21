<?php
class EO_WBC_Support
{
    public static function eo_wbc_get_cart_url() {
        return function_exists('wc_get_cart_url')?wc_get_cart_url():apply_filters( 'woocommerce_get_cart_url', self::wc_get_page_permalink( 'cart' ));
    }

    public static function eo_wbc_get_product($product_id){
        return function_exists('wc_get_product')?wc_get_product($product_id):WC()->product_factory->get_product($product_id,array());
    }
    
    public static function eo_wbc_get_product_variation_attributes( $variation_id ) {
        return function_exists('wc_get_product_variation_attributes')
                        ?
                            wc_get_product_variation_attributes($variation_id)
                            :
                            self::eo_wbc_product_variation_attributes($variation_id);
    }
    
    private static function eo_wbc_product_variation_attributes( $variation_id ) {
        // Build variation data from meta.
        $all_meta                = get_post_meta( $variation_id );
        $parent_id               = wp_get_post_parent_id( $variation_id );
        $parent_attributes       = array_filter( (array) get_post_meta( $parent_id, '_product_attributes', true ) );
        $found_parent_attributes = array();
        $variation_attributes    = array();
        
        // Compare to parent variable product attributes and ensure they match.
        foreach ( $parent_attributes as $attribute_name => $options ) {
            if ( ! empty( $options['is_variation'] ) ) {
                $attribute                 = 'attribute_' . sanitize_title( $attribute_name );
                $found_parent_attributes[] = $attribute;
                if ( ! array_key_exists( $attribute, $variation_attributes ) ) {
                    $variation_attributes[ $attribute ] = ''; // Add it - 'any' will be asumed.
                }
            }
        }
        
        // Get the variation attributes from meta.
        foreach ( $all_meta as $name => $value ) {
            // Only look at valid attribute meta, and also compare variation level attributes and remove any which do not exist at parent level.
            if ( 0 !== strpos( $name, 'attribute_' ) || ! in_array( $name, $found_parent_attributes ) ) {
                unset( $variation_attributes[ $name ] );
                continue;
            }
            /**
             * Pre 2.4 handling where 'slugs' were saved instead of the full text attribute.
             * Attempt to get full version of the text attribute from the parent.
             */
            if ( sanitize_title( $value[0] ) === $value[0] && version_compare( get_post_meta( $parent_id, '_product_version', true ), '2.4.0', '<' ) ) {
                foreach ( $parent_attributes as $attribute ) {
                    if ( 'attribute_' . sanitize_title( $attribute['name'] ) !== $name ) {
                        continue;
                    }
                    $text_attributes = self::wc_get_text_attributes( $attribute['value'] );
                    
                    foreach ( $text_attributes as $text_attribute ) {
                        if ( sanitize_title( $text_attribute ) === $value[0] ) {
                            $value[0] = $text_attribute;
                            break;
                        }
                    }
                }
            }
            
            $variation_attributes[ $name ] = $value[0];
        }
        
        return $variation_attributes;
    }
    
    private function wc_get_text_attributes( $raw_attributes ){
        return array_filter( array_map( 'trim', explode( WC_DELIMITER, html_entity_decode( $raw_attributes, ENT_QUOTES, get_bloginfo( 'charset' ) ) ) ), array(self,'wc_get_text_attributes_filter_callback') );
    }
    
    private function wc_get_text_attributes_filter_callback( $value ){
        return '' !== $value;
    }
    
    private function wc_get_page_permalink( $page ) {
        $page_id   = wc_get_page_id( $page );
        $permalink = 0 < $page_id ? get_permalink( $page_id ) : get_home_url();
        return apply_filters( 'woocommerce_get_' . $page . '_page_permalink', $permalink );
    }
}