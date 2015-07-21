<?php

namespace Projects\DidYouDoItBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * OAuth Controller
 */
class OAuthController extends Controller
{
    public function connectionAction() 
    {
        $googleClient = $this->get('persistance');

        return $this->redirect(filter_var($googleClient->createAuthUrl(), FILTER_SANITIZE_URL));
    }

    public function oauth2callbackAction(Request $request)
    {
        $googleClient = $this->get('persistance');

        if ($request->query->get('code') !== null)
        {
            try {
                $googleClient->authenticate($request->query->get('code'));
            } 
            catch (\Exception $e)
            {
                return $this->render('DidYouDoItBundle:Error:error.html.twig', array(
                    'error'      => $e->getMessage(),
                ));
            }
            return $this->redirect($this->generateUrl('did_you_do_it_get_all_tasklist'));
        }
        else if ($request->query->get('error') !== null)
        {
            return $this->render('DidYouDoItBundle:Error:error.html.twig', array(
                'error'      => $e->getMessage(),
            ));
            return $this->redirect($this->generateUrl('did_you_do_it_get_all_tasklist'));
        }
    }
}
