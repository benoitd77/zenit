<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class-wad-products-list
 *
 * @author HL
 */
class WAD_Products_List {

    public $id;
    private $args;
    private $products;
    private $last_fetch;

    public function __construct($list_id) {
        if ($list_id)
        {
            $this->id = $list_id;
            $this->args=  get_post_meta($list_id, "o-list", true);
            $this->products=false;
            $this->last_fetch=false;
//            $this->args=  $this->get_args($raw_args);
        }
    }

    /**
     * Register the list custom post type
     */
    public function register_cpt_list() {

        $labels = array(
            'name' => __('List', 'wad'),
            'singular_name' => __('List', 'wad'),
            'add_new' => __('New list', 'wad'),
            'add_new_item' => __('New list', 'wad'),
            'edit_item' => __('Edit list', 'wad'),
            'new_item' => __('New list', 'wad'),
            'view_item' => __('View list', 'wad'),
            //        'search_items' => __('Search a group', 'wad'),
            'not_found' => __('No list found', 'wad'),
            'not_found_in_trash' => __('No list in the trash', 'wad'),
            'menu_name' => __('Lists', 'wad'),
        );

        $args = array(
            'labels' => $labels,
            'hierarchical' => false,
            'description' => 'Lists',
            'supports' => array('title'),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_in_nav_menus' => false,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'has_archive' => false,
            'query_var' => false,
            'can_export' => true,
//            'menu_icon' => 'dashicons-schedule',
        );

        register_post_type('o-list', $args);
    }

    /**
     * Adds the metabox for the list CPT
     */
    public function get_list_metabox() {

        $screens = array('o-list');

        foreach ($screens as $screen) {

            add_meta_box(
                    'o-list-settings-box', __('List settings', 'wad'), array($this, 'get_list_settings_page'), $screen
            );
        }
    }

    /**
     * List CPT metabox callback
     */
    public function get_list_settings_page() {
        ?>
                <div class='block-form'>
            <?php
            
            $begin = array(
                'type' => 'sectionbegin',
                'id' => 'wad-datasource-container'
                    );
            
            $extraction_type = array(
                'title' => __('Extraction type', 'wad'),
                'name' => 'o-list[type]',
                'type' => 'radio',
                'class'=> 'o-list-extraction-type',
                'default' => 'by-id',
                'desc' => __('How would you like to specify which products you want to include in the list?', 'wad'),
                'options' => array(
                    "by-id" => "By ID",
                    "custom-request" => "Dynamic request",
                )
            );
            
            $list_id = get_the_ID();
            $metas = get_post_meta($list_id, "o-list", true);
            $action_meta = get_proper_value($metas, "type", "by-id");
            if ($action_meta == "by-id") {
                $custom_request_css = "display:none;";
                $by_id_css = "";
            } else {
                $by_id_css = "display:none;";
                $custom_request_css = "";
            }
            
            $ids_list= array(
                'title' => __('Products IDs', 'wad'),
                'desc' => __('Values separated by commas', 'wad'),
                'name'=>'o-list[ids]',
                'row_class'=>'extract-by-id-row',
                'row_css'=> $by_id_css,
                'type' => 'text',
                'default' => '',
            );
            
            $author= array(
                'title' => __('Author', 'wad'),
                'desc' => __("Retrieves only the elements created by the specified authors", "wad"),
                'name'=>'o-list[author__in]',
                'row_class'=>'extract-by-custom-request-row',
                'row_css'=>$custom_request_css,
                'type' => 'multiselect',
                'default' => '',
                'options' => $this->get_authors(),
            );
            
            $exclude= array(
                'title' => __('Exclude', 'wad'),
                'desc' => __('Excludes the following elements IDs from the results (values separated by commas)', 'wad'),
                'name'=>'o-list[post__not_in]',
                'row_class'=>'extract-by-custom-request-row',
                'row_css'=>$custom_request_css,
                'type' => 'text',
                'default' => '',
            );
            
            $metas_relationship= array(
                'title' => __('Metas relationship', 'wad'),
                'name'=>'o-list[meta_query][relation]',
                'row_class'=>'extract-by-custom-request-row',
                'row_css'=>$custom_request_css,
                'type' => 'select',
                'default' => '',
                'options' => array(
                    "AND"=> 'AND',
                    "OR"=> 'OR'
                    )
            );
            
            $meta_filter_key= array(
                'title' => __('Key', 'wad'),
                'name'=>'key',
                'type' => 'text',
                'default' => '',
            );
            
            $meta_filter_compare= array(
                'title' => __('Operator', 'wad'),
                'tip' => __("If the operator  is 'IN', 'NOT IN', 'BETWEEN', or 'NOT BETWEEN', make sure the different values are separated by a comma", "wad"),
                'name'=>'compare',
                'type' => 'select',
                'options'=> array(
                    "="=>"EQUALS",
                    "!="=>"NOT EQUALS",
                    ">"=>"MORE THAN",
                    ">="=>"MORE OR EQUALS",
                    "<"=>"LESS THAN",
                    "<="=>"LESS OR EQUALS",
                    "LIKE"=>"LIKE",
                    "NOT LIKE"=>"NOT LIKE",
                    "IN"=>"IN",
                    "NOT IN"=>"NOT IN",
                    "BETWEEN"=>"BETWEEN",
                    "NOT BETWEEN"=>"NOT BETWEEN",
                    "NOT EXISTS"=>"NOT EXISTS",
                    "REGEXP"=>"REGEXP",
                    "NOT REGEXP"=>"NOT REGEXP",
                    "RLIKE"=>"RLIKE",
                )
            );
            
            $meta_filter_value= array(
                'title' => __('Value', 'wad'),
                'name'=>'value',
                'type' => 'text',
                'default' => '',
            );
            
            $meta_filter_type= array(
                'title' => __('Type', 'wad'),
                'name'=>'type',
                'type' => 'select',
                'options'=> array(
                    ""=>"Undefined",
                    "NUMERIC"=>"NUMERIC",
                    "BINARY"=>"BINARY",
                    "DATE"=>"DATE",
                    "CHAR"=>"CHAR",
                    "DATETIME"=>"DATETIME",
                    "DECIMAL"=>"DECIMAL",
                    "SIGNED"=>"SIGNED",
                    "TIME"=>"TIME",
                    "UNSIGNED"=>"UNSIGNED"
                )
            );
            
            $tax_query_data=  $this->get_wad_tax_query_data();
            ?>
                    <script>
                    var wad_tax_query_recap=<?php echo json_encode($tax_query_data["values"]);?>;
                    </script>
            <?php
            
            $metas_filters= array(
                'title' => __('Metas', 'wad'),
                'desc' => __('Filter by metas', 'wad'),
                'name'=>'o-list[meta_query][queries]',
                'row_class'=>'extract-by-custom-request-row',
                'row_css'=>$custom_request_css,
                'type' => 'repeatable-fields',
                'fields'=> array($meta_filter_key, $meta_filter_compare, $meta_filter_value, $meta_filter_type),
            );
            
            $taxonomies_relationship= array(
                'title' => __('Taxonomies relationship', 'wad'),
                'name'=>'o-list[tax_query][relation]',
                'row_class'=>'extract-by-custom-request-row',
                'row_css'=>$custom_request_css,
                'type' => 'select',
                'default' => '',
                'options' => array(
                    "AND"=> 'AND',
                    "OR"=> 'OR'
                    )
            );
            
            $taxonomy_filter_key= array(
                'title' => __('Taxonomy', 'wad'),
                'name'=>'taxonomy',
                'type' => 'select',
                'class'=>'wad-taxonomies-selector',
                'options' => $tax_query_data["params"],
            );
            
            $taxonomy_filter_operator= array(
                'title' => __('Operator', 'wad'),
                'name'=>'operator',
                'type' => 'select',
                'options'=> array(
                    "IN"=>"IN",
                    "NOT IN"=>"NOT IN",
                    "AND"=>"AND",
                )
            );
            
            $taxonomy_filter_value= array(
                'title' => __('Value', 'wad'),
                'name'=>'terms',
                'type' => 'multiselect',
                'class' => 'wad-terms-selector',
                'options' => $tax_query_data["values_arr"],
            );
            
            $taxonomies_filters= array(
                'title' => __('Taxonomies', 'wad'),
                'desc' => __('Filter by taxonomies (Categories, Tags, Attributes)', 'wad'),
                'name'=>'o-list[tax_query][queries]',
                'row_class'=>'extract-by-custom-request-row',
                'row_css'=>$custom_request_css,
                'type' => 'repeatable-fields',
                'fields'=> array($taxonomy_filter_key, $taxonomy_filter_operator, $taxonomy_filter_value),
            );
            


            $end = array('type' => 'sectionend');
            $settings=array(
                $begin,
                $extraction_type,
                $ids_list,
                $author,
                $exclude,
                $taxonomies_relationship,
                $taxonomies_filters,
                $metas_relationship,
                $metas_filters,                
                $end
                );
            echo o_admin_fields($settings);
                    ?>
                </div>
                <a id="wad-check-query" class="button mg-top"><?php _e("Evaluate", "wad");?></a>
                <span id="wad-evaluate-loading" class="wad-loading mg-top mg-left" style="display: none;"></span>
                <div id="debug" class="mg-top"></div>
            <?php
        global $o_row_templates;
            ?>
        <script>
            var o_rows_tpl=<?php echo json_encode($o_row_templates);?>;
        </script>
        <?php
    }
    
    private function get_wad_tax_query_data()
    {
        $tax_terms = get_taxonomies(array(), 'objects');

        $params=array();
        $values=array();
        $values_arr=array();
        $values_arr_by_key=array();

        foreach ($tax_terms as $tax_key=>$tax_obj)
        {
            //We ignore everything that has nothing to do with products
            if(!in_array("product", $tax_obj->object_type))
//            if(!o_startsWith($tax_key, "product_")&&!o_startsWith($tax_key, "pa_"))
                    continue;
            $params[$tax_key]=$tax_obj->labels->singular_name;
            $terms=  get_terms($tax_key);
            $terms_select="";
            foreach ($terms as $term)
            {
                $terms_select.='<option value="'.$term->term_id.'">'.$term->name.'</option>';
                $values_arr[$term->term_id]=$term->name;
                if(!isset($values_arr_by_key[$tax_key]))
                    $values_arr_by_key[$tax_key]=array();
                $values_arr_by_key[$tax_key][$term->term_id]=$term->name;
            }
            if($terms_select)
            {
                $values[$tax_key]=$terms_select;
            }
            else//Empty tax element. We remove it from the labels
                unset ($params[$tax_key]);
        }

        return array(
            "params"=>$params,
            "values"=>$values,
            "values_arr"=>$values_arr,
            "values_arr_by_key"=>$values_arr_by_key,
        );
    }
    
    function get_authors()
    {
        $all_users=  get_users();
        $authors_arr=array(""=>"Any");
        foreach ($all_users as $user)
        {
            $authors_arr[$user->ID]=$user->user_nicename;
        }

        return $authors_arr;
    }
    
    /**
    * Saves the display data
    * @param type $post_id
    */
   public function save_list($post_id) {
       //Optimize and merge the two meta
       $meta_key="o-list";
       if(isset($_POST[$meta_key]))
       {
           update_post_meta($post_id, $meta_key, $_POST[$meta_key]);
       }
   }
   
   public function get_all()
   {
        global $wpdb;
        $sql="select ID, post_title from $wpdb->posts where post_type='o-list' and post_status='publish'";
        $results=$wpdb->get_results($sql);
        foreach ($results as $result_row)
        {
            $lists_arr[$result_row->ID]=$result_row->post_title;
        }
        return $lists_arr;
   }
   
   public function evaluate_wad_query()
    {
        $parameters=$_POST["data"]["o-list"];
        $args=$this->get_args($parameters);
        $posts=  get_posts($args);
        $msg=count($posts).__(" result(s) found","acd");
        if(count($posts))
        {
            $msg.=": (";
            foreach ($posts as $post)
            {
                $msg.=$post->post_title.", ";
            }
            $length=  strlen($msg);
            $msg=  substr($msg, 0, $length-2);
            $msg.=")";
        }
        else
            $msg.=".";
        echo json_encode(array("msg"=>$msg));
        die();

    }
    
    public function get_args($raw_args = false) {
        if(!$raw_args)
            $raw_args=  $this->args;
        
        $args = array(
            "post_type"=>array("product", "product_variation")
            );
        if(isset($raw_args["type"])&&$raw_args["type"]=="by-id")
        {
            $args['post__in'] = explode(",",$raw_args["ids"]);
        }
        else
        {
            //Tax queries
            if (isset($raw_args["tax_query"]["queries"])) {
                $args["tax_query"] = array();
                $args["tax_query"]["relation"] = $raw_args["tax_query"]["relation"];
                foreach ($raw_args["tax_query"]["queries"] as $query) {
                    array_push($args["tax_query"], $query);
                }
            }

            //Metas
            if (isset($raw_args["meta_query"]["queries"])) {
                $args["meta_query"] = array();
                $args["meta_query"]["relation"] = $raw_args["meta_query"]["relation"];
                foreach ($raw_args["meta_query"]["queries"] as $query) {
                    //Some operators expect an array as value
                    $array_operators = array('IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN');
                    if (in_array($query["compare"], $array_operators))
                        $query["value"] = explode(",", $query["value"]);
                    array_push($args["meta_query"], $query);
                }
            }

            //Other parameters
            $other_parameters = array("author__in", "post__not_in");
            foreach ($other_parameters as $parameter) {
                if (!isset($raw_args[$parameter]))
                    continue;
                if ($parameter == "post__not_in")
                    $args[$parameter] = explode(",", $raw_args[$parameter]);
                else if ($parameter == "author__in" && $raw_args[$parameter] == array(""))
                    continue;
                else
                    $args[$parameter] = $raw_args[$parameter];
            }
        }
        
        $args["nopaging"]=true;

        return $args;
    }
    
    public function get_products($force_old=false)
    {
        global $wad_settings;
        $to_return=array();
        $products=array();
        $variations_ids=array();
        
        if(empty($products))
        {
            $new_extraction_algorithm=  get_proper_value($wad_settings, "new-extraction-algorithm", 1);
            if($force_old)
                $new_extraction_algorithm=false;
            //If we don't do this, the free gifts prices are not removed from the total 
            //right after the customer clicks on the place order button
            if(wad_is_checkout())
                $new_extraction_algorithm=false;
            $args=  $this->get_args();
            if($new_extraction_algorithm)
            {
                global $wad_last_products_fetch;
                if(empty($wad_last_products_fetch))
                    return array();
                else if($wad_last_products_fetch && $wad_last_products_fetch!=$this->last_fetch)
                {
                    //We make sure that the values excluded using the list exclude field are not included again in the last fetch
                    if(isset($args['post__not_in'])&&!empty($args['post__not_in']))
                    {
                        $wad_last_products_fetch=  array_diff($wad_last_products_fetch, $args['post__not_in']);
                    }
                    if(isset($args["post__in"]))
                        array_merge ($args["post__in"], $wad_last_products_fetch);
                    else
                        $args["post__in"]=$wad_last_products_fetch;
                    
                    $this->last_fetch=$wad_last_products_fetch;
                    $this->products=false;
                }
                else
                    $products=$this->products;
            }
            
            if($this->products && $force_old==FALSE)
            {
                $products=$this->products;
            }
            else
            {
                $products=  get_posts($args);
                if(!empty($products))
                {
                    $to_return=array_map(create_function('$o', 'return $o->ID;'), $products);
                    //This will make sure the variations are included for the variable products
                    $variations_ids= $this->get_request_variations($products);
                    $this->products=  array_merge($to_return, $variations_ids);
                    
                }
            }
        }
        
        return $this->products;
    }
    
    /**
     * Check if the request contains any variation. If it does not, it adds returns all variations linked to the request
     * @global type $wpdb
     * @param type $posts
     * @return type
     */
    private function get_request_variations($posts)
    {        
        $results=array();
        $variations = array_filter(
            $posts,
            function ($e) {
                return $e->post_type == "product_variation";
            }
        );
        //If there is no variation in the list, we gather the variations manually and add them to the list
        if(empty($variations))
        {
            global $wpdb;
            $parents_ids=array_map(create_function('$o', '$p=wc_get_product($o->ID); if($p->get_type()=="variable") return $o->ID;'), $posts);
            $clean_parents_ids=array_filter($parents_ids);
            $parents_ids_str= implode(",", $clean_parents_ids);
            if(!empty($parents_ids_str))
            {
                $request="select distinct id from $wpdb->posts where post_parent in($parents_ids_str) and post_type='product_variation'";
                $results=$wpdb->get_col($request);
            }
            
        }
        
        return $results;
    }

}