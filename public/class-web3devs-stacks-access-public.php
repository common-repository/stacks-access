<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://stacksaccess.com
 * @since      1.0.0
 *
 * @package    Web3devs_Stacks_Access
 * @subpackage Web3devs_Stacks_Access/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Web3devs_Stacks_Access
 * @subpackage Web3devs_Stacks_Access/public
 * @author     Web3devs <wordpress@web3devs.com>
 */
class Web3devs_Stacks_Access_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_filter( 'request', array( $this, 'handleCallback' ));
		add_action( "the_content", array( $this, 'handleContentAccess' ));
		add_filter( "get_the_excerpt", array( $this, 'handleExcerptAccess' ));
		add_filter( "comments_open", array( $this, 'handleCommentsAccess' ));
		add_filter( "wp_list_comments_args", array( $this, 'handleCommentsListAccess' ));
		add_filter( "get_comments_number", array( $this, 'handleCommentsNumberAccess' ));
		add_action( 'add_meta_boxes', array($this, 'addWeb3devsStacksAccessBox'));
		add_action( 'save_post', array($this, 'handleSavePost'));

		add_action('init', array($this, 'registerStacksSession'));
	}

	public function registerStacksSession() {
		if (!session_id()) {
			session_start();
		}
	}

	public function addWeb3devsStacksAccessBox( ) {
		 add_meta_box(
            'web3devs_stacks_access_box_id',
            'Stacks Access',
            array($this, 'renderWeb3devsStacksAccessBox'),
			'',             // 'post', // leave empty to add to all post types
			// 'side',
        );
	}

	public function renderWeb3devsStacksAccessBox( $post ) {
		$stacks_access = get_post_meta($post->ID, '_web3devs_stacks_access_meta_key', true);
		$coins = get_option('web3devs_stacks_access_configured_coins_setting');
	?>
		<label for="web3devs_stacks_access_field">Stacks coin restriction:</label>
		<select name="web3devs_stacks_access_field" id="web3devs_stacks_access_field" class="postbox">
			<option value="">No token required...</option>
			<?php foreach ($coins as $coin): ?>
				<option value="<?php echo esc_attr($coin['contract']); ?>" <?php echo ($coin['contract'] == $stacks_access) ? 'selected' : '' ?>><?php echo esc_html($coin['symbol']); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	public function handleSavePost($post_id) {
		if (array_key_exists('web3devs_stacks_access_field', $_POST)) {
			update_post_meta(
				$post_id,
				'_web3devs_stacks_access_meta_key',
				$_POST['web3devs_stacks_access_field']
			);
		}
	}


	private function decodeSignature($hash, $sig, $network) {
		$url = 'https://api.staging.rootpayments.com/tools/decode-stacks-address';
		$body = json_encode(array(
			'hash' 			 => $hash,
			'signature' 	 => $sig,
			'stacks_network' => $network,
		));

		$options = [
			'body'        => $body,
			'headers'     => [
				'Content-Type' => 'application/json',
			],
			'timeout'     => 60,
			'redirection' => 5,
			'blocking'    => true,
			'httpversion' => '1.1',
			'sslverify'   => false,
			'data_format' => 'body',
		];
 
		$resp = wp_remote_post($url, $options);
		if (is_wp_error($resp)) {
			die("Error: call to URL $url failed with status $status, response $resp");
		}

		return json_decode(wp_remote_retrieve_body($resp), true);
	}

	private function getWallet($address) {
		$api = 'https://stacks-node-api.testnet.stacks.co/extended/v1/address/{ADDRESS}/balances';
		if (str_starts_with($address, 'SP')) {
			$api = 'https://stacks-node-api.mainnet.stacks.co/extended/v1/address/{ADDRESS}/balances';
		}
		$url = str_replace('{ADDRESS}', $address, $api);

		$options = [
			'headers'     => [
				'Content-Type' => 'application/json',
			],
			'timeout'     => 60,
			'redirection' => 5,
			'blocking'    => true,
			'httpversion' => '1.1',
			'sslverify'   => false,
		];

		$resp = wp_remote_get($url, $options);
		if (is_wp_error($resp)) {
			die("Error: call to URL $url failed with status $status, response $resp");
		}

		return json_decode(wp_remote_retrieve_body($resp), true);
	}

	public function handleCallback($vars) {
		if (isset($_GET['web3devs-stacks-access-callback'])) {
			$req = $entityBody = file_get_contents('php://input');
			$data = json_decode($req, true);

			//Required fields:
			$required = array('signature', 'stacks_network');
			foreach ($required as $field) {
				if (!isset($data[$field]) || strlen($data[$field]) == 0) {
					header('Content-Type: application/json; charset=utf-8');
					echo json_encode(array('error' => 'Missing required field: '.$field));
					exit;
				}
			}

			if(!isset($_SESSION) && !headers_sent()) {
				session_start();
			}

			$hash = hash('sha256', session_id());
			$sig = trim($data['signature']);
			$network = trim($data['stacks_network']);

			$resp = $this->decodeSignature($hash, $sig, $network);
			if (!isset($resp['data']) || strlen($resp['data']) == 0) {
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode(array('error' => 'Could not decode signature.'));
				exit;
			}
			$address = trim($resp['data']);
			$wallet = $this->getWallet($address);
			if (!isset($wallet['stx'])) {
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode(array('error' => 'Could not get STX balance.'));
				exit;
			}

			$balance = ['stx' => $wallet['stx']['balance']];
			foreach ($wallet['fungible_tokens'] as $contract => $values) {
				$balance[$contract] = intval($values['balance']);
			}
			foreach ($wallet['non_fungible_tokens'] as $contract => $values) {
				$balance[$contract] = intval($values['count']);
			}

			$_SESSION['web3devs-stacks-access-address'] = $address;
			$_SESSION['web3devs-stacks-access-tokens'] = $balance;

			$pid = url_to_postid($_SERVER['HTTP_REFERER']);
			if ($pid !== 0) {
				$token = get_post_meta($pid, '_web3devs_stacks_access_meta_key', true);
				if (!$this->checkBalance($token, 1, $balance)) {
					$selected = get_option('web3devs_stacks_access_denial_page_setting');
					$p = get_permalink($selected);
					if ($p) {
						header('Content-Type: application/json; charset=utf-8');
						echo json_encode(array('message' => 'NOTOK', 'redirect' => $p));
						exit;
					}
				}
			}

			header('Content-Type: application/json; charset=utf-8');
			echo json_encode(array('message' => 'OK', 'address' => $address));
			exit;
		}

		return $vars;
	}

	private function renderComponent() {
		if(!isset($_SESSION) && !headers_sent()) {
			session_start();
		}

		global $post;
		$p = get_permalink($post);
		$token = get_post_meta($post->ID, '_web3devs_stacks_access_meta_key', true);
		$network = substr($token, 0, 2 ) === 'ST' ? 'testnet' : 'mainnet';
		$url_parts = parse_url($p);
		$params = [];
		if (isset($url_parts['query'])) {
			parse_str($url_parts['query'], $params);
		}
		$params['web3devs-stacks-access-callback'] = '';
		$url_parts['query'] = http_build_query($params);
		$port = isset($url_parts['port']) ? ':'.$url_parts['port'] : '';
		$callback_url = $url_parts['scheme'].'://'.$url_parts['host'].$port.$url_parts['path'].'?'.$url_parts['query'];

		$data = [
			'hash' 		=> hash('sha256', session_id()),
			'callback' 	=> $callback_url,
			'network'	=> $network,
			'token'		=> $token,
		];

		return '<stacks-sign hash="'.$data['hash'].'" callback="'.$data['callback'].'" network="'.$data['network'].'" token="'.$data['token'].'">';
	}

	//Check if we our plugin should control this page
	private function shouldControl() {
		global $post;
		$stacks_access = get_post_meta($post->ID, '_web3devs_stacks_access_meta_key', true);
		if (!empty($stacks_access)) {
			return true;
		}

		return false;
	}

	private function checkBalance($token, $amount = 1, $balance = []) {
		if (isset($balance[$token]) && $balance[$token] >= $amount) {
			return true;
		}

		return false;
	}

	private function hasAccess() {
		//Get the required token and amount
		global $post;
		$token = get_post_meta($post->ID, '_web3devs_stacks_access_meta_key', true);
		$amount = 1; //XXX: maybe someday we'll change it to something else :)

		//Start/read session
		if(!isset($_SESSION) && !headers_sent()) {
			session_start();
		}

		//Check if we have a session with tokens
		if (!isset($_SESSION['web3devs-stacks-access-tokens'])) {
			return false;
		}

		if ($this->checkBalance($token, $amount, $_SESSION['web3devs-stacks-access-tokens'])) {
			return true;
		}

		return false;
	}

	public function handleContentAccess($content) {
		if ($this->shouldControl() && !$this->hasAccess()) {
			return $this->renderComponent();
		}

		return $content;
	}

	public function handleExcerptAccess($content) {
		if ($this->shouldControl() && !$this->hasAccess()) {
			return __( 'There is no excerpt because this is a protected post.' );
		}

		return $content;
	}

	public function handleCommentsAccess($open) {
		if ($this->shouldControl() && !$this->hasAccess()) {
			return false;
		}

		return $open;
	}

	public function handleCommentsListAccess($args) {
		if ($this->shouldControl() && !$this->hasAccess()) {
			$args['page'] = -1;
			$args['per_page'] = -1;
			$args['type'] = 'none';
		}

		return $args;
	}

	public function handleCommentsNumberAccess($number) {
		if ($this->shouldControl() && !$this->hasAccess()) {
			return 0;
		}

		return $number;
	}

	
	


	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Web3devs_Stacks_Access_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Web3devs_Stacks_Access_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/web3devs-stacks-access-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Web3devs_Stacks_Access_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Web3devs_Stacks_Access_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/web3devs-stacks-access-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/stack-sign/dist/stacks-sign.js', array( 'jquery' ), $this->version, false );

	}

}
