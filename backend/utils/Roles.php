<?php

class Roles
{
    const ADMIN = 'admin';
    const TRAINER = 'trainer';

    public static function all()
    {
        return [self::ADMIN, self::TRAINER];
    }
}