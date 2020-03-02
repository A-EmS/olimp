<?php
namespace app\repositories;

class ContactTypesInputRep
{
    CONST PHONE_INT_TYPE = 1;
    CONST PHONE_TYPE = 'phone';
    CONST EMAIL_INT_TYPE = 2;
    CONST EMAIL_TYPE = 'email';

    public static $_types_string = [
        self::PHONE_INT_TYPE => self::PHONE_TYPE,
        self::EMAIL_INT_TYPE => self::EMAIL_TYPE,
    ];

    public static $_types_int = [
        self::PHONE_TYPE => self::PHONE_INT_TYPE,
        self::EMAIL_TYPE => self::EMAIL_INT_TYPE,
    ];
}