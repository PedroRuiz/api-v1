<?php
defined('BASEPATH') OR exit('No direct script access allowed');

define('TABLE', 'custom_emails');

class Clientemails extends MY_Controller {



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
  
  function index($id,$params)
	{
    switch ($_SERVER['REQUEST_METHOD']) {
      case 'GET':
        if ( ! $params ) 
        {
          $this->get_all( $id );
        } else {
          $this->get_by_id( $id , $params );
        }
        break;

      case 'POST':
        $this->post_email( $id );
        break;

      case 'DELETE':
        $this->delete_email( $id  );
        break;

      case 'PUT':
        $this->update_email( $id );
        break;

      default:
        $this->get_all();
        break;
    }
  }

  function get_all($id)
  {
      $this->json(
        $this->db->where( 'id_custom' , $id )->get(TABLE)->result_array()
      );
  }

  function get_by_id( $id , $params )
  {
    $where = array(
      'id' => $params[0],
      'id_custom' => $id
    );

    $this->json(
      $this->db->where( $where )->get(TABLE)->result_array()
    );

  }

  function post_email() {
    $body = json_decode(file_get_contents('php://input'));

    $body = json_decode(json_encode($body) , true);


    if ( $this->filter_email( $body['email'] ) )
    {

        if ( ! $this->db->insert( TABLE , $body ) )
        {
            $this->err500( $this->db->error() );
        }
        else
        {
            $this->ok();
        }
    }
    else 
    {
        $this->err500('EMAIL bad format');
    }
  }

  /**
   * where id isn't id_custom.
   * 
   * it means id (auto_increment field and primary key) 
   * of custom_addresses table;
   */
  function delete_email( $id  )
  {
    $where = $params[0];

    if ( ! $this->db->where( 'id' , $id )->delete(TABLE) )
    {
      $this->err500( $this->db->error() );
    }
    else
    {
      $this->ok();
    }
  }

  /**
   * where id isn't id_custom.
   * 
   * it means id (auto_increment field and primary key) 
   * of custom_addresses table;
   */
  function update_email( $id )
  {
    $body = json_decode(file_get_contents('php://input'));

    $body = json_decode(json_encode($body) , true);

    if ( $this->filter_email( $body['email'] ) )
    {
        if ( ! $this->db->set($body)->where( 'id' , $id )->update( TABLE ) )
        {
        $this->err500($this->db->error());
        } 
        else 
        {
        $this->ok();
        }
    }
    else 
    {
        $this->err500('EMAIL bad format');
    }
  }

  function filter_email( $email )
  {
      return filter_var( $email , FILTER_VALIDATE_EMAIL);
  }



}

