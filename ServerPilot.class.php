<?php

/**
 * ServerPilot API Client
 * @package serverpilot
 * @version 0.0.1
 * @author  https://github.com/usefulz
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @see     https://github.com/usefulz/serverpilot-api-client
 */

class ServerPilot
{

  /**
   * Client ID
   * @type string $client_id ServerPilot Client ID
   * @see https://manage.serverpilot.io/#account
   */

  public $client_id = '';

  /**
   * API Token
   * @access private
   * @type string $api_token ServerPilot API token
   * @see https://manage.serverpilot.io/#account
   */

  private $api_token = '';

  /**
   * API Endpoint
   * @access public
   * @type string URL for ServerPilot API
   */

  public $endpoint = 'https://api.serverpilot.io/v1/';

  /**
   * Current Version
   * @access public
   * @type string Current version number
   */

  public $version = '0.0.1';

  /**
   * User Agent
   * @access public
   * @type string API User-Agent string
   */

  public $agent = 'ServerPilot API Client';

  /**
   * Debug Variable
   * @access public
   * @type bool Debug API requests
   */

  public $debug = TRUE;

  /**
   * Servers Variable
   * @access public
   * @type mixed Array to store Server IDs
   */

  public $servers   = array();

  /**
   * Users Variable
   * @access public
   * @type mixed Array to store Users list
   */

  public $users   = array();

  /**
   * Actions Variable
   * @access public
   * @type array Array to store Action ID responses
   */

  public $actions = array();

  /**
   * Constructor function
   * @param string $token
   * @return void
   */

  public function __construct($client_id, $token)
  {
    $this->client_id = $client_id;
    $this->api_token = $token;
    $this->servers   = self::list_servers();
    $this->users     = self::list_users();
    $this->apps      = self::list_apps();
    $this->databases = self::list_databases();
  }

  /**
   * List servers
   * @see https://github.com/ServerPilot/API#list-all-servers
   * @return mixed
   */

  public function list_servers()
  {
    return self::get('servers');
  }

  /**
   * List users
   * @see https://github.com/ServerPilot/API#list-all-system-users
   * @return mixed
   */

  public function list_users()
  {
    return self::get('sysusers');
  }

  /**
   * List apps
   * @see https://github.com/ServerPilot/API#list-all-apps
   * @return mixed
   */

  public function list_apps()
  {
    return self::get('apps');
  }

  /**
   * List databases
   * @see https://github.com/ServerPilot/API#list-all-databases
   * @return mixed
   */

  public function list_databases()
  {
    return self::get('dbs');
  }

  /**
   * Connect a new server
   * @param string $name The nickname of the server [\w\d\-\.]{1,255} (generally a hostname)
   * @return mixed
   */

  public function server_create($name)
  {
    $args = array('name' => $name);
    return self::post('servers', $args);
  }

  /**
   * Get server data
   * @param string $server_id The ID of the server
   * @return mixed
   */

  public function server_get($server_id)
  {
    return self::get('servers/'.$server_id);
  }

  /**
   * Delete server
   * @param string $server_id The ID of the server
   * @return mixed
   */

  public function server_delete($server_id)
  {
    $path = 'servers/'.$server_id;
    return self::custom($path, 'DELETE');
  }

  /**
   * Update server
   * @param string $server_id The ID of the server
   * @return mixed
   * @todo firewall => true, autoupdates => true
   */

  public function server_update($server_id)
  {
    return self::post('servers/'. $server_id);
  }

  /**
   * Create user
   * @param string $server_id Server ID
   * @param string $name Username
   * @param string $password Password 
   */

  public function user_create($server_id, $name, $password)
  {
    $args = array(
      'serverid' => $server_id,
      'name' => $name,
      'password' => $password
    );
    return self::post('sysusers', $args);
  }

  /**
   * Get user data
   * @param string $user_id The ID of the user
   * @return mixed
   */

  public function user_get($user_id)
  {
    return self::get('sysusers/'.$user_id);
  }

  /**
   * Delete user
   * @param string $user_id The ID of the user
   * @return mixed
   */

  public function user_delete($user_id)
  {
    $path = 'sysusers/'.$user_id;
    return self::custom($path, 'DELETE');
  }

  /**
   * Change password for user
   * @param string $user_id User ID
   * @param string $password Password 
   */

  public function user_passwd($user_id, $password)
  {
    $args = array('password' => $password);
    return self::post('sysusers/' . $user_id, $args);
  }

  /**
   * Create App
   * @param string $name Name of application
   * @param string $user_id Application owner
   * @param string $runtime Version of PHP (php5.4, php5.5, php5.6)
   * @param array  $domains Domains to configure
   * @return mixed
   */

  public function apps_create($name, $user_id, $runtime, $domains)
  {
    $args = array(
      'name' => $name,
      'sysuserid' => $user_id,
      'runtime' => $runtime,
      'domains' => $domains
    );
    return self::post('apps', $args);
  }

  /**
   * Retrieve App
   * @param string $app_id Application ID
   * @return mixed
   */

  public function apps_get($app_id)
  {
    return self::get('apps/'.$app_id);
  }

  /**
   * Delete App
   * @param string $app_id Application ID
   * @return mixed
   */

  public function apps_delete($app_id)
  {
    $path = 'apps/'.$app_id;
    return self::custom($path, 'DELETE');
  }

  /**
   * Update App
   * @param string $app_id Application ID
   * @param string $runtime PHP Version (php5.4, php5.5, php5.6)
   * @param array  $domains Domains to configure
   */

  public function apps_update($app_id, $runtime, $domains)
  {
    $args = array(
      'runtime' => $runtime,
      'domains' => $domains
    );
    return self::get('apps/'.$app_id, $args);
  }

  /**
   * Add SSL Certificate
   * @param string $app_id Application ID
   * @param string $key SSL Private Key
   * @param string $cert SSL Certificate
   * @param string $ca_cert SSL CA Certificate
   * @return mixed
   */

  public function ssl_add($app_id, $key, $cert, $ca_certs = null)
  {
    $args = array(
      'key' => $key,
      'cert' => $cert,
      'cacerts' => $ca_certs
    );
    return self::post('apps/'.$app_id.'/ssl', $args);
  }

  /**
   * Delete SSL Certificate
   * @param string $app_id Application ID
   * @return mixed
   */

  public function ssl_delete($app_id)
  {
    $path = 'apps/'.$app_id.'/ssl';
    return self::custom($path, 'DELETE');
  }

  /**
   * Create Database
   * @param string $app_id Application ID
   * @param string $name   Database name
   * @param json   $user
   *   @param string $user['name']     Username
   *   @param string $user['password'] Password
   * @return mixed
   */

  public function database_create($app_id, $name, $user)
  {
    $args = array(
      'appid' => $app_id,
      'name'  => $name,
      'user'  => json_encode($user)
    );
    return self::post('dbs', $args);
  }

  /**
   * Retrieve Database
   * @param string $database_id Database ID
   * @return mixed
   */

  public function database_get($database_id)
  {
    return self::get('dbs/'.$database_id);
  }

  /**
   * Delete Database
   * @param string $database_id Database ID
   * @return mixed
   */

  public function database_delete($database_id)
  {
    $path = 'dbs/'.$database_id;
    return self::custom($path, 'DELETE');
  }

  /**
   * Update Database Credentials
   * @param string Database ID
   * @param json User object
   *   @param string $user['id']       Database User ID
   *   @param string $user['password'] Database password
   * @return mixed
   */

  public function database_update($database_id, $user)
  {
    $args = json_encode($user);
    $path = 'dbs/'.$database_id;
    return self::post($path, $args);
  }

  /**
   * Get Action Status
   * @param string $action_id Action ID
   * @return mixed
   */

  public function action_status($action_id)
  {
    return self::get('actions/'.$action_id);
  }

  /**
   * Get request
   * @param string $method URL
   * @param array  $args   Query arguments
   * @return mixed
   */

  private function get($method, $args = FALSE)
  {
    $this->request_type = 'GET';
    return $this->query($method, $args);
  }

  /**
   * POST Method
   * @param string $method
   * @param mixed $args
   * @return mixed
   */

  private function post($method, $args)
  {
    $this->request_type = 'POST';
    return $this->query($method, $args);
  }

  /**
   * Custom Request Method
   * @param string $method Currently supported is GET/POST/DELETE
   * @param mixed $args
   * @return mixed
   */

  private function custom($method, $request_type, $args = null)
  {
    $this->request_type = $request_type;
    return $this->query($method, $args);
  }

  /**
   * API Query Function
   * @param string $method
   * @param mixed $args
   */

  private function query($method, $args)
  {

    $url = $this->endpoint . $method;

    if ($this->debug) echo $this->request_type . ' ' . $url . PHP_EOL;

    $_defaults = array(
      CURLOPT_USERAGENT => sprintf('%s v%s (%s)', $this->agent, $this->version, 'https://github.com/usefulz/serverpilot-api-client'),
      CURLOPT_HEADER => 0,
      CURLOPT_VERBOSE => 0,
      CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
      CURLOPT_USERPWD => sprintf('%s:%s', $this->client_id, $this->api_token),
      CURLOPT_SSL_VERIFYPEER => 1,
      CURLOPT_SSL_VERIFYHOST => 1,
      CURLOPT_HTTP_VERSION => '1.0',
      CURLOPT_FOLLOWLOCATION => 0,
      CURLOPT_FRESH_CONNECT => 1,
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_FORBID_REUSE => 1,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTPHEADER => array('Accept: application/json', 'Content-type: application/json'),
    );

    switch($this->request_type)
    {

      case 'POST':
        $post_data = http_build_query($args);
        $_defaults[CURLOPT_URL] = $url;
        $_defaults[CURLOPT_POST] = 1;
        $_defaults[CURLOPT_POSTFIELDS] = json_encode($post_data);
      break;

      case 'GET':
        if ($args !== FALSE)
        {
          $get_data = http_build_query($args);
          $_defaults[CURLOPT_URL] = $url . '&' . $get_data;
        } else {
          $_defaults[CURLOPT_URL] = $url;
        }
      break;

      default:
        $_defaults[CURLOPT_CUSTOMREQUEST] = $this->request_type;
      break;
    }

    $apisess = curl_init();
    curl_setopt_array($apisess, $_defaults);
    $response = curl_exec($apisess);

    /**
     * Check to see if there were any API exceptions thrown
     * If so, then error out, otherwise, keep going.
     */

    try
    {
      self::isAPIError($apisess, $response);
    }
    catch(Exception $e)
    {
      curl_close($apisess);
      die($e->getMessage() . PHP_EOL);
    }


    /**
     * Close our session
     * Return the decoded JSON response
     */

    curl_close($apisess);
    $obj = json_decode($response, true);

    if (array_key_exists('actionid', $obj))
    {
      $this->actions[] = array($obj['actionid'] => $obj['data']);
    }

    return $obj['data'];
  }

  /**
   * API Error Handling
   * @param  cURL_Handle $response_obj
   * @param  string      $response
   * @throws Exception   if 400-500 series errors occur
   * @throws Exception   if error message is present
   */

  public function isAPIError($response_obj, $response)
  {
    $code = curl_getinfo($response_obj, CURLINFO_HTTP_CODE);

    if ($this->debug) echo $code . PHP_EOL;

    switch($code)
    {
      case 200: break;
      case 400: throw new Exception('We could not understand your request. Typically missing a parameter or header.'); break;
      case 401: throw new Exception('Either no authentication credentials were provided or they are invalid.'); break;
      case 402: throw new Exception('Method is restricted to users on the Coach or Business plan.'); break;
      case 403: throw new Exception('Typically when trying to alter or delete protected resources.'); break;
      case 404: throw new Exception('You requested a resource that does not exist.'); break;
      case 409: throw new Exception('Typically when trying creating a resource that already exists.'); break;
      case 500: throw new Exception('Internal server error. Try again at a later time'); break;
      default:  break;
    }

    $check_error = json_decode($response, true);

    if (array_key_exists('error', $check_error))
    {
      throw new Exception($check_error['error']['message']);
    }

  }

}
?>
