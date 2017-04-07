<?php
namespace App\Http\Controllers\Auth;
use App\User;
use Validator;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\ClientException;
use Laravel\Socialite\Two\InvalidStateException;
use League\OAuth1\Client\Credentials\CredentialsException;
use Socialite;
use Auth;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;
use Facebook\Exceptions;


class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';
    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }








    /**
     * Redirect the user to the OAuth Provider.
     *
     * @return Response
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from provider.  Check if the user already exists in our
     * database by looking up their provider_id in the database.
     * If the user exists, log them in. Otherwise, create a new user then log them in. After that 
     * redirect them to the authenticated users homepage.
     *
     * @return Response
     */
    public function handleProviderCallback($provider)
    {
        $user = Socialite::driver($provider)->user();
//***********
        $token = $user->token;
        $fqb = new \SammyK\FacebookQueryBuilder\FQB();

        $request = $fqb->node('me')
            ->fields(['email', 'name', 'albums.limit(1){photos{picture,link}}', 'posts.limit(5){story}', 'likes.limit(5)'])
            ->accessToken($token)
            ->graphVersion('v2.8');
        $response = (array) json_decode(file_get_contents($request));

        $userAlbums = ['no priviliges to this data'];
        $userPosts = ['no priviliges to this data'];
        $userLikes = ['no priviliges to this data'];

        if(isset($response['albums'])){
            $userAlbums = $this->objectToArray((array) $response['albums']);
            session(['userPhotos' => $userAlbums['data'][0]['photos']['data']]);
        }

        if(isset($response['posts'])){
            $userPosts = $this->objectToArray($response['posts'])['data'];
            session(['userPosts' => $userPosts]);
        }

        if(isset($response['likes'])){
            $userLikes = $this->objectToArray($response['likes'])['data'];
            session(['userLikes' => $userLikes]);
        }

        session(['userPersonalData'=>[$response['id'], $response['email'], $response['name']]]);



//*************
        $authUser = $this->findOrCreateUser($user, $provider);
        Auth::login($authUser, true);

        return redirect($this->redirectTo);
    }

    /**
     * If a user has registered before using social auth, return the user
     * else, create a new user object.
     * @param  $user Socialite user object
     * @param $provider Social auth provider
     * @return  User
     */
    private function findOrCreateUser($user, $provider)
    {
        $authUser = User::where('provider_id', $user->id)->first();
        if ($authUser) {
            return $authUser;
        }
        return User::create([
            'name'     => $user->name,
            'email'    => $user->email,
            'provider' => $provider,
            'provider_id' => $user->id
        ]);
    }

    private function objectToArray($result)
    {
        $array = array();
        foreach ($result as $key=>$value)
        {
            if (is_object($value))
            {
                $array[$key]=$this->objectToArray($value);
            }
            elseif (is_array($value))
            {
                $array[$key]=$this->objectToArray($value);
            }
            else
            {
                $array[$key]=$value;
            }
        }
        return $array;
    }
}