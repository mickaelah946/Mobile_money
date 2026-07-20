<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session      = session();
        $rolesAutorises = $arguments ?? [];

        if (! $session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $roleCode = $session->get('roleCode');

        if (! empty($rolesAutorises) && ! in_array($roleCode, $rolesAutorises, true)) {
            return redirect()->to('/dashboard')->with('error', "Vous n'avez pas les droits nécessaires pour accéder à cette page.");
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Rien à faire après la requête.
    }
}
