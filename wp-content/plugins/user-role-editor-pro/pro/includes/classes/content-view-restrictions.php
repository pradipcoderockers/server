<?php

/*
 * User Role Editor WordPress plugin
 * Content view access by selected roles management - at post level
 * Author: Vladimir Garagulya
 * Author email: support@role-editor.com
 * Author URI: https://www.role-editor.com
 * License: GPL v2+ 
 */

class URE_Content_View_Restrictions {
    
    const view_posts_access_cap = 'ure_view_posts_access';
    const content_for_roles = 'ure_content_for_roles';
    const prohibit_allow_flag = 'ure_prohibit_allow_flag';
    const content_view_whom = 'ure_content_view_whom';
    const post_access_error_action = 'ure_post_access_error_action';
    const post_access_error_message = 'ure_post_access_error_message';
    
    private $lib = null;
    private $posts_view_access = null;
    
    
    public function __construct() {        
        
        $this->lib = URE_Lib_Pro::get_instance();
        
        $this->posts_view_access = new URE_Posts_View_Access();
        $this->checked_posts = array();
        
        add_action('add_meta_boxes', array($this, 'add_post_meta_box'));        
        add_action('admin_enqueue_scripts', array($this, 'admin_css_action'));
        add_action('admin_enqueue_scripts', array($this, 'admin_load_js'));                
        
        add_action('save_post', array($this, 'save_meta_data'));
        add_action('add_attachment', array($this, 'save_meta_data'));
        add_action('edit_attachment', array($this, 'save_meta_data'));
        
        // exclude prohibited posts/pages and other post types from listings
        URE_Content_View_Restrictions_Posts_List::get_instance();
                        
        // set content view restrictions
        add_filter('the_content', array($this, 'restrict'), 999);
        add_filter('get_the_excerpt', array($this, 'restrict'), 999);
        add_filter('the_excerpt', array($this, 'restrict'), 999);
        add_filter('the_content_feed', array($this, 'restrict'), 999);
        add_filter('comment_text_rss', array($this, 'restrict'), 999);
        
        // Apply WordPress formatting filters for the post access error message.
        add_filter('ure_post_access_error_message', 'wptexturize');
        add_filter('ure_post_access_error_message', 'convert_smilies');
        add_filter('ure_post_access_error_message', 'convert_chars');
        add_filter('ure_post_access_error_message', 'wpautop');
        add_filter('ure_post_access_error_message', 'shortcode_unautop');
        add_filter('ure_post_access_error_message', 'do_shortcode');


        // this filter is applied at map_meta_cap() for 'edit_post_meta' capability
        add_filter('auth_post_meta_'. self::content_for_roles, array($this, 'auth_post_meta'), 10, 6);
        add_filter('auth_post_meta_'. self::prohibit_allow_flag, array($this, 'auth_post_meta'), 10, 6);
        add_filter('auth_post_meta_'. self::content_view_whom, array($this, 'auth_post_meta'), 10, 6);
        add_filter('auth_post_meta_'. self::post_access_error_action, array($this, 'auth_post_meta'), 10, 6);
        add_filter('auth_post_meta_'. self::post_access_error_message, array($this, 'auth_post_meta'), 10, 6);
        
        global $wlb_plugin;
        if (!empty($wlb_plugin)) {
            add_action('wp_dashboard_setup', array($this, 'wlb_dashboard_restrict'), 1000000);
        }
        
    }
    // end of __construct()
    
    
    // block access to URE's post meta (custom) fields, if user does not have enough permissions
    public function auth_post_meta($allowed, $meta_key, $post_id, $user_id, $cap, $caps) {
        
        $allowed = current_user_can(self::view_posts_access_cap);
        
        return $allowed;
        
    }
    // end of auth_post_meta()
    
    
    public function add_post_meta_box() {

        if (!current_user_can(self::view_posts_access_cap)) {
            return;
        }
        
        $post_types = $this->lib->_get_post_types();
        foreach ($post_types as $post_type) {
            add_meta_box(
                    'ure_content_view_restrictions_meta_box', 
                    esc_html__('Content View Restrictions', 'user-role-editor'), 
                    array($this, 'render_post_meta_box'),
                    $post_type, 
                    'normal', 
                    'default'
            );
        }
    }
    // end of add_meta_box()
            
    /**
     * Builds HTML to show roles list with checkboxes in the modal dialog window
     * 
     * @param string $content_for_roles
     * @return string
     */
    private function get_roles_list_html($content_for_roles) {
        global $wp_roles;
        
        $selected_roles = explode(', ', $content_for_roles);
        $roles_list = '<input type="checkbox" id="ure_roles_auto_select" name="ure_roles_selector" value="1"><hr/>';
        $roles = array_keys($wp_roles->roles);
        asort($roles);
        foreach($roles as $role_id) {
            if (in_array($role_id, $selected_roles)) {
                $role_selected = 'checked';
            } else {
                $role_selected = '';
            }
            $roles_list .= '<input type="checkbox" id="'. $role_id .'" name="'. $role_id .'" class="ure_role_cb" value="1" '. $role_selected .'>&nbsp'.
                           '<label for="'. $role_id .'">' .$wp_roles->roles[$role_id]['name'] .' ('. $role_id .')</label><br>'."\n";
        }
        $roles_list .= '<input type="checkbox" id="no_role" name="no_role" class="ure_role_cb" value="1" '. (in_array('no_role', $selected_roles) ? 'checked' : '') . '>&nbsp'.
                           '<label for="no_role">No role for this site</label><br>'."\n";        
        
        return $roles_list;
    }
    // end of get_roles_list_html()
    
    
    /**
     * Output needed HTML for metadata meta box
     * 
     */
    public function render_post_meta_box($post) {        
        
        /*
         * Use get_post_meta() to retrieve an existing value
         * from the database and use the value for the form.
         */
        
        $prohibit_allow_flag = get_post_meta($post->ID, self::prohibit_allow_flag, true);
        if (empty($prohibit_allow_flag)) {
            $prohibit_allow_flag = $this->lib->get_option('content_view_allow_flag', 2);
        }
        
        $content_view_whom = get_post_meta($post->ID, self::content_view_whom, true);
        if (empty($content_view_whom)) {
            if ($this->lib->is_new_post()) {
                $content_view_whom = $this->lib->get_option('content_view_whom', 3);
            } else {
                $content_view_whom = 3; // selected roles
            }
        }
        
        $content_for_roles = get_post_meta($post->ID, self::content_for_roles, true);
        $roles_list = $this->get_roles_list_html($content_for_roles);        
        
        $content_view_access_error_action = get_post_meta($post->ID, self::post_access_error_action, true);
        if (empty($content_view_access_error_action)) {
            $content_view_access_error_action = $this->lib->get_option('content_view_access_error_action', 2);
            // It's possible to modify default value for the post view access error action: 1 - 404 HTTP error or 2 - show error message
            $content_view_access_error_action = apply_filters('ure_default_post_access_error_action', $content_view_access_error_action);
        }
        
        $post_access_error_message = get_post_meta($post->ID, self::post_access_error_message, true);        
        
        // Add an nonce field so we can check for it later.
        wp_nonce_field('ure_content_view_restrictions_meta_box', 'ure_content_view_restrictions_meta_box_nonce');        
    ?>
<div style="margin-bottom: 10px;">
    <strong><?php esc_html_e('View Access:','user-role-editor');?></strong>
    <div style="padding-left: 20px;">
        <input type="radio" id="ure_allow_flag" name="ure_prohibit_allow_flag" value="2"  <?php checked($prohibit_allow_flag, 2);?> > <label for="ure_allow_flag"><?php echo esc_html_e('Allow View', 'user-role-editor');?></label><br>
        <input type="radio" id="ure_prohibit_flag" name="ure_prohibit_allow_flag" value="1"  <?php checked($prohibit_allow_flag, 1);?> > <label for="ure_prohibit_flag"><?php echo esc_html_e('Prohibit View', 'user-role-editor');?></label>&nbsp;
    </div>    
</div>

<div style="margin-bottom: 10px;">
    <strong><?php esc_html_e('For Users:','user-role-editor');?></strong>
    <div style="padding-left: 20px;">
        <input type="radio" id="content_view_whom_all" name="ure_content_view_whom" value="1"  <?php checked($content_view_whom, 1); ?> class="ure_content_view_whom" > 
        <label for="content_view_whom_all"><?php echo esc_html_e('All visitors (logged in or not)', 'user-role-editor'); ?></label><br>
        <input type="radio" id="content_view_whom_any_role" name="ure_content_view_whom" value="2"  <?php checked($content_view_whom, 2); ?> class="ure_content_view_whom" > 
        <label for="content_view_whom_any_role"><?php echo esc_html_e('Any User Role (logged in only)', 'user-role-editor'); ?></label><br>
        <input type="radio" id="content_view_whom_selected_roles" name="ure_content_view_whom" value="3"  <?php checked($content_view_whom, 3); ?> class="ure_content_view_whom" > 
        <label for="ure_content_view_whom_selected_roles"><?php echo esc_html_e('Selected User Roles', 'user-role-editor'); ?></label>
        <div id="ure_selected_roles_container" style="display: none; padding-left: 20px;">
            <button id="edit_content_for_roles"><?php echo esc_html_e('Edit Roles List', 'user-role-editor');?></button><br>
            <div style="padding-top: 5px;">
                <textarea id="ure_content_for_roles" name="ure_content_for_roles" rows="3" style="width: 100%;" readonly="readonly"><?php echo $content_for_roles;?></textarea>
            </div>
        </div>
    </div>    
</div>
<strong><?php esc_html_e('Action:','user-role-editor');?>
<div style="padding-left: 20px;">    
    <input type="radio" id="ure_return_http_error_404" name="ure_post_access_error_action" value="1"  
        <?php checked($content_view_access_error_action, 1);?> > 
    <label for="ure_return_http_error_404"><?php esc_html_e('Return HTTP 404 error', 'user-role-editor');?></label><br>
    <input type="radio" id="ure_show_post_access_error_message" name="ure_post_access_error_action" value="2"  
        <?php checked($content_view_access_error_action, 2);?> > 
    <label for="ure_show_post_access_error_message"><?php esc_html_e('Show access error message', 'user-role-editor');?></label><br>
    <input type="radio" id="ure_show_post_access_error_message_custom" name="ure_post_access_error_action" value="3"  
        <?php checked($content_view_access_error_action, 3);?> > 
    <label for="ure_show_post_access_error_message_custom"><?php esc_html_e('Show custom access error message', 'user-role-editor');?></label>
    <div id="ure_post_access_error_message_container" style="display: none; padding-left: 20px;">
        <textarea id="ure_post_access_error_message" name="ure_post_access_error_message" rows="3" style="width: 100%;"><?php echo $post_access_error_message;?></textarea> 
    </div>    
</div>
<div style="text-align: right; color: #cccccc; font-size: 0.8em;"><?php esc_html_e('User Role Editor Pro', 'user-role-editor');?></div>

<div id="edit_roles_list_dialog" style="display: none;">
    <div id="edit_roles_list_dialog_content" style="padding:10px;">
        <?php echo $roles_list; ?>
    </div>    
</div>    
    <?php        
    }
    // end of hpn_render_meta_box()


   /**
     * Load plugin javascript stuff
     * 
     * @param string $hook_suffix
     */
    public function admin_load_js($hook_suffix) {
        if (!in_array($hook_suffix, array('post.php', 'post-new.php'))) {
            return;
        }   
                
        if (!current_user_can(self::view_posts_access_cap)) {
            return;
        }
        wp_enqueue_script('jquery-ui-dialog', '', array('jquery-ui-core', 'jquery-ui-button', 'jquery'));            
        wp_register_script('ure-pro-content-view-restrictions', plugins_url('/pro/js/content-view-restrictions.js', URE_PLUGIN_FULL_PATH));
        wp_enqueue_script('ure-pro-content-view-restrictions');
        wp_localize_script('ure-pro-content-view-restrictions', 'ure_data_pro', array(
            'wp_nonce' => wp_create_nonce('user-role-editor'),
            'edit_content_for_roles' => esc_html__('Edit Roles List', 'user-role-editor'),
            'edit_content_for_roles_title' => esc_html__('Roles List restrict/allow content view', 'user-role-editor'),
            'save_roles_list' => esc_html__('Save', 'user-role-editor'),
            'close' => esc_html__('Close', 'user-role-editor')
        ));

    }
    // end of admin_load_js()
    
    
    public function admin_css_action($hook_suffix) {        
        if (!in_array($hook_suffix, array('post.php', 'post-new.php'))) {
            return;
        }
        if (!current_user_can(self::view_posts_access_cap)) {
            return;
        }
        
        wp_enqueue_style('wp-jquery-ui-dialog');

    }
    // end of admin_css_action()
            
    
    protected function check_security($post_id) {

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return false;
        }

        // Verify that the nonce is valid.
        $nonce = filter_input(INPUT_POST, 'ure_content_view_restrictions_meta_box_nonce', FILTER_SANITIZE_STRING);                
        if (empty($nonce) || !wp_verify_nonce($nonce, 'ure_content_view_restrictions_meta_box')) {
            return false;
        }

        if (!current_user_can(self::view_posts_access_cap)) {
            return false;
        }
        
        if (!$this->lib->can_edit($post_id)) {
            return false;
        }
        
        return true;        
    }
    // end of check_security()
    
    
    // Save meta data with post/page data save event together
    public function save_meta_data($post_id) {

        global $wp_roles;
        
        if (!$this->check_security($post_id)) {
            return $post_id;
        }
        /* OK, its safe for us to save the data now. */
        $ure_prohibit_allow_flag = $this->lib->get_request_var('ure_prohibit_allow_flag', 'post', 'int');        
        update_post_meta($post_id, self::prohibit_allow_flag, $ure_prohibit_allow_flag);

        $content_view_whom = $this->lib->get_request_var('ure_content_view_whom', 'int');
        update_post_meta($post_id, self::content_view_whom, $content_view_whom);
        if ($content_view_whom==3) {    // for selected roles
            $ure_content_for_roles0 = $this->lib->get_request_var('ure_content_for_roles', 'post');
            // Update the meta field.
            $roles_to_check = explode(',', $ure_content_for_roles0);
            $roles_to_save = array();
            foreach($roles_to_check as $role) {
                $role = trim($role);
                if ($role=='no_role' || isset($wp_roles->roles[$role])) {
                    $roles_to_save[] = $role;
                }
            }
            $ure_content_for_roles1 = implode(', ', $roles_to_save);
            update_post_meta($post_id, self::content_for_roles, $ure_content_for_roles1);
        }
        
        $ure_post_access_error_action = $this->lib->get_request_var('ure_post_access_error_action', 'post', 'int');
        update_post_meta($post_id, self::post_access_error_action, $ure_post_access_error_action);        
        if ($ure_post_access_error_action==3) { // custom access error message
            $ure_post_access_error_message = $_POST['ure_post_access_error_message'];
            update_post_meta($post_id, self::post_access_error_message, $ure_post_access_error_message);
        }
    }
    // end of save_meta_data()

    
    private function get_post_access_error_message($post_id) {        
        
        $post_access_error_action = (int) get_post_meta($post_id, self::post_access_error_action, true);
        if ($post_access_error_action==1) { // HTTP 404
            return '';
        }
        
        $post_access_error_message = '';
        if ($post_access_error_action===3) { // Show custom access error message for this post
            $post_access_error_message = get_post_meta($post_id, self::post_access_error_message, true);
        }
            
        if (empty($post_access_error_message)) { // Show global access error message
            $post_access_error_message = stripslashes($this->lib->get_option('post_access_error_message'));
        }        
        
        $post_access_error_message = apply_filters('ure_post_access_error_message', $post_access_error_message);
        
        return $post_access_error_message;
    }
    // end of get_post_access_error_message;
    
    
    private function check_post_level_permissions($content) {
        global $post;
        
        $data = array('post_level'=>false, 'content'=>$content);
        $prohibit_allow_flag = get_post_meta($post->ID, self::prohibit_allow_flag, true);
        $content_view_whom = get_post_meta($post->ID, self::content_view_whom, true);
        if (empty($content_view_whom)) {
            $content_view_whom = 3;    // Use as the default value
        }
        
        if ($prohibit_allow_flag==2) {  // Allow
            if ($content_view_whom==1) { // For All
                return $data;
            }
            if ($content_view_whom==2 && is_user_logged_in()) {  //  Allow to logged in only (any role)
                // permissions are applied at the post level
                $data['post_level'] = true;
                return $data;
            }
        }
        
        if ($content_view_whom==3) { //  Selected roles only
            $ure_content_for_roles = get_post_meta($post->ID, self::content_for_roles, true);
            if (empty($ure_content_for_roles)) {    // no roles selected
                return $data;
            }
            $roles = explode(', ', $ure_content_for_roles);
            if (count($roles)==0) { // no roles selected
                return $data;
            }
        }

        // permissions are applied at the post level
        $data['post_level'] = true;
        
        $post_access_error_message = $this->get_post_access_error_message($post->ID);
        
        if ($prohibit_allow_flag==1) { // Prohibit
            if ($content_view_whom==1) {  // For All
                $data['content'] = $post_access_error_message;
                return $data;
            }
            if ($content_view_whom==2) {
                if (is_user_logged_in()) { // For logged in only (any role)
                    $data['content'] = $post_access_error_message;
                }
                return $data;
            }
        } else {    // Allow
            if ($content_view_whom==2) { // For logged in only (any role)
                if (!is_user_logged_in()) {
                    $data['content'] = $post_access_error_message;
                }
                return $data;
            }
        }
        
        // For selected roles only        
        if (!is_user_logged_in()) { // No role for this site
            if ($prohibit_allow_flag==1) {  // Prohibit
                if (in_array('no_role', $roles)) {
                    $data['content'] = $post_access_error_message;
                }
            } elseif (!in_array('no_role', $roles)) {
                $data['content'] = $post_access_error_message;                    
            }
            return $data;
        }
        
        if ($prohibit_allow_flag==1) {  
            $result0 = $content;
            $result1 = $post_access_error_message;    // for prohibited access
        } else {    
            $result0 = $post_access_error_message;
            $result1 = $content;     // for allowed access
        }
        
        foreach($roles as $role) {
            if (current_user_can($role)) {
                $data['content'] = $result1;
                return $data;
            }
        }
        
        $data['content'] = $result0;
        
        return $data;
    }
    // end of check_post_level_permissions()
    
    
    public static function is_object_restricted_for_role($id_to_check, $blocked, $entity) {
        $blocked_list = isset($blocked['data'][$entity]) ? $blocked['data'][$entity] : array();
        if (count($blocked_list)==0) {
            return false;
        }
        
        $restricted = false;
        if ($blocked['access_model']==1) { // Selected
            if (in_array($id_to_check, $blocked_list)) {
                $restricted = true;
            }
        } else {
            if (!in_array($id_to_check, $blocked_list)) {
                $restricted = true;
            }
        }
        
        return $restricted;
    }
    // end of is_post_restricted_for_role()


    public static function is_author_restricted_for_role($author_id, $blocked) {
        global $current_user;
        
        $blocked_list = isset($blocked['data']['authors']) ? $blocked['data']['authors'] : array();
        if (isset($blocked['data']['own_data_only']) && $blocked['data']['own_data_only']==1) {
            if (!in_array($current_user->ID, $blocked_list)) {
                $blocked_list[] = $current_user->ID;
            }
        }
        if (count($blocked_list)==0) {
            return false;
        }
        
        $restricted = false;
        if ($blocked['access_model']==1) { // Selected
            if (in_array($author_id, $blocked_list)) {
                $restricted = true;
            }
        } else {
            if (!in_array($author_id, $blocked_list)) {
                $restricted = true;
            }
        }
        
        return $restricted;
    }
    // end of is_post_restricted_for_role()    
    
    
    public static function is_term_restricted_for_role($post_id, $blocked) {
        $blocked_terms = isset($blocked['data']['terms']) ? $blocked['data']['terms'] : array();
        if (count($blocked_terms)==0) {
            return false;
        }
        
        $restricted = false;
        $taxonomies = get_taxonomies(array('public'=>'true', 'show_ui'=>true));
        $post_terms = wp_get_object_terms(array($post_id), $taxonomies);
        foreach($post_terms as $term) {
            if (self::is_object_restricted_for_role($term->term_id, $blocked, 'terms')) {
                $restricted = true;
                break;
            }
        }        
        
        return $restricted;
    }
    // end of is_term_restricted_for_role()
    
    
    /**
     * For "Block not selected" access modell only
     * Returns true if post is not included into the list of selected posts (by ID or by terms/categories)
     * 
     * @param int $post_id
     * @param array $blocked
     */
    public static function is_post_restricted_for_role($post_id, $blocked) {

        $lib = URE_Lib_Pro::get_instance();
        $selected_posts = isset($blocked['data']['posts']) ? $blocked['data']['posts'] : array();
        if (isset($blocked['data']['terms']) && count($blocked['data']['terms']) > 0) {
            $post_by_terms = $lib->get_posts_by_terms($blocked['data']['terms']);
            if (count($post_by_terms) > 0) {
                $selected_posts = array_merge($selected_posts, $post_by_terms);
            }
        }

        $result = !in_array($post_id, $selected_posts);

        return $result;
    }
    // end of is_post_restricted_for_role()


    private function check_roles_level_permissions($content) {
    
        global $current_user, $post;
        
        if ($current_user->ID==0) {
            return $content;
        }
        
        $blocked = URE_Content_View_Restrictions_Controller::load_access_data_for_user($current_user);
        if (empty($blocked)) {
            return $content;
        }
        
        if ($blocked['access_model']==1) {
            if (self::is_object_restricted_for_role($post->ID, $blocked, 'posts')) {
                $post_access_error_message = $this->get_post_access_error_message($post->ID);
                return $post_access_error_message;
            }

            if (self::is_term_restricted_for_role($post->ID, $blocked)) {
                $post_access_error_message = $this->get_post_access_error_message($post->ID);
                return $post_access_error_message;
            }
        } else {
            if (self::is_post_restricted_for_role($post->ID, $blocked)) {
                $post_access_error_message = $this->get_post_access_error_message($post->ID);
                return $post_access_error_message;
            }
        } 
                        
        return $content;
    }
    // end of check_roles_level_permissions()       
    
    
    private function get_post_from_last_query() {
        global $wpdb;
        
        if (empty($wpdb->last_query)) {
            return null;
        }
        
        $keys = array('WHERE ID=');
        $post_id = 0;
        foreach($keys as $key) {
            $post_id = URE_Utils::get_int_after_key($key, $wpdb->last_query);
            if ($post_id>0) {
                break;
            }            
        }
        if (empty($post_id)) {
            return null;
        }
        
        $post = get_post($post_id);
        
        return $post;
    }
    // end of get_post_from_last_query()
    
    
    public function restrict($content) {
        
        global $post, $current_user;
        
        $post1 = $post; // do not touch global variable, work with its copy
        if (empty($post1) && !in_the_loop()) {
            $post1 = $this->get_post_from_last_query();
            if (empty($post1)) {
                return $content;
            }
        }
        
        if (empty($post1->ID)) { 
            return $content;
        }
                
        // no restrictions for users who can edit this post
        if ($this->lib->can_edit($post1)) {
            return $content;
        }
        
        $result = $this->check_post_level_permissions($content);
        if ($result['post_level']) {
            $content = $result['content'];
        }
        
        if ($current_user->ID==0) {
            return $content;
        }
        
        $content = $this->check_roles_level_permissions($content);
        
        return $content;
    }
    // end of restrict()
    
        
    /**
     * Returns object with data about view access restrictions applied to the post with ID $post_id or
     * false in case there are not any view access restrictions for this post
     * 
     * @param int $post_id  Post ID
     * @return \stdClass|boolean
     */
    public static function get_post_view_access_users($post_id) {
        global $wpdb;
        
        $ure_content_for_roles = get_post_meta($post_id, URE_Content_View_Restrictions::content_for_roles, true);
        if (empty($ure_content_for_roles)) {
            return false;
        }
        $restricted_roles = explode(', ', $ure_content_for_roles);
        if (count($restricted_roles)==0) {
            return false;
        }
        
        $ure_prohibit_allow_flag = get_post_meta($post_id, self::prohibit_allow_flag, true);
        $restriction = ($ure_prohibit_allow_flag==1) ? 'prohibited' : 'allowed';

        $id = get_current_blog_id();
        $blog_prefix = $wpdb->get_blog_prefix($id);
        $users = $wpdb->get_results("SELECT user_id, meta_value FROM $wpdb->usermeta WHERE meta_key='{$blog_prefix}capabilities'");

        $restricted_users = array();
        foreach ($users as $user) {
            $user_roles = maybe_unserialize($user->meta_value);
            if (!is_array($user_roles)) {
                continue;
            }
            foreach (array_keys($user_roles) as $user_role) {
                if (in_array($user_role, $restricted_roles)) {
                    $restricted_users[] = $user->user_id;
                }
            }
        }


        $result = new stdClass();
        $result->restriction = $restriction;  // restriction kind: allowed or prohibited
        $result->roles = $restricted_roles;   // the list of roles ID, for which this content view access restriction is applied 
        $result->users = $restricted_users;   // the list of users ID, for which this content view access restriction is applied 
        
        return $result;
    }
    // end of get_post_view_access_users()
    
    
    public static function current_user_can_view($post_id) {
        
        
        $lib = URE_Lib_Pro::get_instance();
        $activated = $lib->get_option('activate_content_for_roles', false);
        if (!$activated) {
            return true;
        }
        if (current_user_can('administrator')) {
            return true;
        }
        
        $cvrpl = URE_Content_View_Restrictions_Posts_List::get_instance();
        $prohibited_posts = $cvrpl->get_current_user_prohibited_posts();
        if (!in_array($post_id, $prohibited_posts)) {
            return true;
        }
             
        return false;
    }
    // end of can_view()
                    
}
// end of URE_Content_View_Restrictions class