<?php
/**
 * Created by PhpStorm.
 * User: Halyna_Mecherzhak
 * Date: 6/4/2019
 * Time: 3:14 PM
 */

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Symfony\Component\HttpFoundation\Response;
use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;

class SecurityController extends FOSRestController
{
    private $client_manager;

    /**
     * SecurityController constructor.
     * @param ClientManagerInterface $client_manager
     */
    public function __construct(ClientManagerInterface $client_manager)
    {
        $this->client_manager = $client_manager;
    }

    /**
     * @FOSRest\Get("/getUser")
     */
    public  function getUserData(){
        $restresult = $this->getDoctrine()->getRepository(User::class)->findAll();
        if ($restresult === null) {
            return new View("there are no users exist", Response::HTTP_NOT_FOUND);
        }
        return $restresult;
    }

    /**
     * Create Client.
     * @FOSRest\Get("/createClient")
     *
     * @return Response
     */
    public function AuthenticationAction(Request $request)
    {
        $body = array('redirect-uri' => 'http://127.0.0.1:8000' , 'grant-type' => 'password');
        $clientManager = $this->client_manager;
        $client = $clientManager->createClient();
        $client->setRedirectUris([$body['redirect-uri']]);
        $client->setAllowedGrantTypes([$body['grant-type']]);
        $clientManager->updateClient($client);
        $rows = [
            'client_id' => $client->getPublicId(), 'client_secret' => $client->getSecret(), 'grant_type' => $body['grant-type'], 'username' => 'test_user', 'password'=>'test'
        ];

        return $this->redirectToRoute('token', $rows);
    }

    /**
     * @FOSRest\Post("/oauth/v2/token", name="token")
     * @return Response
     */
    public function getAccessToken(Request $request){
    }
}