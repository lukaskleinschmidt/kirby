<?php

namespace Kirby\Form;

use Kirby\Cms\App;
use Kirby\Form\Exceptions\ValidationException;

class UserField extends Field
{

    use Mixins\Autofocus;
    use Mixins\Help;
    use Mixins\Icon;
    use Mixins\Label;
    use Mixins\Required;
    use Mixins\Value;

    protected function defaultDefault()
    {
        if ($user = App::instance()->user()) {
            return $user->id();
        }
    }

    protected function defaultIcon()
    {
        return 'user';
    }

    protected function defaultLabel()
    {
        return 'User';
    }

    protected function defaultName(): string
    {
        return 'user';
    }

    protected function validate($value): bool
    {
        $this->validateRequired($value);
        $this->validateUser($value);

        return true;
    }

    protected function validateUser($value)
    {
        if ($value !== null) {
            if (App::instance()->user($value) === null) {
                throw new ValidationException('The user cannot be found');
            }
        }

        return true;
    }

}
