<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

  public function __construct()
  {
    parent:: __construct();
    header('Content-Type: application/json');
  }

  public function json($Arr)
  {
    if ( is_array($Arr) )
    {

      echo json_encode($Arr);
    }
    else {
      $this->err500();
    }
  }

  public function ok()
  {
    echo json_encode([
      'status' => 200,
      'Message' => 'Ok'
    ]);
  }

  public function err500($reason = 'Param must be an array')
  {
    echo json_encode([
      'status' => 500,
      'message' => 'Internal Server Error',
      'reason' => $reason
    ]);
  }

}
