<?php

namespace Core\Bundle\ServiceBundle\Service;

class ServiceManager
{
    protected $verifySsl = true;

    protected $baseUrl = '';

    protected $version = 'v1.0';

    protected $oauthConfig = array(
        'oauth_url' => '',
        'oauth_client_id' => '',
        'oauth_client_secret' => '',
        'oauth_scope' => ''
    );

    /**
     * @param $apiProto
     * @param $apiHost
     * @param $apiEndpoint
     */
    public function __construct($apiProto, $apiHost, $apiEndpoint, $version = '')
    {
        $this->baseUrl = $apiProto.'://'.$apiHost.$apiEndpoint;
        if(!empty($version)) {
            $this->setVersion($version);
        }
    }

    /**
     * @param $oauthProto
     * @param $oauthHost
     * @param $oauthEndpoint
     * @param $oauthClientId
     * @param $oauthClientSecret
     */
    public function configureOauth($oauthProto, $oauthHost, $oauthEndpoint, $oauthClientId, $oauthClientSecret, $oauthScope) {
        $this->oauthConfig = array_merge($this->oauthConfig, array(
            'oauth_base_url' => $oauthProto.'://'.$oauthHost,
            'oauth_token_url' => $oauthEndpoint,
            'oauth_client_id' => $oauthClientId,
            'oauth_client_secret' => $oauthClientSecret,
            'oauth_scope' => $oauthScope
        ));
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl.$this->version.'/';
    }

    /**
     * @return array
     */
    public function getOauthConfig($id)
    {
        if(!empty($id)) {
            if(isset($this->oauthConfig[$id])) {
                return $this->oauthConfig[$id];
            }
        }
        return $this->oauthConfig;
    }

    /**
     * @return boolean
     */
    public function isVerifySsl()
    {
        return $this->verifySsl;
    }

    /**
     * @param boolean $verifySsl
     */
    public function setVerifySsl($verifySsl)
    {
        $this->verifySsl = $verifySsl;
    }


    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

}