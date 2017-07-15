<?php

namespace App\Libraries\Common;

abstract class Constants {
    const ACCESS_TOKEN_EXPIRE_IN        = 3600*24*180;
    // cache ttl
    const CACHE_TTL_SIX_HOURS           = 3600*6;
    const CACHE_TTL_ONE_MINUTE          = 60;
}
