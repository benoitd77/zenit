<?php
/**
 * A class to serve static data
 *
 * Class Opt_In_Static
 */
if ( !class_exists ('Opt_In_Static', false ) ) {
    class Opt_In_Static {
        /**
         * Returns animations
         * Returns Popup Pro animations if it's installed and active
         *
         *
         * @return object
         */
        public function get_animations(){

            $animations_in = array(
                '' => array(
                    '' => __( '(No Animation)', Opt_In::TEXT_DOMAIN ),
                ),
                __( 'Fading Entrances', Opt_In::TEXT_DOMAIN ) => array(
                    'fadein' => __( 'Fade In', Opt_In::TEXT_DOMAIN ),
                ),
                __( 'Slidein Entrances', Opt_In::TEXT_DOMAIN ) => array(
                    'slideright' => __( 'Slide In (Right)', Opt_In::TEXT_DOMAIN ),
                    'slidebottom' => __( 'Slide In (Bottom)', Opt_In::TEXT_DOMAIN ),
                ),
                __( 'Falling Entrances', Opt_In::TEXT_DOMAIN ) => array(
                    'fall wpoi-modal' => __( 'Fall', Opt_In::TEXT_DOMAIN ),
                    'sidefall wpoi-modal' => __( 'Side Fall', Opt_In::TEXT_DOMAIN ),
                ),
                __( 'Zoom Entrances', Opt_In::TEXT_DOMAIN ) => array(
                    'scaled' => __( 'Super Scaled', Opt_In::TEXT_DOMAIN ),
                ),
                __( '3D Effects', Opt_In::TEXT_DOMAIN ) => array(
                    'sign wpoi-modal' => __( '3D Sign', Opt_In::TEXT_DOMAIN ),
                    'slit wpoi-modal' => __( '3D Slit', Opt_In::TEXT_DOMAIN ),
                    'flipx wpoi-modal' => __( '3D Flip (Horizontal)', Opt_In::TEXT_DOMAIN ),
                    'flipy wpoi-modal' => __( '3D Flip (Vertical)', Opt_In::TEXT_DOMAIN ),
                    'rotatex wpoi-modal' => __( '3D Rotate (Left)', Opt_In::TEXT_DOMAIN ),
                    'rotatey wpoi-modal' => __( '3D Rotate (Bottom)', Opt_In::TEXT_DOMAIN ),
                ),
                __( 'Specials', Opt_In::TEXT_DOMAIN ) => array(
                    'newspaper' => __( 'Newspaper', Opt_In::TEXT_DOMAIN ),
                ),
            );

            $animations_out = array(
                '' => array(
                    '' => __( '(No Animation)', Opt_In::TEXT_DOMAIN ),
                ),
                __( 'Fading Exits', Opt_In::TEXT_DOMAIN ) => array(
                    'fadein' => __( 'Fade Out', Opt_In::TEXT_DOMAIN ),
                ),
                __( 'Slidein Exits', Opt_In::TEXT_DOMAIN ) => array(
                    'slideright' => __( 'Slide Out (Right)', Opt_In::TEXT_DOMAIN ),
                    'slidebottom' => __( 'Slide Out (Bottom)', Opt_In::TEXT_DOMAIN ),
                ),
                __( 'Zoom Entrances', Opt_In::TEXT_DOMAIN ) => array(
                    'scaled' => __( 'Super Scaled', Opt_In::TEXT_DOMAIN ),
                ),
                __( '3D Effects', Opt_In::TEXT_DOMAIN ) => array(
                    'sign wpoi-modal' => __( '3D Sign', Opt_In::TEXT_DOMAIN ),
                    'flipx wpoi-modal' => __( '3D Flip (Horizontal)', Opt_In::TEXT_DOMAIN ),
                    'flipy wpoi-modal' => __( '3D Flip (Vertical)', Opt_In::TEXT_DOMAIN ),
                    'rotatex wpoi-modal' => __( '3D Rotate (Left)', Opt_In::TEXT_DOMAIN ),
                    'rotatey wpoi-modal' => __( '3D Rotate (Bottom)', Opt_In::TEXT_DOMAIN ),
                ),
                __( 'Specials', Opt_In::TEXT_DOMAIN ) => array(
                    'newspaper' => __( 'Newspaper', Opt_In::TEXT_DOMAIN ),
                ),
            );

            return (object) array(
                'in' => $animations_in,
                'out' => $animations_out,
            );

        }


        /**
         * Returns palettes used to color optins
         *
         * @return array
         */
        public function get_palettes(){
            return array(
                'Gray Slate' => array(
                    'main_background' => '#38454E',
                    'title_color' => '#FDFDFD',
                    'link_color' => '#38C5B5',
                    'content_color' => '#ADB5B7',
                    'link_hover_color' => '#49E2D1',
                    'form_background' => '#5D7380',
                    'fields_background' => '#FDFDFD',
                    'fields_hover_background' => '#FDFDFD',
                    'fields_active_background' => '#FDFDFD',
                    'label_color' => '#ADB5B7',
                    'button_background' => '#38C5B5',
                    'button_label' => '#FDFDFD',
                    'fields_color' => '#363B3F',
                    'fields_hover_color' => '#363B3F',
                    'fields_active_color' => '#363B3F',
                    'error_color' => '#C63D2B',
                    'button_hover_background' => '#49E2D1',
                    'button_active_background' => '#49E2D1',
                    'button_hover_label' => '#FDFDFD',
                    'button_active_label' => '#FDFDFD',
                    'checkmark_color' => '#38C5B5',
                    'success_color' => '#FDFDFD',
                    'close_color' => '#38C5B5',
                    'nsa_color' => '#38C5B5',
                    'overlay_background' => 'rgba(54,59,63,0.8)',
                    'close_hover_color' => '#38C5B5',
                    'close_active_color' => '#38C5B5',
                    'radio_background' => '#FDFDFD',
                    'radio_checked_background' => '#38C5B5',
                    'checkbox_background' => '#FDFDFD',
                    'checkbox_checked_color' => '#38C5B5',
                    'mcg_title_color' => '#FDFDFD',
                    'mcg_label_color' => '#ADB5B7',
                    'nsa_hover_color' => '#38C5B5',
                    'nsa_active_color' => '#38C5B5',
                ),
                'Coffee' => array(
                    'main_background' => '#46403B',
                    'title_color' => '#FDFDFD',
                    'link_color' => '#C6A685',
                    'content_color' => '#ADB5B7',
                    'link_hover_color' => '#C69767',
                    'form_background' => '#59524B',
                    'fields_background' => '#FDFDFD',
                    'label_color' => '#ADB5B7',
                    'button_background' => '#C6A685',
                    'button_label' => '#FDFDFD',
                    'fields_color' => '#363B3F',
                    'fields_hover_color' => '#363B3F',
                    'fields_active_color' => '#363B3F',
                    'error_color' => '#BE1E2D',
                    'button_hover_background' => '#C69767',
                    'button_active_background' => '#C69767',
                    'button_hover_label' => '#FDFDFD',
                    'button_active_label' => '#FDFDFD',
                    'checkmark_color' => '#38C5B5',
                    'success_color' => '#FDFDFD',
                    'close_color' => '#59524B',
                    'nsa_color' => '#C69767',
                    'overlay_background' => 'rgba(253,253,253,0.8)',
                    'close_hover_color' => '#59524B',
                    'close_active_color' => '#59524B',
                    'nsa_hover_color' => '#C6A685',
                    'nsa_active_color' => '#C6A685',
                    'radio_background' => '#FDFDFD',
                    'radio_checked_background' => '#C6A685',
                    'checkbox_background' => '#FDFDFD',
                    'checkbox_checked_color' => '#C6A685',
                    'mcg_title_color' => '#FDFDFD',
                    'mcg_label_color' => '#ADB5B7'
                ),
                'Ectoplasm' => array(
                    'main_background' => '#403159',
                    'title_color' => '#FDFDFD',
                    'link_color' => '#FDFDFD',
                    'content_color' => '#ADB5B7',
                    'link_hover_color' => '#FDFDFD',
                    'form_background' => '#513E70',
                    'fields_background' => '#FDFDFD',
                    'label_color' => '#ADB5B7',
                    'button_background' => '#A4B824',
                    'button_label' => '#FDFDFD',
                    'fields_color' => '#363B3F',
                    'fields_hover_color' => '#363B3F',
                    'fields_active_color' => '#363B3F',
                    'error_color' => '#BE1E2D',
                    'button_hover_background' => '#B9CE33',
                    'button_active_background' => '#B9CE33',
                    'button_hover_label' => '#FDFDFD',
                    'button_active_label' => '#FDFDFD',
                    'checkmark_color' => '#38C5B5',
                    'success_color' => '#FDFDFD',
                    'close_color' => '#403159',
                    'nsa_color' => '#403159',
                    'overlay_background' => 'rgba(253,253,253,0.8)',
                    'close_hover_color' => '#403159',
                    'close_active_color' => '#403159',
                    'nsa_hover_color' => '#513E70',
                    'nsa_active_color' => '#513E70',
                    'radio_background' => '#FDFDFD',
                    'radio_checked_background' => '#A4B824',
                    'checkbox_background' => '#FDFDFD',
                    'checkbox_checked_color' => '#A4B824',
                    'mcg_title_color' => '#FDFDFD',
                    'mcg_label_color' => '#ADB5B7'
                ),
                'Blue' => array(
                    'main_background' => '#176387',
                    'title_color' => '#FDFDFD',
                    'link_color' => '#FDFDFD',
                    'content_color' => '#ADB5B7',
                    'link_hover_color' => '#78B5D1',
                    'form_background' => '#78B5D1',
                    'fields_background' => '#FDFDFD',
                    'label_color' => '#ADB5B7',
                    'button_background' => '#4D95B6',
                    'button_label' => '#FDFDFD',
                    'fields_color' => '#363B3F',
                    'fields_hover_color' => '#363B3F',
                    'fields_active_color' => '#363B3F',
                    'error_color' => '#C63D2B',
                    'button_hover_background' => '#176387',
                    'button_active_background' => '#176387',
                    'button_hover_label' => '#FDFDFD',
                    'button_active_label' => '#FDFDFD',
                    'checkmark_color' => '#38C5B5',
                    'success_color' => '#FDFDFD',
                    'close_color' => '#78B5D1',
                    'nsa_color' => '#176387',
                    'overlay_background' => 'rgba(0,0,0,0.8)',
                    'close_hover_color' => '#78B5D1',
                    'close_active_color' => '#78B5D1',
                    'nsa_hover_color' => '#4D95B6',
                    'nsa_active_color' => '#4D95B6',
                    'radio_background' => '#FDFDFD',
                    'radio_checked_background' => '#4D95B6',
                    'checkbox_background' => '#FDFDFD',
                    'checkbox_checked_color' => '#4D95B6',
                    'mcg_title_color' => '#FDFDFD',
                    'mcg_label_color' => '#ADB5B7'
                ),
                'Sunrise' => array(
                    'main_background' => '#B03E34',
                    'title_color' => '#FDFDFD',
                    'link_color' => '#FDFDFD',
                    'content_color' => '#ADB5B7',
                    'link_hover_color' => '#CBB000',
                    'form_background' => '#CB4B40',
                    'fields_background' => '#FDFDFD',
                    'label_color' => '#ADB5B7',
                    'button_background' => '#CBB000',
                    'button_label' => '#FDFDFD',
                    'fields_color' => '#363B3F',
                    'fields_hover_color' => '#363B3F',
                    'fields_active_color' => '#363B3F',
                    'error_color' => '#C63D2B',
                    'button_hover_background' => '#CCB83D',
                    'button_active_background' => '#CCB83D',
                    'button_hover_label' => '#FDFDFD',
                    'button_active_label' => '#FDFDFD',
                    'checkmark_color' => '#38C5B5',
                    'success_color' => '#FDFDFD',
                    'close_color' => '#CB4B40',
                    'nsa_color' => '#B03E34',
                    'overlay_background' => 'rgba(0,0,0,0.8)',
                    'close_hover_color' => '#CB4B40',
                    'close_active_color' => '#CB4B40',
                    'nsa_hover_color' => '#CB4B40',
                    'nsa_active_color' => '#CB4B40',
                    'radio_background' => '#FDFDFD',
                    'radio_checked_background' => '#CBB000',
                    'checkbox_background' => '#FDFDFD',
                    'checkbox_checked_color' => '#CBB000',
                    'mcg_title_color' => '#FDFDFD',
                    'mcg_label_color' => '#ADB5B7'
                ),
                'Midnight' => array(
                    'main_background' => '#25282B',
                    'title_color' => '#FDFDFD',
                    'link_color' => '#DD4F3D',
                    'content_color' => '#ADB5B7',
                    'link_hover_color' => '#C63D2B',
                    'form_background' => '#363B3F',
                    'fields_background' => '#FDFDFD',
                    'label_color' => '#ADB5B7',
                    'button_background' => '#DD4F3D',
                    'button_label' => '#FDFDFD',
                    'fields_color' => '#363B3F',
                    'fields_hover_color' => '#363B3F',
                    'fields_active_color' => '#363B3F',
                    'error_color' => '#BE1E2D',
                    'button_hover_background' => '#C63D2B',
                    'button_active_background' => '#C63D2B',
                    'button_hover_label' => '#FDFDFD',
                    'button_active_label' => '#FDFDFD',
                    'checkmark_color' => '#1ABC9C',
                    'success_color' => '#FDFDFD',
                    'close_color' => '#25282B',
                    'nsa_color' => '#25282B',
                    'overlay_background' => 'rgba(253,253,253,0.8)',
                    'close_hover_color' => '#25282B',
                    'close_active_color' => '#25282B',
                    'nsa_hover_color' => '#363b3F',
                    'nsa_active_color' => '#363b3F',
                    'radio_background' => '#FDFDFD',
                    'radio_checked_background' => '#DD4F3D',
                    'checkbox_background' => '#FDFDFD',
                    'checkbox_checked_color' => '#DD4F3D',
                    'mcg_title_color' => '#FDFDFD',
                    'mcg_label_color' => '#ADB5B7'
                )
            );
        }

        public static function get_client_ip() {

            if (getenv('HTTP_CLIENT_IP'))
                $ipaddress = getenv('HTTP_CLIENT_IP');
            else if(getenv('HTTP_X_FORWARDED_FOR'))
                $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
            else if(getenv('HTTP_X_FORWARDED'))
                $ipaddress = getenv('HTTP_X_FORWARDED');
            else if(getenv('HTTP_FORWARDED_FOR'))
                $ipaddress = getenv('HTTP_FORWARDED_FOR');
            else if(getenv('HTTP_FORWARDED'))
                $ipaddress = getenv('HTTP_FORWARDED');
            else if(getenv('REMOTE_ADDR'))
                $ipaddress = getenv('REMOTE_ADDR');
            else
                $ipaddress = 'UNKNOWN';
            return $ipaddress;
        }

        /**
         * Returns array of countries
         *
         * @return array|mixed|null|void
         */
        public function get_countries() {

            return apply_filters( 'opt_in-country-list', array(
                    'AU' => __( 'Australia', Opt_In::TEXT_DOMAIN ),
                    'AF' => __( 'Afghanistan', Opt_In::TEXT_DOMAIN ),
                    'AL' => __( 'Albania', Opt_In::TEXT_DOMAIN ),
                    'DZ' => __( 'Algeria', Opt_In::TEXT_DOMAIN ),
                    'AS' => __( 'American Samoa', Opt_In::TEXT_DOMAIN ),
                    'AD' => __( 'Andorra', Opt_In::TEXT_DOMAIN ),
                    'AO' => __( 'Angola', Opt_In::TEXT_DOMAIN ),
                    'AI' => __( 'Anguilla', Opt_In::TEXT_DOMAIN ),
                    'AQ' => __( 'Antarctica', Opt_In::TEXT_DOMAIN ),
                    'AG' => __( 'Antigua & Barbuda', Opt_In::TEXT_DOMAIN ),
                    'AR' => __( 'Argentina', Opt_In::TEXT_DOMAIN ),
                    'AM' => __( 'Armenia', Opt_In::TEXT_DOMAIN ),
                    'AW' => __( 'Aruba', Opt_In::TEXT_DOMAIN ),
                    'AT' => __( 'Austria', Opt_In::TEXT_DOMAIN ),
                    'AZ' => __( 'Azerbaijan', Opt_In::TEXT_DOMAIN ),
                    'BS' => __( 'Bahamas', Opt_In::TEXT_DOMAIN ),
                    'BH' => __( 'Bahrain', Opt_In::TEXT_DOMAIN ),
                    'BD' => __( 'Bangladesh', Opt_In::TEXT_DOMAIN ),
                    'BB' => __( 'Barbados', Opt_In::TEXT_DOMAIN ),
                    'BY' => __( 'Belarus', Opt_In::TEXT_DOMAIN ),
                    'BE' => __( 'Belgium', Opt_In::TEXT_DOMAIN ),
                    'BZ' => __( 'Belize', Opt_In::TEXT_DOMAIN ),
                    'BJ' => __( 'Benin', Opt_In::TEXT_DOMAIN ),
                    'BM' => __( 'Bermuda', Opt_In::TEXT_DOMAIN ),
                    'BT' => __( 'Bhutan', Opt_In::TEXT_DOMAIN ),
                    'BO' => __( 'Bolivia', Opt_In::TEXT_DOMAIN ),
                    'BA' => __( 'Bosnia/Hercegovina', Opt_In::TEXT_DOMAIN ),
                    'BW' => __( 'Botswana', Opt_In::TEXT_DOMAIN ),
                    'BV' => __( 'Bouvet Island', Opt_In::TEXT_DOMAIN ),
                    'BR' => __( 'Brazil', Opt_In::TEXT_DOMAIN ),
                    'IO' => __( 'British Indian Ocean Territory', Opt_In::TEXT_DOMAIN ),
                    'BN' => __( 'Brunei Darussalam', Opt_In::TEXT_DOMAIN ),
                    'BG' => __( 'Bulgaria', Opt_In::TEXT_DOMAIN ),
                    'BF' => __( 'Burkina Faso', Opt_In::TEXT_DOMAIN ),
                    'BI' => __( 'Burundi', Opt_In::TEXT_DOMAIN ),
                    'KH' => __( 'Cambodia', Opt_In::TEXT_DOMAIN ),
                    'CM' => __( 'Cameroon', Opt_In::TEXT_DOMAIN ),
                    'CA' => __( 'Canada', Opt_In::TEXT_DOMAIN ),
                    'CV' => __( 'Cape Verde', Opt_In::TEXT_DOMAIN ),
                    'KY' => __( 'Cayman Is', Opt_In::TEXT_DOMAIN ),
                    'CF' => __( 'Central African Republic', Opt_In::TEXT_DOMAIN ),
                    'TD' => __( 'Chad', Opt_In::TEXT_DOMAIN ),
                    'CL' => __( 'Chile', Opt_In::TEXT_DOMAIN ),
                    'CN' => __( 'China, People\'s Republic of', Opt_In::TEXT_DOMAIN ),
                    'CX' => __( 'Christmas Island', Opt_In::TEXT_DOMAIN ),
                    'CC' => __( 'Cocos Islands', Opt_In::TEXT_DOMAIN ),
                    'CO' => __( 'Colombia', Opt_In::TEXT_DOMAIN ),
                    'KM' => __( 'Comoros', Opt_In::TEXT_DOMAIN ),
                    'CG' => __( 'Congo', Opt_In::TEXT_DOMAIN ),
                    'CD' => __( 'Congo, Democratic Republic', Opt_In::TEXT_DOMAIN ),
                    'CK' => __( 'Cook Islands', Opt_In::TEXT_DOMAIN ),
                    'CR' => __( 'Costa Rica', Opt_In::TEXT_DOMAIN ),
                    'CI' => __( 'Cote d\'Ivoire', Opt_In::TEXT_DOMAIN ),
                    'HR' => __( 'Croatia', Opt_In::TEXT_DOMAIN ),
                    'CU' => __( 'Cuba', Opt_In::TEXT_DOMAIN ),
                    'CY' => __( 'Cyprus', Opt_In::TEXT_DOMAIN ),
                    'CZ' => __( 'Czech Republic', Opt_In::TEXT_DOMAIN ),
                    'DK' => __( 'Denmark', Opt_In::TEXT_DOMAIN ),
                    'DJ' => __( 'Djibouti', Opt_In::TEXT_DOMAIN ),
                    'DM' => __( 'Dominica', Opt_In::TEXT_DOMAIN ),
                    'DO' => __( 'Dominican Republic', Opt_In::TEXT_DOMAIN ),
                    'TP' => __( 'East Timor', Opt_In::TEXT_DOMAIN ),
                    'EC' => __( 'Ecuador', Opt_In::TEXT_DOMAIN ),
                    'EG' => __( 'Egypt', Opt_In::TEXT_DOMAIN ),
                    'SV' => __( 'El Salvador', Opt_In::TEXT_DOMAIN ),
                    'GQ' => __( 'Equatorial Guinea', Opt_In::TEXT_DOMAIN ),
                    'ER' => __( 'Eritrea', Opt_In::TEXT_DOMAIN ),
                    'EE' => __( 'Estonia', Opt_In::TEXT_DOMAIN ),
                    'ET' => __( 'Ethiopia', Opt_In::TEXT_DOMAIN ),
                    'FK' => __( 'Falkland Islands', Opt_In::TEXT_DOMAIN ),
                    'FO' => __( 'Faroe Islands', Opt_In::TEXT_DOMAIN ),
                    'FJ' => __( 'Fiji', Opt_In::TEXT_DOMAIN ),
                    'FI' => __( 'Finland', Opt_In::TEXT_DOMAIN ),
                    'FR' => __( 'France', Opt_In::TEXT_DOMAIN ),
                    'FX' => __( 'France, Metropolitan', Opt_In::TEXT_DOMAIN ),
                    'GF' => __( 'French Guiana', Opt_In::TEXT_DOMAIN ),
                    'PF' => __( 'French Polynesia', Opt_In::TEXT_DOMAIN ),
                    'TF' => __( 'French South Territories', Opt_In::TEXT_DOMAIN ),
                    'GA' => __( 'Gabon', Opt_In::TEXT_DOMAIN ),
                    'GM' => __( 'Gambia', Opt_In::TEXT_DOMAIN ),
                    'GE' => __( 'Georgia', Opt_In::TEXT_DOMAIN ),
                    'DE' => __( 'Germany', Opt_In::TEXT_DOMAIN ),
                    'GH' => __( 'Ghana', Opt_In::TEXT_DOMAIN ),
                    'GI' => __( 'Gibraltar', Opt_In::TEXT_DOMAIN ),
                    'GR' => __( 'Greece', Opt_In::TEXT_DOMAIN ),
                    'GL' => __( 'Greenland', Opt_In::TEXT_DOMAIN ),
                    'GD' => __( 'Grenada', Opt_In::TEXT_DOMAIN ),
                    'GP' => __( 'Guadeloupe', Opt_In::TEXT_DOMAIN ),
                    'GU' => __( 'Guam', Opt_In::TEXT_DOMAIN ),
                    'GT' => __( 'Guatemala', Opt_In::TEXT_DOMAIN ),
                    'GN' => __( 'Guinea', Opt_In::TEXT_DOMAIN ),
                    'GW' => __( 'Guinea-Bissau', Opt_In::TEXT_DOMAIN ),
                    'GY' => __( 'Guyana', Opt_In::TEXT_DOMAIN ),
                    'HT' => __( 'Haiti', Opt_In::TEXT_DOMAIN ),
                    'HM' => __( 'Heard Island And Mcdonald Island', Opt_In::TEXT_DOMAIN ),
                    'HN' => __( 'Honduras', Opt_In::TEXT_DOMAIN ),
                    'HK' => __( 'Hong Kong', Opt_In::TEXT_DOMAIN ),
                    'HU' => __( 'Hungary', Opt_In::TEXT_DOMAIN ),
                    'IS' => __( 'Iceland', Opt_In::TEXT_DOMAIN ),
                    'IN' => __( 'India', Opt_In::TEXT_DOMAIN ),
                    'ID' => __( 'Indonesia', Opt_In::TEXT_DOMAIN ),
                    'IR' => __( 'Iran', Opt_In::TEXT_DOMAIN ),
                    'IQ' => __( 'Iraq', Opt_In::TEXT_DOMAIN ),
                    'IE' => __( 'Ireland', Opt_In::TEXT_DOMAIN ),
                    'IL' => __( 'Israel', Opt_In::TEXT_DOMAIN ),
                    'IT' => __( 'Italy', Opt_In::TEXT_DOMAIN ),
                    'JM' => __( 'Jamaica', Opt_In::TEXT_DOMAIN ),
                    'JP' => __( 'Japan', Opt_In::TEXT_DOMAIN ),
                    'JT' => __( 'Johnston Island', Opt_In::TEXT_DOMAIN ),
                    'JO' => __( 'Jordan', Opt_In::TEXT_DOMAIN ),
                    'KZ' => __( 'Kazakhstan', Opt_In::TEXT_DOMAIN ),
                    'KE' => __( 'Kenya', Opt_In::TEXT_DOMAIN ),
                    'KI' => __( 'Kiribati', Opt_In::TEXT_DOMAIN ),
                    'KP' => __( 'Korea, Democratic Peoples Republic', Opt_In::TEXT_DOMAIN ),
                    'KR' => __( 'Korea, Republic of', Opt_In::TEXT_DOMAIN ),
                    'KW' => __( 'Kuwait', Opt_In::TEXT_DOMAIN ),
                    'KG' => __( 'Kyrgyzstan', Opt_In::TEXT_DOMAIN ),
                    'LA' => __( 'Lao People\'s Democratic Republic', Opt_In::TEXT_DOMAIN ),
                    'LV' => __( 'Latvia', Opt_In::TEXT_DOMAIN ),
                    'LB' => __( 'Lebanon', Opt_In::TEXT_DOMAIN ),
                    'LS' => __( 'Lesotho', Opt_In::TEXT_DOMAIN ),
                    'LR' => __( 'Liberia', Opt_In::TEXT_DOMAIN ),
                    'LY' => __( 'Libyan Arab Jamahiriya', Opt_In::TEXT_DOMAIN ),
                    'LI' => __( 'Liechtenstein', Opt_In::TEXT_DOMAIN ),
                    'LT' => __( 'Lithuania', Opt_In::TEXT_DOMAIN ),
                    'LU' => __( 'Luxembourg', Opt_In::TEXT_DOMAIN ),
                    'MO' => __( 'Macau', Opt_In::TEXT_DOMAIN ),
                    'MK' => __( 'Macedonia', Opt_In::TEXT_DOMAIN ),
                    'MG' => __( 'Madagascar', Opt_In::TEXT_DOMAIN ),
                    'MW' => __( 'Malawi', Opt_In::TEXT_DOMAIN ),
                    'MY' => __( 'Malaysia', Opt_In::TEXT_DOMAIN ),
                    'MV' => __( 'Maldives', Opt_In::TEXT_DOMAIN ),
                    'ML' => __( 'Mali', Opt_In::TEXT_DOMAIN ),
                    'MT' => __( 'Malta', Opt_In::TEXT_DOMAIN ),
                    'MH' => __( 'Marshall Islands', Opt_In::TEXT_DOMAIN ),
                    'MQ' => __( 'Martinique', Opt_In::TEXT_DOMAIN ),
                    'MR' => __( 'Mauritania', Opt_In::TEXT_DOMAIN ),
                    'MU' => __( 'Mauritius', Opt_In::TEXT_DOMAIN ),
                    'YT' => __( 'Mayotte', Opt_In::TEXT_DOMAIN ),
                    'MX' => __( 'Mexico', Opt_In::TEXT_DOMAIN ),
                    'FM' => __( 'Micronesia', Opt_In::TEXT_DOMAIN ),
                    'MD' => __( 'Moldavia', Opt_In::TEXT_DOMAIN ),
                    'MC' => __( 'Monaco', Opt_In::TEXT_DOMAIN ),
                    'MN' => __( 'Mongolia', Opt_In::TEXT_DOMAIN ),
                    'MS' => __( 'Montserrat', Opt_In::TEXT_DOMAIN ),
                    'MA' => __( 'Morocco', Opt_In::TEXT_DOMAIN ),
                    'MZ' => __( 'Mozambique', Opt_In::TEXT_DOMAIN ),
                    'MM' => __( 'Union Of Myanmar', Opt_In::TEXT_DOMAIN ),
                    'NA' => __( 'Namibia', Opt_In::TEXT_DOMAIN ),
                    'NR' => __( 'Nauru Island', Opt_In::TEXT_DOMAIN ),
                    'NP' => __( 'Nepal', Opt_In::TEXT_DOMAIN ),
                    'NL' => __( 'Netherlands', Opt_In::TEXT_DOMAIN ),
                    'AN' => __( 'Netherlands Antilles', Opt_In::TEXT_DOMAIN ),
                    'NC' => __( 'New Caledonia', Opt_In::TEXT_DOMAIN ),
                    'NZ' => __( 'New Zealand', Opt_In::TEXT_DOMAIN ),
                    'NI' => __( 'Nicaragua', Opt_In::TEXT_DOMAIN ),
                    'NE' => __( 'Niger', Opt_In::TEXT_DOMAIN ),
                    'NG' => __( 'Nigeria', Opt_In::TEXT_DOMAIN ),
                    'NU' => __( 'Niue', Opt_In::TEXT_DOMAIN ),
                    'NF' => __( 'Norfolk Island', Opt_In::TEXT_DOMAIN ),
                    'MP' => __( 'Mariana Islands, Northern', Opt_In::TEXT_DOMAIN ),
                    'NO' => __( 'Norway', Opt_In::TEXT_DOMAIN ),
                    'OM' => __( 'Oman', Opt_In::TEXT_DOMAIN ),
                    'PK' => __( 'Pakistan', Opt_In::TEXT_DOMAIN ),
                    'PW' => __( 'Palau Islands', Opt_In::TEXT_DOMAIN ),
                    'PS' => __( 'Palestine', Opt_In::TEXT_DOMAIN ),
                    'PA' => __( 'Panama', Opt_In::TEXT_DOMAIN ),
                    'PG' => __( 'Papua New Guinea', Opt_In::TEXT_DOMAIN ),
                    'PY' => __( 'Paraguay', Opt_In::TEXT_DOMAIN ),
                    'PE' => __( 'Peru', Opt_In::TEXT_DOMAIN ),
                    'PH' => __( 'Philippines', Opt_In::TEXT_DOMAIN ),
                    'PN' => __( 'Pitcairn', Opt_In::TEXT_DOMAIN ),
                    'PL' => __( 'Poland', Opt_In::TEXT_DOMAIN ),
                    'PT' => __( 'Portugal', Opt_In::TEXT_DOMAIN ),
                    'PR' => __( 'Puerto Rico', Opt_In::TEXT_DOMAIN ),
                    'QA' => __( 'Qatar', Opt_In::TEXT_DOMAIN ),
                    'RE' => __( 'Reunion Island', Opt_In::TEXT_DOMAIN ),
                    'RO' => __( 'Romania', Opt_In::TEXT_DOMAIN ),
                    'RU' => __( 'Russian Federation', Opt_In::TEXT_DOMAIN ),
                    'RW' => __( 'Rwanda', Opt_In::TEXT_DOMAIN ),
                    'WS' => __( 'Samoa', Opt_In::TEXT_DOMAIN ),
                    'SH' => __( 'St Helena', Opt_In::TEXT_DOMAIN ),
                    'KN' => __( 'St Kitts & Nevis', Opt_In::TEXT_DOMAIN ),
                    'LC' => __( 'St Lucia', Opt_In::TEXT_DOMAIN ),
                    'PM' => __( 'St Pierre & Miquelon', Opt_In::TEXT_DOMAIN ),
                    'VC' => __( 'St Vincent', Opt_In::TEXT_DOMAIN ),
                    'SM' => __( 'San Marino', Opt_In::TEXT_DOMAIN ),
                    'ST' => __( 'Sao Tome & Principe', Opt_In::TEXT_DOMAIN ),
                    'SA' => __( 'Saudi Arabia', Opt_In::TEXT_DOMAIN ),
                    'SN' => __( 'Senegal', Opt_In::TEXT_DOMAIN ),
                    'SC' => __( 'Seychelles', Opt_In::TEXT_DOMAIN ),
                    'SL' => __( 'Sierra Leone', Opt_In::TEXT_DOMAIN ),
                    'SG' => __( 'Singapore', Opt_In::TEXT_DOMAIN ),
                    'SK' => __( 'Slovakia', Opt_In::TEXT_DOMAIN ),
                    'SI' => __( 'Slovenia', Opt_In::TEXT_DOMAIN ),
                    'SB' => __( 'Solomon Islands', Opt_In::TEXT_DOMAIN ),
                    'SO' => __( 'Somalia', Opt_In::TEXT_DOMAIN ),
                    'ZA' => __( 'South Africa', Opt_In::TEXT_DOMAIN ),
                    'GS' => __( 'South Georgia and South Sandwich', Opt_In::TEXT_DOMAIN ),
                    'ES' => __( 'Spain', Opt_In::TEXT_DOMAIN ),
                    'LK' => __( 'Sri Lanka', Opt_In::TEXT_DOMAIN ),
                    'XX' => __( 'Stateless Persons', Opt_In::TEXT_DOMAIN ),
                    'SD' => __( 'Sudan', Opt_In::TEXT_DOMAIN ),
                    'SR' => __( 'Suriname', Opt_In::TEXT_DOMAIN ),
                    'SJ' => __( 'Svalbard and Jan Mayen', Opt_In::TEXT_DOMAIN ),
                    'SZ' => __( 'Swaziland', Opt_In::TEXT_DOMAIN ),
                    'SE' => __( 'Sweden', Opt_In::TEXT_DOMAIN ),
                    'CH' => __( 'Switzerland', Opt_In::TEXT_DOMAIN ),
                    'SY' => __( 'Syrian Arab Republic', Opt_In::TEXT_DOMAIN ),
                    'TW' => __( 'Taiwan, Republic of China', Opt_In::TEXT_DOMAIN ),
                    'TJ' => __( 'Tajikistan', Opt_In::TEXT_DOMAIN ),
                    'TZ' => __( 'Tanzania', Opt_In::TEXT_DOMAIN ),
                    'TH' => __( 'Thailand', Opt_In::TEXT_DOMAIN ),
                    'TL' => __( 'Timor Leste', Opt_In::TEXT_DOMAIN ),
                    'TG' => __( 'Togo', Opt_In::TEXT_DOMAIN ),
                    'TK' => __( 'Tokelau', Opt_In::TEXT_DOMAIN ),
                    'TO' => __( 'Tonga', Opt_In::TEXT_DOMAIN ),
                    'TT' => __( 'Trinidad & Tobago', Opt_In::TEXT_DOMAIN ),
                    'TN' => __( 'Tunisia', Opt_In::TEXT_DOMAIN ),
                    'TR' => __( 'Turkey', Opt_In::TEXT_DOMAIN ),
                    'TM' => __( 'Turkmenistan', Opt_In::TEXT_DOMAIN ),
                    'TC' => __( 'Turks And Caicos Islands', Opt_In::TEXT_DOMAIN ),
                    'TV' => __( 'Tuvalu', Opt_In::TEXT_DOMAIN ),
                    'UG' => __( 'Uganda', Opt_In::TEXT_DOMAIN ),
                    'UA' => __( 'Ukraine', Opt_In::TEXT_DOMAIN ),
                    'AE' => __( 'United Arab Emirates', Opt_In::TEXT_DOMAIN ),
                    'GB' => __( 'United Kingdom', Opt_In::TEXT_DOMAIN ),
                    'UM' => __( 'US Minor Outlying Islands', Opt_In::TEXT_DOMAIN ),
                    'US' => __( 'USA', Opt_In::TEXT_DOMAIN ),
                    'HV' => __( 'Upper Volta', Opt_In::TEXT_DOMAIN ),
                    'UY' => __( 'Uruguay', Opt_In::TEXT_DOMAIN ),
                    'UZ' => __( 'Uzbekistan', Opt_In::TEXT_DOMAIN ),
                    'VU' => __( 'Vanuatu', Opt_In::TEXT_DOMAIN ),
                    'VA' => __( 'Vatican City State', Opt_In::TEXT_DOMAIN ),
                    'VE' => __( 'Venezuela', Opt_In::TEXT_DOMAIN ),
                    'VN' => __( 'Vietnam', Opt_In::TEXT_DOMAIN ),
                    'VG' => __( 'Virgin Islands (British)', Opt_In::TEXT_DOMAIN ),
                    'VI' => __( 'Virgin Islands (US)', Opt_In::TEXT_DOMAIN ),
                    'WF' => __( 'Wallis And Futuna Islands', Opt_In::TEXT_DOMAIN ),
                    'EH' => __( 'Western Sahara', Opt_In::TEXT_DOMAIN ),
                    'YE' => __( 'Yemen Arab Rep.', Opt_In::TEXT_DOMAIN ),
                    'YD' => __( 'Yemen Democratic', Opt_In::TEXT_DOMAIN ),
                    'YU' => __( 'Yugoslavia', Opt_In::TEXT_DOMAIN ),
                    'ZR' => __( 'Zaire', Opt_In::TEXT_DOMAIN ),
                    'ZM' => __( 'Zambia', Opt_In::TEXT_DOMAIN ),
                    'ZW' => __( 'Zimbabwe', Opt_In::TEXT_DOMAIN )
                ));


        }
    }
}