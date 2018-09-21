<?php
defined('BASEPATH') OR exit('No direct script access allowed');

define('TABLE', 'custom_bank_accounts');

class Clientbanks extends MY_Controller {



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
        $this->post_bank( $id );
        break;

      case 'DELETE':
        $this->delete_bank( $id  );
        break;

      case 'PUT':
        $this->update_bank( $id );
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

  function post_bank( $id )
  {
    $body = json_decode(file_get_contents('php://input'));

    $body = json_decode(json_encode($body) , true);


    if ( $this->check_iban( $body['account'] ) )
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
        $this->err500('IBAN code does not match');
    }
  }


  /**
   * where id isn't id_custom.
   * 
   * it means id (auto_increment field and primary key) 
   * of custom_addresses table;
   */
  function delete_bank( $id  )
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
  function update_bank( $id )
  {
    $body = json_decode(file_get_contents('php://input'));

    $body = json_decode(json_encode($body) , true);

    if ( $this->check_iban( $body['account'] ) )
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
        $this->err500('IBAN code does not match');
    }
  }

    
function check_iban($iban)
{
    $iban = strtolower(str_replace(' ','',$iban));
    $Countries = array('al'=>28,'ad'=>24,'at'=>20,'az'=>28,'bh'=>22,'be'=>16,'ba'=>20,'br'=>29,'bg'=>22,'cr'=>21,'hr'=>21,'cy'=>28,'cz'=>24,'dk'=>18,'do'=>28,'ee'=>20,'fo'=>18,'fi'=>18,'fr'=>27,'ge'=>22,'de'=>22,'gi'=>23,'gr'=>27,'gl'=>18,'gt'=>28,'hu'=>28,'is'=>26,'ie'=>22,'il'=>23,'it'=>27,'jo'=>30,'kz'=>20,'kw'=>30,'lv'=>21,'lb'=>28,'li'=>21,'lt'=>20,'lu'=>20,'mk'=>19,'mt'=>31,'mr'=>27,'mu'=>30,'mc'=>27,'md'=>24,'me'=>22,'nl'=>18,'no'=>15,'pk'=>24,'ps'=>29,'pl'=>28,'pt'=>25,'qa'=>29,'ro'=>24,'sm'=>27,'sa'=>24,'rs'=>22,'sk'=>24,'si'=>19,'es'=>24,'se'=>24,'ch'=>21,'tn'=>24,'tr'=>26,'ae'=>23,'gb'=>22,'vg'=>24);
    $Chars = array('a'=>10,'b'=>11,'c'=>12,'d'=>13,'e'=>14,'f'=>15,'g'=>16,'h'=>17,'i'=>18,'j'=>19,'k'=>20,'l'=>21,'m'=>22,'n'=>23,'o'=>24,'p'=>25,'q'=>26,'r'=>27,'s'=>28,'t'=>29,'u'=>30,'v'=>31,'w'=>32,'x'=>33,'y'=>34,'z'=>35);

    if(strlen($iban) == $Countries[substr($iban,0,2)]){

        $MovedChar = substr($iban, 4).substr($iban,0,4);
        $MovedCharArray = str_split($MovedChar);
        $NewString = "";

        foreach($MovedCharArray AS $key => $value){
            if(!is_numeric($MovedCharArray[$key])){
                $MovedCharArray[$key] = $Chars[$MovedCharArray[$key]];
            }
            $NewString .= $MovedCharArray[$key];
        }

        if(bcmod($NewString, '97') == 1)
        {
            return true;
        }
        else{
            return false;
        }
    }
    else{
        return false;
    }   
}

}

?>



