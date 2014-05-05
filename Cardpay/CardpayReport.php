<?php
/**
 * Class CardpayReport ver 1.0.2
 * http://www.cardpay.com
 *
 * encapsulates Cardpay transaction report functionality
 */
class CardpayReport
{
    private $wallet_id;
    private $client_login;
    private $client_password;
    private $report_url;

    /**
     * Object constructor
     *
     * @param $config array Config parameters
     */
    public function __construct($config)
    {
        $this->wallet_id = $config['wallet_id'];
        $this->client_login = $config['client_login'];
        $this->client_password = $config['client_password'];
        $this->report_url = $config['report_url'];
    }

    /**
     * Get report
     *
     * @param $settings array Report settings
     * @return string Result
     */
    public function getReport($settings = array())
    {
        $settings = array_merge(
            array(
                'wallet_id'=>$this->wallet_id,
                'client_login'=>$this->client_login,
                'client_password'=>$this->client_password,
            ),
            $settings
        );
        return file_get_contents($this->report_url.'?'.http_build_query($settings));
    }
}
