<?php
/*
 * This file was written by Che-Huai Lin and placed in the public domain.
 */

class OC_GROUP_PWAUTH extends OC_Group_Backend implements OC_Group_Interface {

	private $user_pwauth;

	public function __construct() {
		$this->user_pwauth = new OC_USER_PWAUTH();
	}

	/**
	 * @brief is user in group?
	 * @param $uid uid of the user
	 * @param $gid gid of the group
	 * @returns true/false
	 *
	 * Checks whether the user is member of a group or not.
	 */
	public function inGroup($uid, $gid) {
		$groupInfo = posix_getgrnam($gid);
		if ($group_info) {
			$groupMembers = $groupInfo["members"];
			return in_array($uid, $groupMembers);
		} else {
			return false;
		}
	}

	/**
	 * @brief get a list of all users in a group
	 * @returns array with user ids
	 */
	public function usersInGroup($gid, $search = '', $limit = -1, $offset = 0) {
		if ($limit == -1)
			$limit = null;
		$groupInfo = posix_getgrnam($gid);
		if ($groupInfo) {
			$groupUsers = array_slice($groupInfo["members"], $offset, $limit);
			return $groupUsers;
		} else {
			return array();
		}
	}

	/**
	 * @brief get a list of all display names in a group
	 * @returns array with display names (value) and user ids(key)
	 */
	public function displayNamesInGroup($gid, $search, $limit, $offset) {
		if (!$this->groupExists($gid)) {
			return array();
		}
		$groupUsers = $this->usersInGroup($gid, $search, $limit, $offset);
		$displayNames = array();
		foreach ($groupUsers as $uid) {
			$name = $this->user_pwauth->getUserDisplyName($uid);
			array_push($displayNames, $name);
		}
		return $displayNames;
	}

	/**
	 * @brief get a list of all groups
	 * @returns array with group names
	 *
	 * Returns a list with all groups
	 */
	public function getGroups($search = '', $limit = -1, $offset = 0) {
		$users = $this->user_pwauth->getUsers('', -1, 0);
		$allGroups = array();
		foreach($users as $user) {
			$groups = $this->getUserGroups($user);
			foreach ($groups as $group) {
				if ($group != $user) {
					array_push($allGroups, $group);
				}
			}
		}
		$allGroups = array_unique($allGroups);
		return array_slice($allGroups, $offset, $limit);
	}

	public function groupMatchesFilter($group) {
		return true; // TODO: implement this function
	}

	/**
	 * check if a group exists
	 * @param string $gid
	 * @return bool
	 */
	public function groupExists($gid) {
		return posix_getgrnam($gid) != false;
	}

	/**
	 * @brief Get all groups a user belongs to
	 * @param $uid Name of the user
	 * @returns array with group names
	 *
	 * This function fetches all groups a user belongs to. It does not check
	 * if the user exists at all.
	 */
	public function getUserGroups($uid) {
		// use the command "groups <uid>" to find the groups
		// the format of output is as follows:
		// <user> : <group1> <group2> <group3> ...
		$output = shell_exec("groups " . escapeshellarg($uid));
		$fields = explode(" ", $output);
		// fields[0] = <user>, field[1] = ':', field[2] = <group1>, ...
		// strip fields[0] and fields[1] to get the group array
		if (count($fields) < 2) { // error
			return array();
		}
		$groups = array_slice($fields, 2);
		return $groups;
	}

	/**
	* @brief Check if backend implements actions
	* @param $actions bitwise-or'ed actions
	* @returns boolean
	*
	* Returns the supported actions as int to be
	* compared with OC_USER_BACKEND_CREATE_USER etc.
	*/
	public function implementsActions($actions) {
		return (bool)(OC_GROUP_BACKEND_GET_DISPLAYNAME	& $actions);
	}
}

?>
