<?php

namespace App\Filters;

use App\Libraries\AdelWAF;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AdelWAFilter implements FilterInterface
{
    protected $AdelWAF;
	
    public function before(RequestInterface $request)
    {
		$this->AdelWAF = new AdelWAF();
		$this->AdelWAF->isDA();
		$this->AdelWAF->run();
    }

    public function after(RequestInterface $request, ResponseInterface $response)
    {
        // Do something here
    }
}
