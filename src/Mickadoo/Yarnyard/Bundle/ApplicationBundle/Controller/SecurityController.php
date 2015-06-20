<?php

namespace Mickadoo\Yarnyard\Bundle\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class SecurityController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
        } elseif (null !== $session
            &&  $session->has(Security::AUTHENTICATION_ERROR)
        ) {
            $error = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        if ($error) {
            // WARNING! Symfony source code identifies this line as a potential
            // security threat.
            $error = $error->getMessage();
        }

        $lastUsername = (null === $session) ?
            '' : $session->get(Security::LAST_USERNAME);

        return $this->render(
            'MickadooYarnyardApplicationBundle:Security:login.html.twig',
            array(
                'last_username' => $lastUsername,
                'error' => $error,
            )
        );
    }

    /**
     * @param Request $request
     */
    public function loginCheckAction(Request $request)
    {

    }
}