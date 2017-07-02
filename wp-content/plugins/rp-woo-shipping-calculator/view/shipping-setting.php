<div class="clear"></div>
<style type="text/css">
    .rp_table tr td{padding-bottom: 15px;}
    .input_field{width:150px;}
    label.px{font-size: 12px;font-style: italic;font-weight: bold;text-transform: lowercase;}
    .rp_table tr td.setting{  border-bottom: 2px solid #000;    color: #000;    font-size: 15px;    font-weight: bold;    padding-bottom: 5px;}
</style>
<div class="postbox " id="dashboard_right_now" style="width:90%;margin-top:20px;">
    <h3 class="hndle" style="margin: 0px;padding: 10px 15px;"><?php echo __('Shipping Calculator Settings', 'rpship') ?></h3>
    <div class="inside">
        <div class="main">
            <form method="post" action="" name="<?php echo self::$plugin_slug; ?>">
                <input type="hidden" name="<?php echo self::$plugin_slug; ?>" value="1"/>
                <table class="rp_table" width="100%;">
                    <tr>
                        <td width="30%"><?php echo __('Enable on product page?', 'rpship') ?></td>
                        <td>
                            <input type="checkbox" name="enable_on_productpage" <?php echo ($this->get_setting("enable_on_productpage")) ? "checked=checked" : ""; ?> value="1" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo __('Button Position On Product Page', 'rpship') ?></td>
                        <td>
                            <select name="button_pos">
                                <option value="0" <?php echo ($this->get_setting("button_pos") == 0) ? "selected=selected" : ""; ?>>Below  Title</option>
                                <option value="1" <?php echo ($this->get_setting("button_pos") == 1) ? "selected=selected" : ""; ?>>After Description</option>
                                <option value="2" <?php echo ($this->get_setting("button_pos") == 2) ? "selected=selected" : ""; ?>>After Price</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo __('Shipping Method Input', 'rpship') ?></td>
                        <td>
                            <select name="shipping_type">
                                <option value="0" <?php echo ($this->get_setting("shipping_type") == 0) ? "selected=selected" : ""; ?>>Select</option>
                                <option value="1" <?php echo ($this->get_setting("shipping_type") == 1) ? "selected=selected" : ""; ?>>Radio</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo __('Display Shipping Message', 'rpship') ?></td>
                        <td>
                            <select name="display_message">
                                <option value="0" <?php echo ($this->get_setting("display_message") == 0) ? "selected=selected" : ""; ?>>Before Shipping Form</option>
                                <option value="1" <?php echo ($this->get_setting("display_message") == 1) ? "selected=selected" : ""; ?>>After Shipping Form</option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <td width="30%"><?php echo __('Default open shipping calculator?', 'rpship') ?></td>
                        <td>
                            <input type="checkbox" name="default_open" <?php echo ($this->get_setting("default_open")) ? "checked=checked" : ""; ?> value="1" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo __('Max width of calculator', 'rpship') ?></td>
                        <td>
                            <input type="number" class="input_field"  name="max_width" value="<?php echo $this->get_setting("max_width") ?>"/> <label class="px">PX</label>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo __('Button Text', 'rpship') ?></td>
                        <td>
                            <input type="text" class="input_field" name="button_text" value="<?php echo $this->get_setting("button_text") ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>Shipping Message</td>
                        <td>
                            <textarea name="shipping_message" cols="35" rows="5"><?php echo $this->get_setting("shipping_message"); ?></textarea>
                            <br>
                            You can use <strong>[shipping-method]</strong>, <strong>[shipping-cost]</strong> and <strong>[shipping-country]</strong> <br>for display Shipping Method,Shipping Cost and Shipping Country in Shipping Calculation Message.
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo __('Button Align', 'rpship') ?></td>
                        <td>
                            <select name="button_align">
                                <option value="0" <?php echo ($this->get_setting("button_align") == 0) ? "selected=selected" : ""; ?>><?php echo __('Left', 'rpship') ?></option>
                                <option value="1" <?php echo ($this->get_setting("button_align") == 1) ? "selected=selected" : ""; ?>><?php echo __('Right', 'rpship') ?></option>
                                <option value="2" <?php echo ($this->get_setting("button_align") == 2) ? "selected=selected" : ""; ?>><?php echo __('Center', 'rpship') ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo __('Button Border Size', 'rpship') ?></td>
                        <td>
                            <input type="number" class="input_field"  name="button_border_size" value="<?php echo $this->get_setting("button_border_size") ?>"/> <label class="px">PX</label>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo __('Button Border Color', 'rpship') ?></td>
                        <td>
                            <input type="text" class="color_picker" name="button_border_color" value="<?php echo $this->get_setting("button_border_color") ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo __('Button Border Radius', 'rpship') ?></td>
                        <td>
                            <input type="number" class="input_field"  name="button_border_radius" value="<?php echo $this->get_setting("button_border_radius") ?>"/> <label class="px">PX</label>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo __('Button Text Color', 'rpship') ?></td>
                        <td>
                            <input type="text"  class="color_picker" name="button_text_color" value="<?php echo $this->get_setting("button_text_color") ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo __('Custom Css For Calculator', 'rpship') ?></td>
                        <td>
                            <textarea name="custom_css"><?php echo $this->get_setting('custom_css') ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo __('Button Background Color', 'rpship') ?></td>
                        <td>
                            <input type="text" class="color_picker" name="button_bg_color" value="<?php echo $this->get_setting("button_bg_color") ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>
                            <input type="submit" class="button button-primary" name="btn-rpship-submit" value="<?php echo __("Save Settings", "rpship") ?>" />
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<div class="clear"></div>
<div class="postbox " id="dashboard_right_now" style="width:90%;margin-top:20px;">
    <h3 class="hndle" style="margin: 0px;padding: 10px 15px;">Shipping Calculator Shortcode</h3>
    <div class="inside">
        <div class="main">
            <table>
                <tr>
                    <td>
                        <textarea cols="70" rows="2">[shipping-calculator]</textarea>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>