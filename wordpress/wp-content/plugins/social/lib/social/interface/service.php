<?php
/**
 * An interface that must be used by services that want to hook onto the plugin.
 *
 * @package Social
 */
interface Social_IService {

	/**
	 * The max length a post can be when broadcasted.
	 *
	 * @abstract
	 * @return void
	 */
	function max_broadcast_length();

	/**
	 * Adds multiple accounts to the service.
	 *
	 * @abstract
	 * @param  array  $accounts
	 * @return array
	 */
	function accounts(array $accounts = null);

	/**
	 * The account to us for this service.
	 *
	 * @abstract
	 * @param  object  $account  user's account
	 * @return void
	 */
	function account($account);

	/**
	 * Executes the request for the service.
	 *
	 * @abstract
	 * @param  int|object  $account  account to use
	 * @param  string      $api      API endpoint to request
	 * @param  array       $params   parameters to pass to the API
	 * @param  string      $method   GET|POST, default: GET
	 * @return array
	 */
	function request($account, $api, array $params = array(), $method = 'GET');

	/**
	 * Creates a WordPress User
	 *
	 * @abstract
	 * @param  int|object  $account  account to use to create the WP account
	 * @return int
	 */
	function create_user($account);

	/**
	 * Returns the disconnect URL.
	 *
	 * @static
	 * @abstract
	 * @param  object  $account
	 * @param  bool    $is_admin
	 * @param  string  $before
	 * @param  string  $after
	 * @return string
	 */
	function disconnect_url($account, $is_admin = false, $before = '', $after = '');

	/**
	 * Formats the provided content to the defined tokens.
	 *
	 * @abstract
	 * @param  object  $post
	 * @param  string  $format
	 * @return string
	 */
	function format_content($post, $format);

	/**
	 * Updates a user's status on the service.
	 *
	 * @abstract
	 * @param  int|object  $account
	 * @param  string      $status  status message
	 * @return void
	 */
	function status_update($account, $status);

	/**
	 * Returns the URL to the user's account.
	 *
	 * @abstract
	 * @param  object  $account
	 * @return string
	 */
	function profile_url($account);

	/**
	 * Returns the user's display name.
	 *
	 * @abstract
	 * @param  object  $account
	 * @return string
	 */
	function profile_name($account);

	/**
	 * Builds the user's avatar.
	 *
	 * @param  int|object  $account
	 * @param  int         $comment_id
	 * @return string
	 */
	function profile_avatar($account, $comment_id = null);

	/**
	 * Searches the service to find any replies to the blog post.
	 *
	 * @param  object      $post
	 * @param  array       $urls
	 * @param  array|null  $broadcasted_ids
	 * @return array|bool
	 */
	function search_for_replies($post, array $urls, $broadcasted_ids = null);

	/**
	 * Saves the replies as comments.
	 *
	 * @abstract
	 * @param  int    $post_id
	 * @param  array  $replies
	 * @return void
	 */
	function save_replies($post_id, array $replies);

	/**
	 * Builds the status URL.
	 *
	 * @abstract
	 * @param  string  $username
	 * @param  int     $status_id
	 * @return string
	 */
	function status_url($username, $status_id);

} // End Social_Service_Interface
