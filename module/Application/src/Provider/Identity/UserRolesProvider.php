<?php

namespace Application\Provider\Identity;

use BjyAuthorize\Provider\Identity\ProviderInterface;
use BjyAuthorize\Exception\InvalidRoleException;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\Authentication\Storage\Session as SessionStorage; 
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use Zend\Authentication\AuthenticationService;

class UserRolesProvider implements ProviderInterface {
	
	/**
	 * @var AuthenticationService
	 */
	protected $authService;
	
	/**
	 * @var string|\Zend\Permissions\Acl\Role\RoleInterface
	 */
	protected $defaultRole = 'guest';
	
	/**
	 * @var string|\Zend\Permissions\Acl\Role\RoleInterface
	 */
	protected $authenticatedRole;
	
	protected $adapter;
	
	/**
	 * @param AuthenticationService $authService
	 */
	public function __construct(AuthenticationService $authService)
	{
		$this->authService = $authService;
	}
	
	/**
	 * Get the rule that's used if you're not authenticated
	 *
	 * @return string|\Zend\Permissions\Acl\Role\RoleInterface
	 */
	public function getDefaultRole()
	{
		return $this->defaultRole;
	}
	
	public function getIdentityRoles() {

		if (! $identity = $this->authService->getIdentity()) {
			return array($this->defaultRole);
		}
		
		// get roles associated with the logged in user
		$userVals = $this->authService->getStorage()->read();
		$role = $userVals['role'];
		return $role;
		
	}
	
	public function setDbAdapter($dbAdapter){
		$this->adapter = $dbAdapter;
	}
	
	public function getDbAdapter(){
		return $this->adapter;
	}
	
	 
	 
}

?>