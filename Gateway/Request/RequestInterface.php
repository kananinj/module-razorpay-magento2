<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Kananinj\Razorpay\Gateway\Request;

/**
 *
 * @author Kananinj
 */
interface RequestInterface {

    /**
     * body data
     */
    const BODY = 'body';

    /**
     * request url
     */
    const URL = 'uri';

    /**
     * request method
     */
    const METHOD = 'method';

    /**
     * merchant key;
     */
    const RZP_KEY_ID = 'key';

    /**
     * merchant key secret;
     */
    const RZP_KEY_SECRET = 'secret';

    
}
