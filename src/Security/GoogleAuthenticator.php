<?php
// app/src/Security/GithubAuthenticator.php
namespace App\Security;

// Your user entity
use App\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class GoogleAuthenticator extends SocialAuthenticator
{
    private $clientRegistry;
    private $em;
    private $router;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $em, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
	    $this->router = $router;
    }

    public function supports(Request $request)
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function getCredentials(Request $request)
    {
        // this method is only called if supports() returns true

        // For Symfony lower than 3.4 the supports method need to be called manually here:
        // if (!$this->supports($request)) {
        //     return null;
        // }

        return $this->fetchAccessToken($this->getGoogleClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var League\OAuth2\Client\Provider\ResourceOwnerInterface $googleUser */
        $googleUser = $this->getGoogleClient()->fetchUserFromToken($credentials);
       

        // Note: normally, email is always null if the user has no public email address configured on Github
        // https://stackoverflow.com/questions/35373995/github-user-email-is-null-despite-useremail-scope
        $email = $googleUser->getEmail();

        // 1. have they logged in with Github before? Easy!
        $existingUser = $this->em->getRepository(User::class)->findOneBy(['googleId' => $googleUser->getId()]);
        
        // This array contains the API information of the Authenticated Github user
        $githubData = $googleUser->toArray();
        
        if ($existingUser) {
            return $existingUser;
        }
        
        // If your application requires an email to persist an User entity, you need to figure out one in case that the Github user doesn't provide one
        if(!$email){
            $email = "{$googleUser->getId()}@googleoauth.com";
            
        }

        // If the user exists, use it
        if ($existingUser) {
            $user = $existingUser;
        
        // Otherwise, create a new one (?)
        } else {
            // 2) do we have a matching user by email? If so, we shouldn't create a new user, we may use the same entity and set the github id
            $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

            // If it still doesn't exist, you need to create a new one
            // Here comes the custom logic of the creation of your user
            if (!$user) {

                // e.g. This is just an example, it depends of your user entity, so be sure to modify this
                /** @var User $user */
                $user = new User();
                $user->setUsername($googleUser->getName());
                $user->setPassword("null");
                $user->setIsVerified(true);
                $user->setRoles(["ROLE_USER"]);
                
               $user->setImage($googleUser->getAvatar());
                $user->setEmail($googleUser->getEmail());
               
               
            }
        }

        // Finally, there should always exist an $user object
        // So update the GithubId and persist it if it doesn't exist
        $user->setGoogleId($googleUser->getId());
        //dd($googleUser);
        $this->em->persist($user);
        $this->em->flush();

        return $user;
        
    }

    /**
     * @return GoogleClient
     */
    private function getGoogleClient()
    {
        // "github_main" is the key used in config/packages/knpu_oauth2_client.yaml
        return $this->clientRegistry->getClient('google');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // change "app_homepage" to some route in your app
        $targetUrl = $this->router->generate('home');

        return new RedirectResponse($targetUrl);
    
        // or, on success, let the request continue to be handled by the controller
        //return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            '/connect/', // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}