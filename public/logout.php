<?php

require_once __DIR__ . '/../app/auth/Auth.php';

Auth::logout();

header('Location: login.php');
exit;
