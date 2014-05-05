<?php
/**
 * Class CardpayAPI ver 1.0.2
 * http://www.cardpay.com
 *
 * encapsulates Cardpay API functionality
 */
class CardpayAPI
{
    private $order;
    private $config;

    /**
     * Object constructor
     *
     * @param $order array Order parameters
     * @param $config array Config array
     */
    public function __construct($config, $order = array())
    {
        $this->config = $config;
        $this->order = $order;
    }

    /**
     * Converts array to DOM XML node
     *
     * @param $array
     * @param $node
     */
    public static function array2xml($array, &$node)
    {
        foreach($array as $key => $value) {
            if(is_array($value)) {
                $new_node = $node->ownerDocument->createElement(is_numeric($key) ? 'item' : $key);
                $node->appendChild($new_node);
                self::array2xml($value, $new_node);
            } else {
                $node->setAttribute($key,$value);
            }
        }
    }

    /**
     * Executes a post request
     *
     * @param $url string URL
     * @param $params array Posted variables
     * @return string|bool Request result or false on failure
     */
    public static function postRequest($url, $params)
    {
        $c = curl_init ($url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($c, CURLOPT_FAILONERROR,1);
        curl_setopt($c, CURLOPT_POST,1);
        curl_setopt($c, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
        $page = curl_exec($c);
        $http_status_code = curl_getinfo($c, CURLINFO_HTTP_CODE);
        curl_close($c);
        return (int)$http_status_code >= 300 ? false : $page;
    }

    /**
     * Converts Cardpay XML to an array
     *
     * @param $xml_string string XML string
     * @return array
     */
    public static function getXMLAsArray($xml_string)
    {
        $result = array();
        $xml = new DOMDocument('1.0', 'utf-8');
        if (@$xml->loadXML($xml_string)) {
            $elem = $xml->documentElement;
            if ($elem->hasAttributes()) {
                $attrs = $elem->attributes;
                foreach ($attrs as $attr) {
                    $result[$elem->nodeName][$attr->name] = $attr->value;
                }
            }
        }
        return $result;
    }

    /**
     * Returns order XML
     *
     * @return string Order XML
     */
    public function getOrderXML()
    {
        $xml = new DOMDocument('1.0', 'utf-8');
        $order = $xml->createElement('order');
        self::array2xml($this->order,$order);
        return $xml->saveXML($order);
    }

    /**
     * Returns order XML encoded
     *
     * @param $xml string String to encode
     * @return string Encoded string
     */
    public function getOrderXMLEncoded($xml)
    {
        return base64_encode($xml);
    }

    /**
     * Returns sha512
     *
     * @param $xml string String to generate hash
     * @return string Hash
     */
    public function getSHA512($xml)
    {
        return $sha512 = hash('sha512', $xml.$this->config['secret_word']);
    }

    /**
     * Generates Form HTML from the template.
     *
     * The following placeholders are replaced with the actual parameters:
     * %cardpay_orderxml% - Order XML encoded
     * %cardpay_sha512% - sha512
     *
     * @param $html string Form HTML template
     * @return string Form HTML
     */
    public function paymentForm($html)
    {
        $xml = $this->getOrderXML();
        $html = str_replace('%cardpay_url%',$this->config['url'],$html);
        $html = str_replace('%cardpay_orderxml%',$this->getOrderXMLEncoded($xml),$html);
        $html = str_replace('%cardpay_sha512%',$this->getSHA512($xml),$html);
        return $html;
    }

    /**
     * Redirects to payment page
     */
    public function redirectToPaymentPage()
    {
        $xml = $this->getOrderXML();
        header('Location: '.$this->config['url'].'?orderXML='.urlencode($this->getOrderXMLEncoded($xml)).'&sha512='.urlencode($this->getSHA512($xml)));
        exit;
    }

    /**
     * Process gateway payment and returns result of payment
     *
     * @return string Result
     */
    public function getewayPayment()
    {
        $xml = $this->getOrderXML();
        return self::postRequest($this->config['url'],array(
            'orderXML' => $this->getOrderXMLEncoded($xml),
            'sha512' => $this->getSHA512($xml),
        ));
    }

    /**
     * Generates 3ds Form HTML from the template.
     *
     * @param $params array|string Result of gateway payment as array or string. String will be converted to array internally.
     * @param $html string HTML form template
     * @return string HTML Form
     */
    public function get3dsForm($params,$html)
    {
        if (!is_array($params))
            $params = self::getXMLAsArray($params);

        if (!empty($params['redirect']['url']))
            $html = str_replace('%cardpay_url%',$params['redirect']['url'],$html);

        if (!empty($params['redirect']['MD'])) {
            $html = str_replace('%cardpay_md%',$params['redirect']['MD'],$html);
            $html = str_replace('%cardpay_sha512%',$this->getSHA512($params['redirect']['MD']),$html);
        }

        if (!empty($params['redirect']['PaReq']))
            $html = str_replace('%cardpay_pareq%',$params['redirect']['PaReq'],$html);

        if (!empty($this->config['return_url']))
            $html = str_replace('%cardpay_termurl%',$this->config['return_url'],$html);

        return $html;
    }

    /**
     * Makes final 3ds request
     *
     * @param $params array Params
     * @return string|bool Request result or false on failure
     */
    public function final3ds($params)
    {
        return self::postRequest($this->config['url'],$params);
    }

}
