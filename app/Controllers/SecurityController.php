<?php

// app/Controllers/SecurityController.php

namespace Controllers;

use Silex\Application;
use Models\ORM\User;
use Forms\RegForm;
use Forms\Constraints as Assert;

/**
 * Class - SecurityController
 * Security operations
 * 
 * @category Controller
 * @package  app\Controllers
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     https://github.com/bsa-git/silex-mvc/
 */
class SecurityController extends BaseController {

    //-----------------------------
    /**
     * Constructor 
     * 
     * @param Application $app
     */
    public function __construct(Application $app) {
        parent::__construct($app);
//        $this->_iniRoutes();
    }

    /**
     * Routes initialization
     * 
     * @return void
     */
    protected function iniRoutes() {
        $self = $this;
        $this->app->get('/login', function () use ($self) {
            return $self->loginAction();
        })->bind('login');
        $this->app->post('/login_check', function () use ($self) {
            return $self->loginAction();
        })->bind('login_check');
        $this->app->get('/logout', function () use ($self) {
            return $self->logoutAction();
        })->bind('logout');
        $this->app->match('/registration', function () use ($self) {
            return $self->registrationAction();
        })->bind('registration')->method('GET|POST');
        $this->app->get('/account', function () use ($self) {
            return $self->accountAction();
        })->bind('account');
        $this->app->get('/admin', function () use ($self) {
            return $self->adminAction();
        })->bind('admin');
    }

    /**
     * Action - security/login
     * 
     * @return string
     */
    public function loginAction() {
        $request = $this->getRequest();
        $session = $request->getSession();
        //---------------------
        try {
            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);

            $error = $this->app['security.last_error']($request);
            $last_username = $session->get('_security.last_username');

            if ($this->isAuthenticated()) {
                if (!$error) {
                    return $this->redirect("/account");
                }
            }

            $data = array(
                'error' => $error,
                'last_username' => $last_username
            );

            // Show form
            return $this->showView($data);
        } catch (\Exception $exc) {
            return $this->showError($exc);
        }
    }

    /**
     * Action - security/logout
     * 
     * @return string
     */
    public function logoutAction() {
        // Initialization
        $this->init(__CLASS__ . "/" . __FUNCTION__);
    }

    /**
     * Action - security/registration
     * 
     * @return string
     */
    public function registrationAction() {
        $request = $this->getRequest();
        $models = $this->app['models'];
        //--------------------
        try {
            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);

            if ($this->isAjaxRequest()) {

                // Check the validity of input fields in the form below
                if (isset($this->params['reg']['username'])) {
                    $username = $this->params['reg']['username'];

                    // set constraint for username
                    $usernameConstraint = new Assert\Customize\UniqueEntity(array(
                        'app' => $this->app,
                        'entity' => 'Models\ORM\User',
                        'field' => 'username'
                    ));

                    // use a validator to check the value
                    $errorList = $this->getValidator()->validateValue($username, $usernameConstraint);

                    if (count($errorList) == 0) {
                        $result = TRUE;
                    } else {
                        $result = $errorList[0]->getMessage();
                    }
                } elseif (isset($this->params['reg']['email'])) {
                    $email = $this->params['reg']['email'];

                    // set constraint for username
                    $emailConstraint = new Assert\Customize\UniqueEntity(array(
                        'app' => $this->app,
                        'entity' => 'Models\ORM\User',
                        'field' => 'email'
                    ));

                    // use a validator to check the value
                    $errorList = $this->getValidator()->validateValue($email, $emailConstraint);

                    if (count($errorList) == 0) {
                        $result = TRUE;
                    } else {
                        $result = $errorList[0]->getMessage();
                    }
                } elseif (isset($this->params['reg']['personal_mobile'])) {

                    $mobile = $this->params['reg']['personal_mobile'];

                    $len = strlen($mobile);
                    if ($len < 13) {
                        $result = TRUE;
                        return $this->sendJson($result);
                    }

                    // set constraint for username
                    $mobileConstraint = new Assert\Customize\UniqueEntity(array(
                        'app' => $this->app,
                        'entity' => 'Models\ORM\User',
                        'field' => 'personal_mobile'
                    ));

                    // use a validator to check the value
                    $errorList = $this->getValidator()->validateValue($mobile, $mobileConstraint);

                    if (count($errorList) == 0) {
                        $result = TRUE;
                    } else {
                        $result = $errorList[0]->getMessage();
                    }
                } else {
                    $result = $this->trans('communication_error');
                }
                return $this->sendJson($result);
            } else {
                // Create object $newUser and set values
                $newUser = new User();
                $newUser->setApp($this->app);
                $form = $this->createForm(new RegForm(), $newUser);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $data = $models->load('User', 'newUser', array('new_user' => $newUser));
                    $view = $this->getIncPath() . '/mail_new_user.html.twig';
                    $mailBody = $this->renderView($view, $data);

                    $this->mail(\Swift_Message::newInstance()
                                    ->setSubject('Silex Email Test')
                                    ->setFrom(array('m5-asutp@azot.ck.ua'))
                                    ->setTo(array('bsa2657@yandex.ru'))
                                    ->setBody($mailBody, 'text/html')); // 'text/html', 'text/plain'

                    return $this->redirect("/login");
                }
                // Show form
                return $this->showView(array('form' => $form->createView()));
            }
        } catch (\Exception $exc) {
            if ($this->isAjaxRequest()) {
                return $this->errorAjaxValid($exc);
            } else {
                return $this->showError($exc);
            }
        }
    }

    /**
     * Action - security/account
     * 
     * @return string
     */
    public function accountAction() {
        $models = $this->app['models'];
        $data = array();
        //--------------------
        // Initialization
        $this->init(__CLASS__ . "/" . __FUNCTION__);

        $userName = $this->getUser()->getUsername();
        $data += $models->load('Post', 'getPosts', $userName);

        return $this->showView($data);
    }

    /**
     * Action - security/admin
     * 
     * @return string
     */
    public function adminAction() {
        try {
            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);

            return $this->showView();
        } catch (\Exception $exc) {
            return $this->showError($exc);
        }
    }

}
