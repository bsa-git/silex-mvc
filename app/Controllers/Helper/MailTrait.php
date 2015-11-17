<?php

// app/Controllers/Helper/MailTrait.php

namespace Controllers\Helper;


/**
 * Trait - MailTrait
 * mail operations
 *
 * @category Helper
 * @package  app\Controllers\Helper
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
trait MailTrait
{
    /**
     * Sends an email.
     *
     * @param \Swift_Message $message          A \Swift_Message instance
     * @param array          $failedRecipients An array of failures by-reference
     *
     * @return int The number of sent messages
     */
    public function mail(\Swift_Message $message, &$failedRecipients = null) {
        if (!isset($this->app['mailer'])) {
            throw new \LogicException('The \"SwiftmailerServiceProvider\" is not registered in your application.');
        }
        
        if(isset($this->app['config']['parameters']['mail.disable_delivery'])){
            $disable_delivery = $this->app['config']['parameters']['mail.disable_delivery'];
            if($disable_delivery === FALSE){
                return $this->app['mailer']->send($message, $failedRecipients);
            }
            
        }  else {
            return $this->app['mailer']->send($message, $failedRecipients);
        }
        
        
    }
}
