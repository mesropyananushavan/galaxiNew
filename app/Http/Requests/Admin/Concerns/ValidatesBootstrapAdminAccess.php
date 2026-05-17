<?php

namespace App\Http\Requests\Admin\Concerns;

use Illuminate\Validation\Validator;

trait ValidatesBootstrapAdminAccess
{
    protected function validateBootstrapAdminAccess(Validator $validator, string $field, string $message): void
    {
        $validator->after(function (Validator $validator) use ($field, $message): void {
            $user = $this->user();

            if ($user === null || $user->hasBootstrapAdminAccess()) {
                return;
            }

            $validator->errors()->add($field, $message);
        });
    }
}
