<?php

if (! function_exists('currentUser')) {
    function currentUser(): ?array
    {
        $session = session();

        if (! $session->get('isLoggedIn')) {
            return null;
        }

        return [
            'id'        => $session->get('utilisateurId'),
            'nom'       => $session->get('nom'),
            'prenom'    => $session->get('prenom'),
            'roleCode'  => $session->get('roleCode'),
            'roleLabel' => $session->get('roleLabel'),
        ];
    }
}

if (! function_exists('isLoggedIn')) {
    function isLoggedIn(): bool
    {
        return (bool) session()->get('isLoggedIn');
    }
}

if (! function_exists('hasRole')) {
    function hasRole($roles): bool
    {
        $roleCode = session()->get('roleCode');

        if ($roleCode === null) {
            return false;
        }

        $roles = is_array($roles) ? $roles : [$roles];

        return in_array($roleCode, $roles, true);
    }
}
