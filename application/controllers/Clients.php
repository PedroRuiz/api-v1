<?php
defined('BASEPATH') OR exit('No direct script access allowed');

define('TABLE', 'custom_customers');

class Clients extends MY_Controller {



  function __construct()
  {
    parent::__construct();
  }

  function _remap( $method , $params = array() )
  {
    if ( ! method_exists( $this , $method ) )
    {
      $this->index($method, $params);
    } 
    else 
    {
      return call_user_func( array ( $this , $method ), $params );
    }
  }
  
  function index($id)
	{
    switch ($_SERVER['REQUEST_METHOD']) {
      case 'GET':
        if ( ! $id ) 
        {
          $this->get_all();
        } else {
          $this->get_by_id($id);
        }
        break;

      case 'POST':
        $this->post_customer();
        break;

      case 'DELETE':
        $this->delete_customer($id);
        break;

      case 'PUT':
        $this->update_customer($id);
        break;

      default:
        $this->get_all();
        break;
    }
  }


  function get_all()
  {
    $this->json(
      $this->db->get( TABLE )->result_array()
    );
  }

  function get_by_id($id)
  {
    $this->json(
      $this->db->where( 'id' , $id )->get( TABLE )->result_array()
    );
  }

  function post_customer()
  {
    $body = json_decode(file_get_contents("php://input"));

    if ( ! $this->db->insert( TABLE , $body ) )
    {
      $this->err500($this->db->error());
    }

  }

  function delete_customer($id)
  {
    if ( ! $this->db->where( 'id' , $id )->delete( TABLE ) )
    {
      $this->err500($this->db->error());
    } 
    else 
    {
      $this->ok();
    }
  }

  function update_customer($id)
  {
    $body = json_decode(file_get_contents('php://input'));

  
    if ( ! $this->db->set($body)->where( 'id' , $id )->update( TABLE ) )
    {
      $this->err500($this->db->error());
    } 
    else 
    {
      $this->ok();
    }
  
  }


}
