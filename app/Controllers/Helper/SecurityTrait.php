<?php

// app/Controllers/Helper/SecurityTrait.php

namespace Controllers\Helper;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;


/**
 * Trait - SecurityTrait
 * security operations
 *
 * @category Helper
 * @package  app\Controllers\Helper
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
trait SecurityTrait
{
    /**
     * Get a user from the Security Token Storage.
     *
     * @return mixed
     *
     * @throws \LogicException If SecurityBundle is not available
     *
     * @see TokenInterface::getUser()
     */
    public function getUser() {
        if (!isset($this->app['security.token_storage'])) {
            throw new \LogicException('The \"SecurityServiceProvider\" is not registered in your application.');
        }

        if (null === $token = $this->app['security.token_storage']->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return;
        }

        return $user;
    }
    
    /**
     * Checks if the attributes are granted against the current authentication token and optionally supplied object.
     * 
     * How to Secure any Service or Method in your Application 
     * throw new AccessDeniedException(); 
     *
     * @param mixed $attributes The attributes
     * @param mixed $object     The object
     *
     * @throws \LogicException
     *
     * @return bool
     */
    public function isGranted($attributes, $object = null) {
        if (!isset($this->app['security.authorization_checker'])) {
            throw new \LogicException('The \"SecurityServiceProvider\" is not registered in your application.');
        }

        return $this->app['security.authorization_checker']->isGranted($attributes, $object);
    }

    /**
     * Checks if user is authenticated
     *
     *
     * @return bool
     */
    public function isAuthenticated() {
        //IS_AUTHENTICATED_FULLY
        return $this->isGranted('IS_AUTHENTICATED_FULLY');
    }

    /**
     * Checks if user is admin
     *
     *
     * @return bool
     */
    public function isAdmin() {
        //IS_AUTHENTICATED_FULLY
        return $this->isGranted('ROLE_ADMIN');
    }
    
    /**
     * Returns an AccessDeniedException.
     *
     * This will result in a 403 response code. Usage example:
     *
     *     throw $this->createAccessDeniedException('Unable to access this page!');
     *
     * @param string          $message  A message
     * @param \Exception|null $previous The previous exception
     *
     * @return AccessDeniedException
     */
    public function createAccessDeniedException($message = 'Access Denied', \Exception $previous = null) {
//        return new AccessDeniedException($message, $previous);
        $this->app->abort(403, $message);
    }

    /**
     * Throws an exception unless the attributes are granted against the current authentication token and optionally
     * supplied object.
     *
     * @param mixed  $attributes The attributes
     * @param mixed  $object     The object
     * @param string $message    The message passed to the exception
     *
     * @throws AccessDeniedException
     */
    public function denyAccessUnlessGranted($attributes, $object = null, $message = 'Access Denied.') {
        if (!$this->isGranted($attributes, $object)) {
            throw $this->createAccessDeniedException($message);
        }
    }

    /**
     * Checks the validity of a CSRF token.
     *
     * @param string $id    The id used when generating the token
     * @param string $token The actual token sent with the request that should be validated
     *
     * @return bool
     */
    public function isCsrfTokenValid($id, $token) {
        if (!isset($this->app['security.csrf.token_manager'])) {
            throw new \LogicException('CSRF protection is not enabled in your application.');
        }

        return $this->app['security.csrf.token_manager']->isTokenValid(new CsrfToken($id, $token));
    }

    /**
     * Encodes the raw password
     *
     * @param string $pass    The password to encode
     *
     * @return string
     */
    public function encodePassword($pass) {

        $user = $this->getUser();

        // find the encoder for a UserInterface instance
        $encoder = $this->app['security.encoder_factory']->getEncoder($user);

        // compute the encoded password for foo
        $password = $encoder->encodePassword($pass, $user->getSalt());

        return $password;
    }
}
