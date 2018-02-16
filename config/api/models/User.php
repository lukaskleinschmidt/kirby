<?php

use Kirby\Cms\Form;
use Kirby\Cms\User;

/**
 * User
 */
return [
    'default' => function () {
        // TODO: replace with the current user
        return $this->users()->first();
    },
    'fields' => [
        'avatar' => function (User $user) {
            return $user->avatar();
        },
        'blueprint' => function (User $user) {
            return $user->blueprint();
        },
        'content' => function (User $user) {
            return Form::for($user)->values();
        },
        'email' => function (User $user) {
            return $user->email();
        },
        'id' => function (User $user) {
            return $user->id();
        },
        'language' => function (User $user) {
            return $user->language();
        },
        'name' => function (User $user) {
            return $user->name();
        },
        'next' => function (User $user) {
            return $user->next();
        },
        'options' => function (User $user) {
            return $user->blueprint()->options()->toArray();
        },
        'prev' => function (User $user) {
            return $user->prev();
        },
        'role' => function (User $user) {
            return $user->role();
        }
    ],
    'type'  => User::class,
    'views' => [
        'default' => [
            'avatar',
            'content',
            'email',
            'id',
            'language',
            'name',
            'next' => 'compact',
            'options',
            'prev' => 'compact',
            'role'
        ],
        'compact' => [
            'avatar' => 'compact',
            'id',
            'email',
            'language',
            'name',
            'role',
        ]
    ]
];
