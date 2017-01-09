<?php
namespace GPing;

interface Attempt {
  // isSuccess returns true if this Attempt is a Success
  public function isSuccess();

  // isSuccess returns true if this Attempt is a Failure
  public function isFailure();

  // get returns either the value kept as part of the success _or_ the error
  // which caused escalation to Failure
  public function get();

  // map takes a fuction which accepts the result of a Success and produces
  // some new result. That result is then returned as a Success of its own.
  // If calling the function throws (note: not returns) an Exception it is
  // converted into a Failure.
  //
  // If map is called on a Failure the Failure is returned and the function is
  // not called. This enables chains of code to be written ensuring progress
  // in made only when previous steps have completed without Exception.
  //
  // Example:
  //
  //   function auth() {
  //     // do auth
  //     if ($could_not_auth) {
  //       throw new AuthenticationFailedException(...);
  //     } else {
  //       return AuthenticatedUser(...);
  //     }
  //   }
  //
  //   function perform_request($user) {
  //     // do things that must be protected
  //     return $request_result;
  //   }
  //
  //   $result = auth()->map(perform_request);
  //     auth() will attempte to authenticate the user and will either
  //     1. succeed - perform_request will get called with $user being set
  //        as the AuthenticatedUser constructed and returned from auth
  //     2. fail by throwing an AuthenticationFailedException - this results
  //        in perform_request no getting called because map on a Failure
  //        only returns the Failure.
  //     $result will be either Success($request_result) or Failure(AuthenticationFailedException(...))
  public function map(callable $fn);

  // flatMap takes a callable function which accepts the result of a Success
  // and produces a new Attempt. The result of that callable is then flattened
  // so that we don't end up with Success(Success(Success(42))) etc. This
  // allows users to more easily handle deeply nested call chains without
  // having to unpack them to determine if the result was eventually success
  // or failure.
  //
  // This path is also useful if you wish to indicate Failure in ways other than
  // throwing Exceptions.
  //
  // Example:
  //
  //   function foo()   { return new Success(42); }
  //   function bar($n) { return new Success("number " . $n); }
  //   function err()   { return new Failure(new Exception("bad code")); }
  //   foo()->map(bar)      // produces "number 42"
  //   foo()->map(err)      // produces Success(Failure("bad code"))
  //   foo()->flatMap(err); // produces Failure("bad code"))
  public function flatMap(callable $tryFn);

  // process makes acting on succes or failure simple. It takes two callables
  // which will be be passed either the result of a Success or the error nested
  // within a Failure.
  //
  // The return value from the parameter function is passed back directly (i.e.
  // not wrapped in a Success/Failure).
  public function process(callable $succFn, callable $failFn);
}

// With deepest and most sincere apologies to com.twitter.util.Try <3.

?>
