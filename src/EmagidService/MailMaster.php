<?php
/**
 * Created by PhpStorm.
 * User: zhou
 * Date: 3/20/17
 * Time: 4:40 PM
 */
namespace EmagidService;

/**
 * Class MailMaster
 * @package EmagidService
 * @Example
 * $res = (new EmagidService\MailMaster())
 *  ->addTo(['email' => $user->email, 'name' => $user->email,  'type' => 'to'])
 *  ->addMergeTags([$user->email => ['REMINDING_REFERRAL'=>$reminding]])
 *  ->setFromAddress('xxx@xxx.com')
 *  ->setSubject('Marketing Campaign')
 *  ->setTemplate('marketing-campaign')
 *  ->send();
 */
class MailMaster{
    private $mandrill;
    private $fromAddress;
    private $fromName;
    private $tos;
    private $mergeTagArray;
    private $mergeTags;
    private $toEmail;
    private $subject;
    private $template;
    private $mergelanguage = 'mailchimp';

    private $defaultCC = [];

    public function __construct($key = null)
    {
        if(!$key && !defined('MANDRILL_KEY')){
            throw new \Exception('MANDRILL KEY is required');
        }

        $key = $key?:MANDRILL_KEY;

        $this->mandrill = new \Mandrill($key);

        $this->tos = [];
        $this->mergeTagArray = [];
    }

    /**
     * @param $fromName string
     * @return $this
     */
    public function setFromName($fromName)
    {
        $this->fromName = $fromName;
        return $this;
    }

    /**
     * @param $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }


    /**
     * @param $fromAddress string
     * @return $this
     */
    public function setFromAddress($fromAddress)
    {
        $this->fromAddress = $fromAddress;
        return $this;
    }

    /**
     * $to should be format as ['email' => 'abc@abc.com', 'name' => 'JANUS ZHOU', 'type' => 'to/cc/bcc']
     * @param array $to
     * @return $this
     * @throws \Exception
     */
    public function addTo(array $to)
    {
        if(!isset($to['email'])){
            throw new \Exception('Email required');
        }

        $this->tos[] = $to;
        return $this;
    }

    /**
     * $mergeTags should be as ['EMAIL' => ['FORGOT_PASSWORD_WORD_LINK' => 'link']]
     * @param $mergeTags
     * @return $this
     */
    public function addMergeTags(array $mergeTags)
    {
        $this->mergeTagArray = array_merge($this->mergeTagArray, $mergeTags);
        return $this;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    public function setMergeLanguage($mergelanguage)
    {
        $this->mergelanguage = $mergelanguage;
        return $this;
    }

    public function send()
    {
        $message = [
            'subject' => $this->subject,
            'from_email' => $this->fromAddress,
            'from_name' => $this->fromName ,
            'to' => array_merge($this->tos, $this->defaultCC),
            'headers' => array('Reply-To' => $this->fromAddress),
            'track_clicks' => true,
            'track_opens' => true,
            'merge_vars' => $this->formatMergeTags(),
            'merge_language' => $this->mergelanguage
        ];

        $async = false;

        $ipPool = 'Main Pool';
        try {
            return $this->mandrill->messages->sendTemplate($this->template, [], $message, $async, $ipPool);
        } catch(\Mandrill_Error $e) {
            // Mandrill errors are thrown as exceptions
            echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
            throw $e;
        }

    }

    private function formatMergeTags()
    {
        $vars = [];
        foreach($this->mergeTagArray as $email => $mergeTag){
            $data = [ 'rcpt' => '',  'vars' => []];
            $data['rcpt'] = $email;
            foreach($mergeTag as $name => $tag){
                $data['vars'][] = ['name' => $name, 'content' => $tag];
            }
            $vars[] = $data;
        }

        return $vars;
    }

}