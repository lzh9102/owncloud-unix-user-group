<?php

class OC_GROUP_PWAUTH extends BackendUtility implements \OCP\GroupInterface {

	public function __construct(Access $access) {
		parent::__construct($access);
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
			$groupUsers = array_slice($groupInfo["members"], $offset, $limit)
			return $groupInfo["members"];
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
			$name = $this->getUserDisplyName($uid);
			array_push($displayNames, $name);
		}
		return $displayNames;
	}

	private function getUserDisplyName($uid) {
		$userInfo = posix_getpwnam($user);
		if (!$userInfo) {
			return $uid; // cannot find user info, use uid as display name
		}
		// gecos is a comma separated list
		// the first fields (0) is the user's full name
		$gecos = $user["gecos"];
		$fields = explode(",", $gecos);
		return $fields[0];
	}

	/**
	 * @brief get a list of all groups
	 * @returns array with group names
	 *
	 * Returns a list with all groups
	 */
	public function getGroups($search = '', $limit = -1, $offset = 0) {
		$groups = array();
		foreach(posix_getgroups() as $posixGroupID) {
			$groupInfo = posix_getgrgid($posixGroupID);
			$groupName = $groupInfo["name"];
			array_push($groups, $groupName);
		}
		return $groups;
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
		$groups = array();
		foreach(posix_getgroups() as $posixGroupID) {
			$groupInfo = posix_getgrgid($posixGroupID);
			$groupMembers = $groupInfo["members"];
			if (in_array($uid, $groupMembers)) {
				$groupName = $groupInfo["name"];
				array_push($groups, $groupName);
			}
		}
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
