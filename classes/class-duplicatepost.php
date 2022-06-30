<?php

if (!defined('ABSPATH'))
	exit;

if (!class_exists('wtb_Duplicate_post')) {
	class wtb_Duplicate_post
	{
		/**
		 * @param string $status 	  发布状态
		 * @param string $redirect  发布后进入的页面地址
		 * @return void
		 */
		public function __construct($status = null, $redirect = null)
		{
			$this->status   = $status;
			$this->redirect = $redirect;

			add_filter('post_row_actions', [$this, 'link'], 10, 2);
			add_filter('page_row_actions', [$this, 'link'], 10, 2);
			add_action('admin_action_duplicate_post', [$this, 'duplicate']);
		}

		public function duplicate()
		{
			global $wpdb;

			if (!(isset($_GET['post']) || isset($_POST['post'])  || (isset($_REQUEST['action']) && 'duplicate_post' == $_REQUEST['action']))) {
				wp_die(__('请提供要复制的帖子！', 'wtb'));
			}

			if (!isset($_GET['duplicate_nonce']) || !wp_verify_nonce($_GET['duplicate_nonce'], basename(__FILE__)))
				return;

			$id     = (isset($_GET['post']) ? absint($_GET['post']) : absint($_POST['post']));
			$post   = get_post($id);
			$status = $this->status ? $this->status : 'draft';

			if (isset($post) && $post != null) {
				$new_id = wp_insert_post([
					'post_content'   => $post->post_content,
					'post_title'     => $post->post_title,
					'post_excerpt'   => $post->post_excerpt,
					'post_status'    => $status,
					'post_type'      => $post->post_type,
					'comment_status' => $post->comment_status,
					'ping_status'    => $post->ping_status,
					'post_password'  => $post->post_password,
					'post_name'      => $post->post_name,
					'to_ping'        => $post->to_ping,
					'post_parent'    => $post->post_parent,
					'menu_order'     => $post->menu_order,
				]);

				$taxonomies = get_object_taxonomies($post->post_type);
				foreach ($taxonomies as $taxonomy) {
					$terms = wp_get_object_terms($id, $taxonomy, ['fields' => 'slugs']);
					wp_set_object_terms($new_id, $terms, $taxonomy, false);
				}

				$post_db = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$id");
				if (count($post_db) != 0) {
					$sql = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
					foreach ($post_db as $meta) {
						$key = $meta->meta_key;
						if ($key == '_wp_old_slug') continue;
						$value = addslashes($meta->meta_value);
						$sql_sel[] = "SELECT $new_id, '$key', '$value'";
					}
					$sql .= implode(" UNION ALL ", $sql_sel);
					$wpdb->query($sql);
				}

				$url = $this->redirect ? $this->redirect : 'post.php?action=edit&post=' . $new_id;

				wp_redirect(admin_url($url));
				exit;
			} else {
				wp_die(__('帖子创建失败，找不到原始帖子！', 'wtb'));
			}
		}

		public function link($actions, $post)
		{
			if (current_user_can('edit_posts')) {
				$dutext = __('复制', 'wtb');
				$actions['duplicate'] = "<a href=" . wp_nonce_url('admin.php?action=duplicate_post&post=' . $post->ID, basename(__FILE__), 'duplicate_nonce') . ">{$dutext}</a>";
			}
			return $actions;
		}
	}
}

new wtb_Duplicate_post;
