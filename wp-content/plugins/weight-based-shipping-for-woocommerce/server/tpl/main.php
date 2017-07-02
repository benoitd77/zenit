</form>

<style>
    #mainform > h2 {
        display: none;
    }
</style>

<!--suppress JSUnresolvedVariable -->
<script>
    jQuery(function() {
        if (!wbs_js_data.isGlobalInstance) {
            jQuery('#mainform > h2').show();
        }
    });
</script>

<div class="woocommerce">
    <app></app>
</div>

<form style="display: none">