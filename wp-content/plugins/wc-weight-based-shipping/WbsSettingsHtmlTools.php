<?php
class WbsSettingsHtmlTools
{
    public function __construct(WC_Weight_Based_Shipping $settings)
    {
        $this->settings = $settings;
    }

    public function generateRangeHtml($key, array $data = array())
    {
        $field = $this->settings->getPostKey($key);

        $range = $this->settings->get_option($key);
        if ($range === '' || $range === false) {
            $range = null;
        }

        $data['wbs_range_type'] = @$data['wbs_range_type'] !== 'simple' ? 'default' : $data['wbs_range_type'];
        $simple = @$data['wbs_range_type'] == 'simple';

        return $this->formRow('
            <label class="wbs-minifield-container">
                <span class="wbs-minifield-label">' . ($simple ? 'minimum' : 'above') . '</span>
                ' . $this->rangeInput('min', @$range['min'], true, $simple, $field) . '
            </label>
            <label class="wbs-minifield-container">
                <span class="wbs-minifield-label">' . ($simple ? 'maximum' : 'below') . '</span>
                ' . $this->rangeInput('max', @$range['max'], false, $simple, $field) . '
            </label>
            ',
            "{$field}_min",
            array('wbs-range', "wbs-range-{$data['wbs_range_type']}"),
            $data
        );
    }

    public function validateRangeHtml($key)
    {
        $range = array();

        $input = @$_POST[$this->settings->getPostKey($key)];
        foreach(array('min', 'max') as $limit) {
            $range[$limit]['value'] = $this->receiveDecimal(@$input[$limit]['value']);
            $range[$limit]['inclusive'] = (bool)(int)@$input[$limit]['inclusive'];
        }

        if (isset($range['min']['value']) && isset($range['max']['value'])) {
            if ($range['min']['value'] > $range['max']['value']) {
                $tmp = $range['max']['value'];
                $range['max']['value'] = $range['min']['value'];
                $range['min']['value'] = $tmp;
            } else if ($range['min']['value'] === $range['max']['value']) {
                $range['min']['inclusive'] = $range['max']['inclusive'] = true;
            }
        }

        return $range;
    }

    public function generateWeightRateHtml($key, array $data = array())
    {
        $inputNamePrefix = $this->settings->getPostKey($key);
        $id = "{$inputNamePrefix}_cost";
        $value = $this->settings->get_option($key);

        return $this->formRow(
            $this->weightRate($inputNamePrefix, $value),
            $id, 'wbs-weight-rate', $data
        );
    }

    public function validateWeightRateHtml($key)
    {
        return $this->receiveWeightRate(@$_POST[$this->settings->getPostKey($key)]);
    }

    public function generateShippingClassesHtml($key, $data)
    {
        $prefix = $this->settings->getPostKey($key);

        $data = wp_parse_args($data, array(
            'title'             => '',
            'desc_tip'          => '',
            'description'       => '',
        ));

        $rates = $this->settings->get_option($key);
        if (!$rates) {
            $rates = new WbsBucketRates();
        }

        $tableId = "{$this->settings->id}_flat_rates";

        ob_start();
        ?>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <?php echo wp_kses_post($data['title']); ?>
                    <?php echo $this->settings->get_description_html($data); ?>
                </th>
                <td class="forminp" id="<?php echo esc_html($tableId); ?>">
                    <table class="shippingrows widefat" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="check-column"><input type="checkbox"></th>
                                <th class="shipping_class"><?php _e('Shipping Class', 'woowbs'); ?></th>
                                <th><?php _e('Additional Cost', 'woowbs'); ?></th>
                                <th><?php _e('Weight Rate', 'woowbs'); ?></th>
                            </tr>
                        </thead>
                        <tbody class="flat_rates">
                            <?php
                                foreach (array_values($rates->listAll()) as $i => $rate) {
                                    echo $this->shippingClassRateRow($prefix, $i, $rate);
                                }

                                echo $this->shippingClassRateRow($prefix, '{{{rate_id}}}', null, 'flat_rate_tpl');
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5">
                                    <a href="#" class="add button"><?php _e('Add', 'woowbs'); ?></a>
                                    <a href="#" class="remove button"><?php _e('Delete selected costs', 'woowbs'); ?></a>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                    <script type="text/javascript">
                        jQuery(function($) {

                            var $table = $("#<?php echo $tableId ?>");
                            var $templateRow = $table.find('.flat_rate_tpl');

                            $table

                                .on('click', '.add', function() {

                                    var rateCount = $table.find('.flat_rate').size();
                                    var newRateHtml = $('<div/>').append($templateRow.clone()).html().replace(/\{\{\{rate_id}}}/g, rateCount);
                                    $(newRateHtml).insertBefore($templateRow).removeClass('flat_rate_tpl');

                                    return false;
                                })

                                .on('click', '.remove', function() {

                                    var answer = confirm("<?php _e('Delete the selected rates?', 'woowbs'); ?>");

                                    if (answer) {
                                        $table.find('.check-column input:checked').each(function(i, el) {
                                            $(el).closest('.flat_rate:not(.flat_rate_tpl)').remove();
                                        });
                                    }

                                    return false;
                                })

                                .closest('form').on('submit', function() {
                                    $templateRow.remove();
                                })
                            ;
                        });
                    </script>
                </td>
            </tr>
        <?php
        return ob_get_clean();
    }

    public function validateShippingClasses($key)
    {
        $rates = new WbsBucketRates();

        $rows = (array)@$_POST[$this->settings->getPostKey($key)];
        foreach ($rows as $i => $row) {
            $rates->add(new WbsBucketRate(
                @$row['class'],
                $this->receiveDecimal(@$row['fee']),
                WbsProgressiveRate::fromArray($this->receiveWeightRate(@$row['weight_rate']))
            ));
        }

        return $rates;
    }

    private $settings;

    private function shippingClassSelect($name, $value = null)
    {
        $html = '<select name="' . esc_attr($name) . '" class="select">';

        if ($classes = WC()->shipping->get_shipping_classes()) {
            foreach ($classes as $shipping_class) {
                $html .=
                    '<option value="' . esc_attr($shipping_class->slug) . '" ' . selected($shipping_class->slug, $value, false) . '>' .
                        $shipping_class->name .
                    '</option>';
            }
        } else {
            $html .= '<option value="">' . __('Select a class&hellip;', 'woowbs') . '</option>';
        }

        return $html;
    }

    private function shippingClassRateRow($inputNamePrefix, $rateId, WbsBucketRate $rate = null, $class = null)
    {
        return '
            <tr class="flat_rate ' . esc_html($class) . '">
                <th class="check-column">
                    <input type="checkbox" name="select" />
                </th>
                <td class="flat_rate_class">
                    ' . $this->shippingClassSelect(
                        "{$inputNamePrefix}[{$rateId}][class]",
                        $rate ? $rate->getId() : null
                    ) . '
                </td>
                <td>' .
                    $this->decimalInput(array(
                        'value' => $rate ? $rate->getFlatRate() : null,
                        'name' => "{$inputNamePrefix}[{$rateId}][fee]"
                    )) . '
                </td>
                <td>' .
                    $this->weightRate(
                        "{$inputNamePrefix}[{$rateId}][weight_rate]",
                        $rate ? $rate->getProgressiveRate()->toArray() : null,
                        false
                    ) . '
                </td>
            </tr>
        ';
    }

    private function weightRate($inputNamePrefix, array $value = null, $placeholders = true)
    {
        $id = "{$inputNamePrefix}_cost";

        $weightUnit = get_option('woocommerce_weight_unit');

        $fields = array(
            array(
                'id' => $id,
                'name' => 'cost',
                'label' => 'charge',
                'placeholder' => sprintf(__('e.g. %s'), strip_tags(wc_price(2.5))),
                'decorator' => get_woocommerce_currency_symbol(),
                'decorator_left' => substr(get_option('woocommerce_currency_pos'), 0, 1) === 'l',
            ),
            array(
                'id' => null,
                'name' => 'step',
                'label' => 'per each',
                'placeholder' => sprintf(__('e.g. %s %s'), wc_format_localized_decimal(0.5), $weightUnit),
                'decorator' => $weightUnit,
                'decorator_left' => false,
            ),
            array(
                'id' => null,
                'name' => 'skip',
                'label' => 'over',
                'placeholder' => sprintf(__('e.g. %s %s'), wc_format_localized_decimal(3), $weightUnit),
                'decorator' => $weightUnit,
                'decorator_left' => false,
            )
        );

        $html = '';

        foreach ($fields as $field) {

            $decorator = '<span class="wbs-input-group-addon">' . esc_html($field['decorator']) . '</span>';

            $html .= '
                <label class="wbs-minifield-container">
                    <span class="wbs-minifield-label">' . esc_html($field['label']) . '</span>
                    <div class="wbs-input-group">
                        ' . ($field['decorator_left'] ? $decorator : null) . '
                        ' . $this->decimalInput(array(
                                'id'            => $field['id'],
                                'class'         => 'wbs-minifield',
                                'name'          => "{$inputNamePrefix}[{$field['name']}]",
                                'value'         => (float)($v = @$value[$field['name']]) === 0.0 ? null : $v,
                                'placeholder'   => $placeholders ? $field['placeholder'] : null,
                            )) . '
                        ' . ($field['decorator_left'] ? null : $decorator) . '
                    </div>
                </label>
            ';
        }

        return $html;
    }

    private function rangeInput($name, $current, $defaultInclusive, $simple, $field)
    {
        $html = $this->decimalInput(array(
            'id'            => "{$field}_{$name}",
            'name'          => "{$field}[{$name}][value]",
            'class'         => "wbs-minifield",
            'value'         => @$current['value'],
            'placeholder'   => $simple ? esc_html($name) : null,
        ));

        if (!$simple) {
            $html .= '
                 <label> ' .
                    $this->input(array(
                        'type'    => 'checkbox',
                        'name'    =>"{$field}[{$name}][inclusive]",
                        'value'   => 1,
                        'checked' => isset($current['inclusive']) ? $current['inclusive'] : $defaultInclusive,
                    )) .
                    ' or equal
                </label>
            ';
        }

        return $html;
    }

    private function input(array $attrs = array())
    {
        $attrs += array(
            'type' => 'text',
        );

        $html = '<input';

        foreach ($attrs as $name => $value) {

            if (!isset($value)) {
                continue;
            }

            if (is_bool($value)) {

                if ($value) {
                    $html .= ' '.esc_html($name);
                }

                continue;
            }

            if (is_array($value)) {
                $value = join(' ', $value);
            }

            $html .= ' ' . esc_html($name) . '="' . esc_html($value) . '"';
        }

        $html .= '>';

        return $html;
    }

    private function decimalInput(array $attrs = array())
    {
        if (isset($attrs['value']) && (string)$attrs['value'] !== '') {
            $attrs['value'] = wc_format_localized_decimal($attrs['value']);
        }

        $attrs['class'] = (array)@$attrs['class'];
        $attrs['class'][] = 'wc_input_decimal input-text';

        return $this->input($attrs);
    }

    private function formRow($innerHtml, $fieldId, $classes, $data)
    {
        if (is_array($classes)) {
            $classes = join(' ', $classes);
        }

        $ksesTitle = wp_kses_post(@$data['title']);

        $data += array(
            'desc_tip'    => false,
            'description' => '',
        );

        ob_start();
            ?>
                <tr valign="top" class="<?php echo esc_html($classes) ?>">
                    <th scope="row" class="titledesc">
            <?php if ($ksesTitle): ?>
                            <label for="<?php echo esc_attr($fieldId) ?>"><?php echo $ksesTitle ?></label>
            <?php endif; ?>
                        <?php echo $this->settings->get_tooltip_html($data); ?>
                    </th>
                    <td class="forminp">
                        <fieldset>
            <?php if ($ksesTitle): ?>
                            <legend class="screen-reader-text">
                                <span><?php echo $ksesTitle; ?></span>
                            </legend>
            <?php endif; ?>
                            <?php echo $innerHtml ?>
                            <?php echo $this->settings->get_description_html($data); ?>
                        </fieldset>
                    </td>
                </tr>
            <?php
        return ob_get_clean();
    }

    private function receiveString($value)
    {
        if (isset($value)) {

            $value = stripslashes(trim($value));

            if ((string)$value === '') {
                $value = null;
            }
        }

        return $value;
    }

    private function receiveDecimal($value, $defaultValue = null)
    {
        $value = $this->receiveString($value);

        $value = isset($value) ? wc_format_decimal($value) : $defaultValue;

        if (isset($value) && !is_numeric($value)) {
            $this->settings->errors[] = "'{$value}' is not a valid decimal value";
            $value = null;
        }

        return $value;
    }


    private function receiveWeightRate($input)
    {
        $input = (array)$input;

        return array(
            'cost' => $this->receiveDecimal(@$input['cost']),
            'step' => $this->receiveDecimal(@$input['step']),
            'skip' => $this->receiveDecimal(@$input['skip']),
        );
    }
}