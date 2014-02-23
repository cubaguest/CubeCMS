<?php
// base app init
Auth::addAuthenticator(new Auth_Provider_Google(3));
Auth::addAuthenticator(new Auth_Provider_OpenID(3));