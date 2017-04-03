<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;
use Facebook\Exceptions;

class FbGraphController extends Controller
{
    public function index(LaravelFacebookSdk $fb)
    {
        $login_link = $fb
            ->getRedirectLoginHelper()
            ->getLoginUrl('http://localhost:8000/facebook/callback', ['email', 'user_events']);

        return view('facebook.index', compact('login_link'));
    }

    public function callback(LaravelFacebookSdk $fb)
    {
        try {
            $token = $fb->getAccessTokenFromRedirect();
        }
        catch (FacebookSDKException $e) {
            dd($e->getMessage());
        }

        try {
            $response = $fb->get('/me?fields=link,name,email,albums.limit(5){name, photos.limit(6){name, picture, tags.limit(6)}},posts.limit(5)', $token);
        }
        catch(FacebookSDKException $e) {
            dd($e->getMessage());
        }

        try {
            $respBasic = $fb->get('/me?fields=link,name,email', $token);
        }
        catch(FacebookSDKException $e) {
            dd($e->getMessage());
        }


        try {
            $respAlbums = $fb->get('me?fields=albums.limit(2){name, photos.limit(6){name, picture, tags.limit(6)}}', $token);
        }
        catch (FacebookSDKException $e)
        {
            dd($e->getMessage());
        }


        try {
            $respLikes = $fb->get('me?fields=likes.limit(5)', $token);
        }
        catch (FacebookSDKException $e)
        {
            dd($e->getMessage());
        }


        $userGrabbedData = $response->getGraphUser()['albums'];
        $userAlbums = $respAlbums->getGraphUser()['albums']->asArray();
        $userLikes = $respLikes->getGraphUser()['likes']->asArray();
        $userBasicData = $respBasic->getGraphUser();

        return view('facebook.callback', compact('userGrabbedData','userAlbums','userLikes','userBasicData'));
    }
}
