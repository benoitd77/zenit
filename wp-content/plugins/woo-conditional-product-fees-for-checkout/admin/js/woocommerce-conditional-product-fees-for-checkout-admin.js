(function($) {
    $(window).load(function() {
        $("#wcpfc_free_dialog").dialog({
            modal: true, title: 'Subscribe To Our Newsletter', zIndex: 10000, autoOpen: true,
            width: '500', resizable: false,
            position: {my: "center", at: "center", of: window},
            dialogClass: 'dialogButtons',
            buttons: [
                {
                    id: "Delete",
                    text: "YES",
                    click: function() {
                        // $(obj).removeAttr('onclick');
                        // $(obj).parents('.Parent').remove();
                        var email_id = jQuery('#txt_user_sub_wcpfc_free').val();
                        var data = {
                            'action': 'wp_add_plugin_userfn_free_wcpfc',
                            'email_id': email_id
                        };
                        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                        jQuery.post(ajaxurl, data, function(response) {
                            jQuery('#wcpfc_free_dialog').html('<h2>You have been successfully subscribed');
                            jQuery(".ui-dialog-buttonpane").remove();
                        });
                    }
                },
                {
                    id: "No",
                    text: "No, Remind Me Later",
                    click: function() {

                        jQuery(this).dialog("close");
                    }
                },
            ]
        });
        jQuery("div.dialogButtons .ui-dialog-buttonset button").removeClass('ui-state-default');
        jQuery("div.dialogButtons .ui-dialog-buttonset button").addClass("button-primary woocommerce-save-button");

        jQuery(".multiselect2").chosen();
        $("#fee_settings_start_date").datepicker({
            dateFormat: 'dd-mm-yy',
            minDate: '0',
            onSelect: function(selected) {
                var dt = $(this).datepicker('getDate');
                dt.setDate(dt.getDate() + 1);
                $("#fee_settings_end_date").datepicker("option", "minDate", dt);
            }
        });
        $("#fee_settings_end_date").datepicker({
            dateFormat: 'dd-mm-yy',
            minDate: '0',
            onSelect: function(selected) {
                var dt = $(this).datepicker('getDate');
                dt.setDate(dt.getDate() - 1);
                $("#fee_settings_start_date").datepicker("option", "maxDate", dt);
            }
        });
        var ele = $('#total_row').val();
        if (ele > 2) {
            var count = ele;
        } else {
            var count = 2;
        }
        $('body').on('click', '#fee-add-field', function() {
            var tds = '<tr id=row_' + count + '>';
            tds += '<td><select rel-id=' + count + ' id=product_fees_conditions_condition_' + count + ' name="fees[product_fees_conditions_condition][]" class="product_fees_conditions_condition"><optgroup label="Location Specific"><option value="country">Country</option><option value="state" disabled>State (Available in Pro Version)</option><option value="postcode"disabled>Postcode (Available in Pro Version)</option><option value="zone" disabled>Zone (Available in Pro Version)</option></optgroup><optgroup label="Product Specific"><option value="product">Product</option><option value="category" disabled>Category (Available in Pro Version)</option><option value="tag" disabled>Tag (Available in Pro Version)</option></optgroup><optgroup label="User Specific (Available in Pro Version)" disabled><option value="user">User</option><option value="user_role">User Role</option></optgroup><optgroup label="Cart Specific (Available in Pro Version)" disabled><option value="cart_total">Cart Subtotal (Before Discount)</option><option value="cart_totalafter">Cart Subtotal (After Discount)</option><option value="quantity">Quantity</option><option value="weight">Weight</option><option value="coupon">Coupon</option><option value="shipping_class">Shipping Class</option></optgroup><optgroup label="Payment Specific (Available in Pro Version)" disabled><option value="payment">Payment Gateway</option></optgroup><optgroup label="Shipping Specific (Available in Pro Version)" disabled><option value="shipping_method">Shipping Method</option></optgroup></select></td>';
            tds += '<td><select name="fees[product_fees_conditions_is][]" class="product_fees_conditions_is product_fees_conditions_is_' + count + '"><option value="is_equal_to">Equal to ( = )</option><option value="not_in">Not Equal to ( != )</option></select></td>';
            tds += '<td id=column_' + count + '><select name="fees[product_fees_conditions_values][value_' + count + '][]" class="product_fees_conditions_values product_fees_conditions_values_' + count + ' multiselect2" multiple="multiple"></select><input type="hidden" name="condition_key[value_' + count + '][]" value=""></td>';
            tds += '<td><a id="fee-delete-field" rel-id="' + count + '" title="Delete" class="delete-row" href="javascript:;"><i class="fa fa-trash"></i></a></td>';
            tds += '</tr>';
            $('#tbl-product-fee').append(tds);
            jQuery(".product_fees_conditions_values_" + count).append(jQuery(".default-country-box select").html());
            jQuery(".product_fees_conditions_values_" + count).trigger("chosen:updated");
            jQuery(".multiselect2").chosen();
            count++;
        });
        $('body').on('click', '#fee-delete-field', function() {
            var deleId = $(this).attr('rel-id');
            $("#row_" + deleId).remove();

        });

        $('body').on('change', '.product_fees_conditions_condition', function() {
            var condition = $(this).val();
            var count = $(this).attr('rel-id');
            $('#column_' + count).html('<img src="' + coditional_vars.plugin_url + 'images/ajax-loader.gif">');
            var data = {
                'action': 'product_fees_conditions_values_ajax',
                'condition': condition,
                'count': count
            };

            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            jQuery.post(ajaxurl, data, function(response) {
                if (condition == 'cart_total' || condition == 'cart_totalafter' || condition == 'quantity' || condition == 'weight') {
                    jQuery('.product_fees_conditions_is_' + count).html('');
                    jQuery('.product_fees_conditions_is_' + count).append(jQuery(".text-condtion-is select.text-condition").html());
                    jQuery('.product_fees_conditions_is_' + count).trigger("chosen:updated");
                } else {
                    jQuery('.product_fees_conditions_is_' + count).html('');
                    jQuery('.product_fees_conditions_is_' + count).append(jQuery(".text-condtion-is select.select-condition").html());
                    jQuery('.product_fees_conditions_is_' + count).trigger("chosen:updated");
                }
                $('#column_' + count).html('');
                $('#column_' + count).append(response);
                $('#column_' + count).append('<input type="hidden" name="condition_key[value_' + count + '][]" value="">');
                jQuery(".multiselect2").chosen();
                if (condition == 'product') {
                    $('#product_filter_chosen input').val('Please enter 3 or more characters');
                }

            });
        });
        $('body').on('keyup', '#product_filter_chosen input', function() {
            countId = $(this).closest("td").attr('id');
            $('#product_filter_chosen ul li.no-results').html('Please enter 3 or more characters');
            var value = $(this).val();
            var valueLenght = value.replace(/\s+/g, '');
            var valueCount = valueLenght.length;
            var remainCount = 3 - valueCount;
            if (valueCount >= 3) {
                $('#product_filter_chosen ul li.no-results').html('<img src="' + coditional_vars.plugin_url + 'images/ajax-loader.gif">');
                var data = {
                    'action': 'product_fees_conditions_values_product_ajax',
                    'value': value
                };
                jQuery.post(ajaxurl, data, function(response) {
                    $('#' + countId + ' #product-filter').append(response);
                    $('#' + countId + ' #product-filter option').each(function() {
                        $(this).siblings("[value='" + this.value + "']").remove();
                    });
                    jQuery('#' + countId + ' #product-filter').trigger("chosen:updated");
                    $('#' + countId + ' #product-filter').chosen().change(function() {
                        var productVal = $('#' + countId + ' #product-filter').chosen().val();
                        jQuery('#' + countId + ' #product-filter option').each(function() {
                            $(this).siblings("[value='" + this.value + "']").remove();
                            if (jQuery.inArray(this.value, productVal) == -1) {
                                jQuery(this).remove();
                            }
                        });
                        jQuery('#' + countId + ' #product-filter').trigger("chosen:updated");
                    });
                    $('#product_filter_chosen ul li.no-results').html('');
                });
            } else {
                if (remainCount > 0) {
                    $('#product_filter_chosen ul li.no-results').html('Please enter ' + remainCount + ' or more characters');
                }
            }
        });
        $(".condition-check-all").click(function() {
            $('input.multiple_delete_fee:checkbox').not(this).prop('checked', this.checked);
        });
        $('#detete-conditional-fee').click(function() {
            if ($('.multiple_delete_fee:checkbox:checked').length == 0) {
                alert('Please select at least one checkbox');
                return false;
            }
            if (confirm('Are You Sure You Want to Delete?')) {
                var allVals = [];
                $(".multiple_delete_fee:checked").each(function() {
                    allVals.push($(this).val());
                });
                var data = {
                    'action': 'wc_multiple_delete_conditional_fee',
                    'allVals': allVals
                };

                // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                jQuery.post(ajaxurl, data, function(response) {
                    if (response == 1) {
                        alert('Delete Successfully');
                        $(".multiple_delete_fee").prop("checked", false);
                        location.reload();
                    }
                });
            }
        });

        /* description toggle */
        $('span.woocommerce_conditional_product_fees_checkout_tab_descirtion').click(function(event) {
            event.preventDefault();
            var data = $(this);
            $(this).next('p.description').toggle();
            //$('span.advance_extra_flate_rate_disctiption_tab').next('p.description').toggle();
        });
    });

    jQuery(document).ready(function($) {
        $(".tablesorter").tablesorter({
            headers: {
                0: {
                    sorter: false
                },
                4: {
                    sorter: false
                }
            }
        });
        var fixHelperModified = function(e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function(index)
            {
                $(this).width($originals.eq(index).width())
            });
            return $helper;
        };
        //Make diagnosis table sortable
        $("table#conditional-fee-listing tbody").sortable({
            helper: fixHelperModified,
        });
        $("table#conditional-fee-listing tbody").disableSelection();
    });
})(jQuery);
//subscribe