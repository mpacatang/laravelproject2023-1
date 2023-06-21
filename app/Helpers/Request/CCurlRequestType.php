<?php

namespace App\Helpers\Request;

enum CCurlRequestType{
    case POST;
    case GET;
    case PATCH;
    case PUT;
}