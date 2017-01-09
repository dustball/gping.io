<?php

use GPing\Success;
use GPing\Failure;
use Lcobucci\JWT;

// ApiEndpoint is the expected interface each endpoint for the API will implement.
interface ApiEndpoint {
  // act performs the actions associated with this endpoint. It must return a
  // Response implementation.
  public function act($path, array $path_args, $query_string) /*: Response */;
}

// AuthedApiEndpoint is base class that handles request authentication by
// validating a JWT header and ensuring it comes with a claim that indicates
// the requesting user's ID. Once Authentication is done authed_action is called.
//
// NOTE: This base class handles _authentication_ and does not make any claims
// about whether or not the user is authorized to take the action. That should
// be verify by the endpoint itself.
abstract class AuthedApiEndpoint implements ApiEndpoint {
  // authed_action will only be called if the request is made with a valid JWT
  abstract protected function authed_action($user_id, $path, array $path_args, $query_string);

  public function act($path, array $path_args, $query_string) {

    $hs = getallheaders();
    $hdr = "Authorization";

    if (!key_exists($hdr, $hs)) {
      return $this->not_authed($path);
    }

    $token = "";
    $uid = "";

    try {
      $token = (new JWT\Parser())->parse($hs[$hdr]);
      if ($token->hasClaim("uid")) {
        $uid = $token->getClaim("uid", "");
      }
    } catch (Exception $e) {
      return $this->bad_token($path);
    }

    if ("" == $uid) {
      return $this->bad_token($path);
    }

    $data = (new JWT\ValidationData());
    $data->setIssuer(jwt_iss());
    $data->setAudience(jwt_aud());

    if (!$token->validate($data)) {
      return $this->bad_token($path);
    }

    return $this->authed_action($uid, $path, $path_args, $query_string);
  }

  public function not_authed($path) {
    return new ApiError("not authorized to access $path", 403);
  }

  public function bad_token($path) {
    return new ApiError("received a bad authorization token for access to $path", 403);
  }
}

// has_email checks the user associated with $id to ensure they are the same as
// indicated by $email and returns an Attempt. A Failure will carry an ApiError
// and a Success will carry an associative array containing user data.
//
// On an $id / $email mismpatch $on_auth_failure will be returned wrapped in a
// GPing\Failure.
function has_email($db, $id, $email, $on_auth_failure) /* Attempt<Response> */{
  $email = _canonicalize($email);
  $result = get_user_by_id($db, $id);

  if ($result->err() || $result->count() != 1) {
    // if there was an error getting a user or more / less than one user
    // comes back we got problems
    return new Failure(internal_server_error());
  }

  $data = $result->row();
  if ($data["email"] !== $email) {
    return new Failure($on_auth_failure);
  }

  return new Success($result->row());
}

?>
