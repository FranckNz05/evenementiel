<?php
namespace App\Exceptions;

use Exception;

class NoTicketsSelectedException extends Exception
{
    protected $message = 'Veuillez sélectionner au moins un billet.';
}

// InsufficientTicketsException.php
namespace App\Exceptions;

use Exception;

class InsufficientTicketsException extends Exception
{
    // Le message sera passé au constructeur
}
