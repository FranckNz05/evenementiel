<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidEmailDomain implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Extraire le domaine de l'email
        $parts = explode('@', $value);
        if (count($parts) !== 2) {
            return false;
        }

        $domain = $parts[1];

        // Vérifier que le domaine contient au moins un point (TLD requis)
        if (strpos($domain, '.') === false) {
            return false;
        }

        // Vérifier que le domaine ne se termine pas par un point
        if (substr($domain, -1) === '.') {
            return false;
        }

        // Vérifier que le domaine a au moins 3 caractères (ex: a.co)
        if (strlen($domain) < 3) {
            return false;
        }

        // Vérifier que le TLD a au moins 2 caractères (ex: .com, .fr)
        $domainParts = explode('.', $domain);
        $tld = end($domainParts);
        if (strlen($tld) < 2) {
            return false;
        }

        // Vérifier que le domaine ne contient que des caractères valides
        if (!preg_match('/^[a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?(\.[a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?)*$/', $domain)) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'L\'adresse email doit contenir un domaine valide avec une extension (ex: .com, .fr, .org).';
    }
}

