<?php

namespace Reborn\Util;

/**
 * Hashing class for Reborn.
 * Now only support bCrypt only.
 *
 * @package Reborn\Util
 * @author Myanmar Links Professional Web Development Team
 **/
class Hash
{

    /**
     * Rounds value
     *
     * @var int
     **/
    protected $rounds;

    /**
     * Default constructor method
     *
     * @param  integer $rounds Value of rounds. Default is 10
     * @return void
     */
    public function __construct($rounds = 10)
    {
        $this->rounds = $rounds;
    }

    /**
     * Hash the given value to bCrypt hash
     *
     * @param  string $input
     * @return string
     **/
    public function hash($input)
    {
        $salt = $this->getSalt();

        $hash = crypt($input, $salt);

        return $hash;
    }

    /**
     * Check the hash value is true or false.
     *
     * @param  string  $input
     * @param  string  $hashedValue Hashed value
     * @return boolean
     */
    public function check($input, $hashedValue)
    {
        $hash = crypt($input, $hashedValue);

        return $hash === $hashedValue;
    }

    /**
     * Get the salt key to hash
     *
     * @return string
     */
    protected function getSalt()
    {
        $salt = sprintf('$2a$%02d$', $this->rounds);

        return $salt.random_str(22);
    }

} // END class Hash
