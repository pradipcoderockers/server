=== User Role Editor Pro ===
Contributors: Vladimir Garagulya (https://www.role-editor.com)
Tags: user, role, editor, security, access, permission, capability
Requires at least: 4.0
Tested up to: 4.7.4
Stable tag: 4.34
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

With User Role Editor WordPress plugin you may change WordPress user roles and capabilities easy.

== Description ==

With User Role Editor WordPress plugin you can change user role capabilities easy.
Just turn on check boxes of capabilities you wish to add to the selected role and click "Update" button to save your changes. That's done. 
Add new roles and customize its capabilities according to your needs, from scratch of as a copy of other existing role. 
Unnecessary self-made role can be deleted if there are no users whom such role is assigned.
Role assigned every new created user by default may be changed too.
Capabilities could be assigned on per user basis. Multiple roles could be assigned to user simultaneously.
You can add new capabilities and remove unnecessary capabilities which could be left from uninstalled plugins.
Multi-site support is provided.

== Installation ==

Installation procedure:

1. Deactivate plugin if you have the previous version installed.
2. Extract "user-role-editor-pro.zip" archive content to the "/wp-content/plugins/user-role-editor-pro" directory.
3. Activate "User Role Editor Pro" plugin via 'Plugins' menu in WordPress admin menu. 
4. Go to the "Settings"-"User Role Editor" and adjust plugin options according to your needs. For WordPress multisite URE options page is located under Network Admin Settings menu.
5. Go to the "Users"-"User Role Editor" menu item and change WordPress roles and capabilities according to your needs.

In case you have a free version of User Role Editor installed: 
Pro version includes its own copy of a free version (or the core of a User Role Editor). So you should deactivate free version and can remove it before installing of a Pro version. 
The only thing that you should remember is that both versions (free and Pro) use the same place to store their settings data. 
So if you delete free version via WordPress Plugins Delete link, plugin will delete automatically its settings data. 
You will have to configure User Role Editor Pro Settings again after that.
Right decision in this case is to delete free version folder (user-role-editor) via FTP, not via WordPress.


== Changelog ==
= [4.34] 21.04.2017 =
* Core version: 4.32.3
* New: Front end menu access add-on: 
*   - "Not logged-in and logged-in users with selected roles" option was added.
*   - menu items with links to the posts/pages prohibited for view for current user by "Content view restrictions" add-on are excluded from menu automatically.
* New: Content view restrictions add-on: 
*   - Shortcode [user_role_editor] roles / except_roles attributes support '&&' role ID separator. For example roles="subscriber && customer" means that user should have both roles simultaneously, comparing to the roles="subscriber, customer" which works for subscribers or customers or (subscribers and customers).
*   - public static method URE_Content_View_Restrictions::current_user_can_view($post_id) was added. It returns boolean value.
* Update: Content view restrictions add-on:
*   - roles list opened at the post level is sorted by alphabet.
*   - Singleton pattern was applied to the URE_Content_View_Restrictions_Posts_List class.
* Update: Admin menu access add-on: "block not selected model": support was added for URL parameters added to users.php by "Ultimate Member" plugin.
* Fix: Content view restrictions add-on: 
*   - default setting for access error action "return HTTP 404 error" was not always applied to the new added post. 
*   - categories/tags/terms group selection checkboxes work separately now for every term group - categories, tags, etc.
* Fix: bbPress role support was broken, even administrator did not see bbPress menu and user roles in some cases while User Role Editor Pro was active.
* Fix: Admin menu access add-on: "block not selected model" did not allow to delete users and use other core WordPress functionality at "users.php' page redirecting user to the 1st available admin menu item.
 

= [4.33] 03.04.2017 =
* Core version: 4.32.3
* New: Content view restrictions add-on: authors list and own data only options were added for roles.
* Fix: Content view restrictions add-on: 
*   - filter by categories may work incorrectly due to mistake in the SQL query;
*   - content-view-restrictions-controller.php used not existed function URE_Lib_Pro::filter_int_array().
* Update: Admin menu access add-on: parameters added by 'Enable media replace' plugin were registered as allowed for upload.php link. Earlier 'Replace' link was blocked with a redirection to the 1st available menu item.
* Fix: Admin menu access add-on: "Block not selected" model: 
*   - search a user at "Users" page was finished by the automatic redirection to the 1st available menu item (Dashboard, etc.). The list of allowed parameters for 'Users' page was extended for the search and sort parameters used at this page by WordPress core.
*   - selection of 'Media Library->Add new' menu item was resulted by removing of 'Upload Files' tab at a dialog opened by "Add Media" button from the post/page editor screen.
* Fix: Bulk grant to users multiple roles JavaScript code is loaded now for users.php page only, not globally.
* Fix: nonexistent html_esc__() function was called instead of valid esc_html__() one at pro/includes/classes/posts-edit-access-bulk-action.php file.
* Fix: "Users->Grant Roles" button did not work with switched off option "Count Users without role" at "Settings->User Role Editor->Additional Modules" tab. "JQuery UI" library was not loaded.
* Fix: Boolean false was sent to WordPress core wp_enqueue_script() function as the 2nd parameter instead of an empty string. We should respect the type of parameter which code author supposed to use initially.
* Update: minimal PHP version was raised to 5.3.

= [4.32.3] 10.03.2017 =
* Core version: 4.32.1
* New: Button "Grant Roles" allows to "Assign multiple roles to the selected users" directly from the "Users" page.
* Update: singleton template was applied to the main class User Role Editor Pro. While GLOBALS['user-role-editor'] reference to the instance of User_Role_Editor_Pro class is still available for the compatibility reasons, call to User_Role_Editor_Pro::get_instance() is the best way now to get a reference to the instance of User_Role_Editor_Pro class.
* Fix: Content view restrictions add-on: PHP notice "Undefined index: ure_post_access_error_action in content-view-restrictions-controller.php" was removed.
* Fix: 'unfiltered_html' capability was added to the 'General' capabilities group.

= [4.32.2] 10.02.2017 =
* Core version: 4.31.1
* Fix: Content view restrictions add-on: restrictions were applied too early, some theme or plugin could replace 'access error' message from URE with original protected content. 
* Fix: Posts edit restrictions add-on: User with restrictions saws a full list of Media Library items in case he did not have own attachments in the list of allowed posts, minor code enhancements.
* Fix: It's possible to translate license key states: "Active, Expired, Invalid".
* Fix: Admin menu access add-on: Code responsible for a legacy data format conversion was excluded.

= [4.32.1] 07.01.2017 =
* Core version: 4.31.1
* Fix: Plugins access add-on: User with 'activate_plugins' capability but empty allowed plugins list did not see any plugins. When a restriction is not set, user should see a full plugins list.
* Update: Front-end menu access add-on: It works now according to the given permissions, if current user is a site admin too.
* Update: Posts edit access add-on: It's possible to modify posts/pages, custom post type ID list via filter 'ure_edit_posts_access_id_list'. ID list is a comma separated list of integers.

= [4.32] 06.01.2017 =
* Core version: 4.31.1
* New: Plugins access add-on: 
- It's possible to restrict access to the list of plugins available for activation/deactivation for the role.
- It's possible to change selection model: allow access to the selected or not selected plugins.
* Fix: bbPress roles changes were not saved.
* Fix: Admin menu access add-on: List of allowed URL parameters checked under "blocked not selected" model was extended for parameters used by "Gravity Forms" plugin.
* Fix: WP transients get/set were removed from URE_Own_Capabilities class. It leaded to the MySQL deadlock in some cases.
* Update: Base_Lib::get_request_var() sanitizes user input by PHP's filter_var() in addition to WordPress core's esc_attr().

= [4.31.1] 17.12.2016 =
* Core version: 4.31
* Fix: Admin menu access add-on: Blocked menu was not hidden in some cases.
* Fix: Gravity form access add-on: invalid redirection took place for many 'admin.php?page=some-page' URLs.
* New: Admin menu access add-on: 'ure_admin_menu_access_admin_bar' filter allows to extend the list of top admin menu bar items which could be hidden if their main admin menu was blocked.

= [4.31] 15.12.2016 =
* Core version: 4.31
* New: It's possible to remove unused user capabilities by list.
* Fix: There was no support for installations with a hidden/changed URL to wp-admin. URE uses 'admin_url()' now to get and check admin URL, instead of direct comparing URL with 'wp-admin' string.
* Fix: Admin menu access add-on: custom links under Settings menu were converted from /wp-admin/options-general.php?page=some-key to /wp-admin/some-key incorrectly in some cases.
* Fix: Front-end menu access add-on: access controls were shown at menu editing form for user without 'ure_front_end_menu_access' capability.
* Update: Admin menu access add-on: Contact Form 7 plugin URL parameters are supported to exclude URL with unknown parameters blocking and automatic redirection to dashboard.
* Update: Capability groups CSS classes are prefixed with 'ure-' in order to minimize possible CSS conflicts with other plugins/themes which may load styles with the same classes and break URE's markup.

= [4.30] 01.12.2016 =
* Core version: 4.30
* New: "Granted Only" checkbox to the right from "Quick Filter" input control allows to show only granted capabilities for the selected role.
* New: Front-end menu access add-on is available. It's possible to show menu items for everyone, logged-in users, logged-in users with selected role(s), not-logged-in visitors.
* Update: Admin menu access add-on: Top level menu items list is ordered similar way as WordPress itself uses. 
* Fix: Content view restrictions add-on: Content of a post prohibited for logged-in user with the selected role only was not shown for the not logged-in users. But it should be shown until this post is not prohibited apparently for the 'no_role' (No role for this site) virtual role too.
* Fix: Admin menu access add-on: There was automatic redirect to admin dashboard after WooCommerce->Coupons->Add New (or similar) button click under "Block menu items: Not Selected" model, in case "Posts->Add New" was not allowed.


Click [here](http://role-editor.com/changelog)</a> to look at [the full list of changes](http://role-editor.com/changelog) of User Role Editor plugin.
