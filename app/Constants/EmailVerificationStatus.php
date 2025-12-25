<?php

namespace App\Constants;

final class EmailVerificationStatus
{
    public const VERIFIED = 'OK';
    public const FAILED = 'NG';
    public const ALREADY_VERIFIED = 'Authenticated';
}