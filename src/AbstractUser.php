<?php

namespace Cblink\Hyperf\Socialite;

use ArrayAccess;
use Cblink\Hyperf\Socialite\Contracts\User;

abstract class AbstractUser implements ArrayAccess, User
{
    /**
     * The unique identifier for the user.
     *
     * @var mixed
     */
    public $id;

    /**
     * The user's nickname / username.
     *
     * @var string
     */
    public $nickname;

    /**
     * The user's full name.
     *
     * @var string
     */
    public $name;

    /**
     * The user's e-mail address.
     *
     * @var string
     */
    public $email;

    /**
     * The user's avatar image URL.
     *
     * @var string
     */
    public $avatar;

    /**
     * The user's raw attributes.
     *
     * @var array
     */
    public $user;

    /**
     * @var string
     */
    public $mobile;

    /**
     * Get the unique identifier for the user.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the nickname / username for the user.
     *
     * @return string
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Get the full name of the user.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the e-mail address of the user.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get the avatar / image URL for the user.
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Get the raw user array.
     *
     * @return array
     */
    public function getRaw()
    {
        return $this->user;
    }

    /**
     * Set the raw user array from the provider.
     *
     * @param  array  $user
     * @return $this
     */
    public function setRaw(array $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Map the given array onto the user's properties.
     *
     * @param  array  $attributes
     * @return $this
     */
    public function map(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }

    /**
     * Determine if the given raw user attribute exists.
     *
     * @param  string  $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->user);
    }

    /**
     * Get the given key from the raw user.
     *
     * @param  string  $offset
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        return $this->user[$offset];
    }

    /**
     * Set the given attribute on the raw user array.
     *
     * @param  string  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->user[$offset] = $value;
    }

    /**
     * Unset the given value from the raw user array.
     *
     * @param  string  $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->user[$offset]);
    }
}