<?php
/* Copyright (C) 2015   Jean-François Ferry     <jfefe@aternatik.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

use Luracast\Restler\RestException;

//require_once DOL_DOCUMENT_ROOT . '/contact/class/contact.class.php';

/**
 * API class for users
 *
 * @access protected 
 * @class  DolibarrApiAccess {@requires user,external}
 */
class Users extends DolibarrApi
{
	/**
	 *
	 * @var array   $FIELDS     Mandatory fields, checked when create and update object 
	 */
	static $FIELDS = array(
		'login'
	);

	/**
	 * @var User $user {@type User}
	 */
	public $useraccount;

	/**
	 * Constructor
	 */
	function __construct() {
		global $db, $conf;
		$this->db = $db;
		$this->useraccount = new User($this->db);
	}

	/**
	 * Get properties of an user object
	 *
	 * Return an array with user informations
	 *
	 * @param 	int 	$id ID of user
	 * @return 	array|mixed data without useless information
	 * 
	 * @throws 	RestException
	 */
	function get($id) {
		//if (!DolibarrApiAccess::$user->rights->user->user->lire) {
			//throw new RestException(401);
		//}

		$result = $this->useraccount->fetch($id);
		if (!$result)
		{
			throw new RestException(404, 'User not found');
		}

		if (!DolibarrApi::_checkAccessToResource('user', $this->useraccount->id, 'user'))
		{
			throw new RestException(401, 'Access not allowed for login ' . DolibarrApiAccess::$user->login);
		}

		return $this->_cleanObjectDatas($this->useraccount);
	}
	
	
	/**
	 * Create user account
	 *
	 * @param array $request_data New user data
	 * @return int
	 */
	function post($request_data = NULL) {
	    // check user authorization
	    //if(! DolibarrApiAccess::$user->rights->user->creer) {
	    //   throw new RestException(401, "User creation not allowed");
	    //}
	    // check mandatory fields
	    /*if (!isset($request_data["login"]))
	        throw new RestException(400, "login field missing");
	    if (!isset($request_data["password"]))
	        throw new RestException(400, "password field missing");
	    if (!isset($request_data["lastname"]))
	         throw new RestException(400, "lastname field missing");*/
	    //assign field values
        $xxx=var_export($request_data, true);
        dol_syslog("xxx=".$xxx);
        foreach ($request_data as $field => $value)
	    {
	          $this->useraccount->$field = $value;
	    }
	    
        $result = $this->useraccount->create(DolibarrApiAccess::$user);
	    if ($result <=0) {
	         throw new RestException(500, "User not created : ".$this->useraccount->error);
	    }
	    return array('id'=>$result);
    }                
	
    
	/**
	 * Update account
	 *
	 * @param int   $id             Id of account to update
	 * @param array $request_data   Datas   
	 * @return int 
	 */
	function put($id, $request_data = NULL) {
		//if (!DolibarrApiAccess::$user->rights->user->user->creer) {
			//throw new RestException(401);
		//}

		$result = $this->useraccount->fetch($id);
		if (!$result)
		{
			throw new RestException(404, 'Account not found');
		}

		if (!DolibarrApi::_checkAccessToResource('user', $this->useraccount->id, 'user'))
		{
			throw new RestException(401, 'Access not allowed for login ' . DolibarrApiAccess::$user->login);
		}

		foreach ($request_data as $field => $value)
		{
			$this->useraccount->$field = $value;
		}

		if ($this->useraccount->update(DolibarrApiAccess::$user, 1))
			return $this->get($id);

        return false;
    }

    /**
	 * add user to group
	 *
	 * @param   int     $id User ID
	 * @param   int     $group Group ID
	 * @return  int
     * 
	 * @url	GET {id}/setGroup/{group}
	 */
	function setGroup($id, $group) {
		//if (!DolibarrApiAccess::$user->rights->user->user->supprimer) {
			//throw new RestException(401);
		//}
        $result = $this->useraccount->fetch($id);
        if (!$result)
        {
          throw new RestException(404, 'User not found');
        }
    
        if (!DolibarrApi::_checkAccessToResource('user', $this->useraccount->id, 'user'))
        {
          throw new RestException(401, 'Access not allowed for login ' . DolibarrApiAccess::$user->login);
        }
    
        return $this->useraccount->SetInGroup($group,1);
    }

	/**
	 * Delete account
	 *
	 * @param   int     $id Account ID
	 * @return  array
	 */
	function delete($id) {
		//if (!DolibarrApiAccess::$user->rights->user->user->supprimer) {
			//throw new RestException(401);
		//}
		$result = $this->useraccount->fetch($id);
		if (!$result)
		{
			throw new RestException(404, 'User not found');
		}

		if (!DolibarrApi::_checkAccessToResource('user', $this->useraccount->id, 'user'))
		{
			throw new RestException(401, 'Access not allowed for login ' . DolibarrApiAccess::$user->login);
		}

		return $this->useraccount->delete($id);
	}

	/**
	 * Validate fields before create or update object
     * 
	 * @param   array $data Data to validate
	 * @return  array
	 * @throws RestException
	 */
	function _validate($data) {
		$account = array();
		foreach (Users::$FIELDS as $field)
		{
			if (!isset($data[$field]))
				throw new RestException(400, "$field field missing");
			$account[$field] = $data[$field];
		}
		return $account;
	}
}
