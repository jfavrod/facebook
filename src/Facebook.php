<?php
namespace Epoque\Facebook;


/**
 * Facebook
 * 
 * An object for working with the Facebook Graph API.
 * Each Facebook Object represents a node in the API's terms.
 */

class Facebook
{
    protected $config = [];
    
    protected $defaults = [
        'gapi' => 'https://graph.facebook.com',
        'version' => 'v2.8',
        'id' => '',
        'secret' => '',
        'node' => 'me',
        'user_agent' => 'Epoque\\Facebook ()'
    ];

    
    /**
     * Configures the Facebook Object.
     * 
     * @param assoc_array|NULL $spec The key => value configuration
     * details for the Facebook Object. If $spec is omitted, all the
     * $defaults used. If provided, whatever given $key => value pairs
     * will override defaults, and whichever not given, $defaults used.
     */
    
    public function __construct($spec=[])
    {
        foreach ($this->defaults as $key => $val) {
            $this->config[$key] = $this->defaults[$key];
        }
        
        if (is_array($spec) && !empty($spec)) {
            foreach ($spec as $key => $val) {
                if (array_key_exists($key, $this->config)) {
                    $this->config[$key] = $val;
                }
            }
        }
    }

    
    /**
     * Used to generate an access token needed for
     * Facebook Graph API calls.
     * 
     * @return string A Facebook Graph API App Access Token.
     */
    
    protected function appAccessToken()
    {
        $url  = $this->config['gapi'].'/'.$this->config['version'];
        $url .= '/oauth/access_token';
        $url .= '?client_id='.$this->config['id'];
        $url .= '&client_secret='.$this->config['secret'];
        $url .= '&grant_type=client_credentials';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_USERAGENT, $this->config['user_agent']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_URL, $url);

        $stdClass = json_decode(curl_exec($curl));
        return $stdClass->access_token;
    }


    /**
     * @return stdClass Containing stdClass objects which contain
     * Facebook posts. Each can contain the following fields:
     * 
     * message => The content of a user's post on Facebook.
     * created_time => The time the post was created.
     * id => Identification string.
     */
    
    public function getPosts()
    {
        $url  = $this->config['gapi'] . '/' .$this->config['version'];
        $url .= '/'. $this->config['node'];
        $url .= '?fields=posts';
        $url .= '&access_token=' . $this->appAccessToken();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_USERAGENT, $this->config['user_agent']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_URL, $url);

        $response = json_decode(curl_exec($curl));
        return $response->posts->data;
    }
}

