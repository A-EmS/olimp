<?php
/**
 * In this case, we want to increase the default cost for BCRYPT to 12.
 * Note that we also switched to BCRYPT, which will always be 60 characters.
 */

$pwd = password_hash("GfhjkmFlvbyf", PASSWORD_BCRYPT, [12]);
var_dump($pwd);
