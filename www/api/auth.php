<?php
use Lcobucci\JWT;
use Lcobucci\JWT\Signer\Hmac\Sha256;

function act() {
  // TODO: need to swap our stuff to an autoloader
  include(dr('db/query_result.php'));
  include(dr('config.php'));
  include(dr('util.php'));
  include(dr('db/auth.php'));

  $db = get_db();

  $req = _getBodyJSON();
  $user = $req['username'];
  $pass = $req['password'];

  $r = get_user($db, $user);

  $authed = false;

  if ($r->count() == 1) {
    $row = $r->row();
    $hash = $row['password'];
    $authed = password_verify($pass, $hash);

    if ($authed) {
      $now = time();
      $b = (new JWT\Builder())
        ->setIssuer(jwt_iss())
        ->setAudience(jwt_aud())
        ->setIssuedAt($now)
        ->set('uid', $row['id'])
        ->sign(new JWT\Signer\Hmac\Sha256(), jwt_secret());
      return new ApiSuccess(["token" => ("" . $b->getToken())]);
    }
  }

  return new ApiError("unable to authenticate $user", 403);
}
?>
