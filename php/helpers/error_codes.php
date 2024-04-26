<?php

enum UserCode: int
{
    case USER_NOT_FOUND = 1;
    case USER_EXISTS = 2;
    case PASSWORD_DONT_MATCH = 3;
}

class UserException extends Exception
{
    function __construct(UserCode $excode)
    {
        match($excode) {
            UserCode::USER_NOT_FOUND =>         parent::__construct("User Not Found", UserCode::USER_NOT_FOUND->value),
            UserCode::USER_EXISTS =>            parent::__construct("User Already Exists", UserCode::USER_EXISTS->value),
            UserCode::PASSWORD_DONT_MATCH =>    parent::__construct("Password Do Not Match", UserCode::PASSWORD_DONT_MATCH->value)
        };
    }
}