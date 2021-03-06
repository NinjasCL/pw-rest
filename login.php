<?php 
/**
* login.php
* Create a new template named login
* and a page with that template
* for testing the endpoint
*/
include_once './rest/core/rest.php';
include_once './rest/languages/languages.php';
include_once './rest/login/errors.php';

use Rest\Errors\MethodNotAllowed as MethodNotAllowed;

use Rest\Request as Request;
use Rest\Response as Response;
use Rest\Method as Method;
use Rest\Header as Header;

use Login\Errors;
use Languages\Language as Language;

$response = new Response();

$response->allowMethod(Method::POST);

$params = Request::params();

if (!Request::isPost()) {

	$response->setError(MethodNotAllowed::error());

} else {

	$username = $params['username'];
	$password = $params['password'];

	if (!$username || !$password) {

		// Basic Auth
		$username = Header::username();
		$password = Header::password();
	}

	if ((!isset($username) || $username == '') ||
		(!isset($password) || $password == '')) {

		$response->setError(Login\Errors\InvalidCredentials::error());

	} else  {

		if ($username == 'hello' && $password == 'world') {

				$response->output['data']['name'] = 'Tony';
				$response->output['data']['lastname'] = 'Stark';
				$response->output['data']['job'] = 'Ironman';

		} else {

			$response->setError(Login\Errors\InvalidCredentials::error());
		}
	}
}

$response->addArray(Language::current());

$headerParam = Header::get('origin');
$response->addArray(['_request_headers' => Header::getAll()]);
$response->addArray(['headerParam' => $headerParam]);

/*
Should render
{
  "data": {
    "name": "Tony",
    "lastname": "Stark",
    "job": "Ironman"
  },
  "_language": {
    "id": 1017,
    "code": "default",
    "name": "English"
  }
}
*/
$response->render();