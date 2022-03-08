<?php
// app/src/Controller/SocialAuthenticationController.php
namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FacebookAuthenticationController extends AbstractController
{
    /**
     * Link to this controller to start the "connect" process
     * @param ClientRegistry $clientRegistry
     *
     * @Route("/connect/facebook", name="connect_facebook_start")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function connectFacebookAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            // ID used in config/packages/knpu_oauth2_client.yaml
            ->getClient('facebook')
            // Request access to scopes
            // https://github.com/thephpleague/oauth2-github
            ->redirect()
        ;
    }

    /**
     * After going to Github, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * @param Request $request
     * @param ClientRegistry $clientRegistry
     *
     * @Route("/connect/facebook/check", name="connect_facebook_check")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function connectFacebookCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        return $this->redirectToRoute('home');
    }
}