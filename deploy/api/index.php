<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Tuupola\Middleware\HttpBasicAuthentication;
use \Firebase\JWT\JWT;
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../src/User.php';
require __DIR__ . '/../src/Catalogue.php';
 
$app = AppFactory::create();
const JWT_SECRET = "leriway123";
$app->addErrorMiddleware(true, true, true);


$app->get('/api/hello/{name}', function (Request $request, Response $response, $args) {
    $array = [];
    $array ["nom"] = $args ['name'];
    $response->getBody()->write(json_encode ($array));
    return $response;
});

// APi d'authentification générant un JWT
$app->post('/api/login', function (Request $request, Response $response, $args) {   
    $err=false;
    $inputJSON = file_get_contents('php://input');
    $body = json_decode( $inputJSON, TRUE );
    $login = $body ['login'] ?? "";
    $password = $body ['password'] ?? "";

    // if (empty($login) || empty($password)|| !preg_match("/^[a-zA-Z0-9]+$/", $login) || !preg_match("/^[a-zA-Z0-9]+$/", $password)) {
    //     $err=true;
    // }
 
    if (!$err) {
            $response = createJwT ($response , $login, $password);
            $response = addHeaders($response);
            $data = array('login' => $login, 'password' => $password);
            $response->getBody()->write(json_encode($data));
     } else {          
            $response = $response->withStatus(401);
     }
    return $response;
});

// get user 
$app->get('/api/user', function (Request $request, Response $response, $args) {
    $array = [];
    $array ["nom"] = "AMOCA";
    $array ["prenom"] = "Okkes";
    $response = addHeaders($response);
    $response->getBody()->write(json_encode ($array));
    return $response;
});

function createJwT (Response $response, $login, $password) : Response {

    $issuedAt = time();
    $expirationTime = $issuedAt + 700;
    $payload = array(
    'login' => $login,
    'password' => $password,
    'iat' => $issuedAt,
    'exp' => $expirationTime
    );

    $token_jwt = JWT::encode($payload,JWT_SECRET, "HS256");
    $response = $response->withHeader("Authorization", "Bearer {$token_jwt}");
    return $response;
}

$options = [
    "attribute" => "token",
    "header" => "Authorization",
    "regexp" => "/Bearer\s+(.*)$/i",
    "secure" => false,
    "algorithm" => ["HS256"],
    "secret" => JWT_SECRET,
    "path" => ["/api"],
    "ignore" => ["/api/hello","/api/login","/api/createUser", "/api/catalogue"],
    "error" => function ($response, $arguments) {
        $data = array('ERREUR' => 'Connexion', 'ERREUR' => 'JWT Non valide');
        $response = $response->withStatus(401);
        return $response->withHeader("Content-Type", "application/json")->getBody()->write(json_encode($data));
    }
];

// get products
$app->get('/api/product', function (Request $request, Response $response, $args) {
    $json = file_get_contents("./mock/catalogue.json");
    $response = addHeaders($response);
    $response->getBody()->write($json);
    return $response;
});

function  addHeaders (Response $response) : Response {
    $response = $response
    ->withHeader("Content-Type", "application/json")
    ->withHeader('Access-Control-Allow-Origin', ('https://deploy-b6a9.onrender.com'))
    ->withHeader('Access-Control-Allow-Headers', 'Content-Type,  Authorization')
    ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
    ->withHeader('Access-Control-Expose-Headers', 'Authorization');
    return $response;
}

$app->get('/api/catalogue', function (Request $request, Response $response, $args) {
    global $entityManager;
    $products = $entityManager->getRepository('Catalogue')->findAll();
    var_dump($products);
    $response = addHeaders($response);
    $response->getBody()->write(json_encode ($products));
    return $response; //oui
});

$app->add(new Tuupola\Middleware\JwtAuthentication($options));
$app->run ();