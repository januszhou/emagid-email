<?php
/**
 * Created by PhpStorm.
 * User: zhou
 * Date: 3/20/17
 * Time: 4:55 PM
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
interface MailMasterInterface
{
    /**
     * @param $fromName string
     * @return $this
     */
    public function setFromName($fromName);

    /**
     * @param $subject
     * @return $this
     */
    public function setSubject($subject);

    /**
     * @param $fromAddress string
     * @return $this
     */
    public function setFromAddress($fromAddress);

    /**
     * $to should be format as ['email' => 'abc@abc.com', 'name' => 'JANUS ZHOU', 'type' => 'to/cc/bcc']
     * @param array $to
     * @return $this
     * @throws \Exception
     */
    public function addTo(array $to);

    /**
     * $mergeTags should be as ['EMAIL' => ['FORGOT_PASSWORD_WORD_LINK' => 'link']]
     * @param $mergeTags
     * @return $this
     */
    public function addMergeTags(array $mergeTags);

    public function setTemplate($template);

    public function setMergeLanguage($mergelanguage);

    public function setHtml($html);

    public function send();
}