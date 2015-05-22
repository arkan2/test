<?php

namespace Core\Bundle\ServiceBundle\Service;

class ServiceManager
{
    protected $verifySsl = true;

    protected $baseUrl = '';

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
    public function __construct($apiProto, $apiHost, $apiEndpoint)
    {
        $this->baseUrl = $apiProto.'://'.$apiHost.'/'.$apiEndpoint;
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
            'oauth_url' => $oauthProto.'://'.$oauthHost.'/'.$oauthEndpoint,
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
        return $this->baseUrl;
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

}