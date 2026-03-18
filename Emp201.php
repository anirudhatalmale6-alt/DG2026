<?php
defined('BASEPATH') or exit('No direct script access allowed');
require 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

class Emp201 extends AdminController
{
    public function index()
    {
        $this->load->view('theme2/sars/emp2');
    }

}