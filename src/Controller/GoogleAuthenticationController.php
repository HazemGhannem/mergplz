<?php
// app/src/Controller/SocialAuthenticationController.php
namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GoogleAuthenticationController extends AbstractController
{
    /**
     * Link to this controller to start the "connect" process
     * @param ClientRegistry $clientRegistry
     *
     * @Route("/connect/google", name="connect_google_start")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function connectGoogleAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            // ID used in config/packages/knpu_oauth2_client.yaml
            ->getClient('google')
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
     * @Route("/connect/google/check", name="connect_google_check")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function connectGoogleCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        return $this->redirectToRoute('home');
    }
}