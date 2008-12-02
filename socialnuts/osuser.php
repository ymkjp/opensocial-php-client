<?php
/*
 * +--------------------------------------------------------------------------+
 * | OpenSocial PHP5 client                                           |
 * +--------------------------------------------------------------------------+
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements. See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership. The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied. See the License for the
 * specific language governing permissions and limitations under the License.
 */

class OpenSocialUser {
        var $uid = 0;
        var $firstName = 0;
	var $lastName = 0;
	var $thumbnailUrl = '';

        // define app-specific fields below e.g. nutty points

	function OpenSocialUser($uid=0) {
	    if ($uid>0) {
	        $this->uid = $uid;
	    }
	}

	function setNames($firstName, $lastName) {
	    $this->firstName = $firstName;
	    $this->lastName = $lastName;
	}

	function setThumbnail($thumbnailUrl) {
	    $this->thumbnailUrl = $thumbnailUrl;
	}

	function getFirstName() {
	    return $this->firstName;
	}

	function getLastName() {
	    return $this->lastName;
	}

	function getThumbnail() {
	    return $this->thumbnailUrl;
	}

        // define app-specific functions below 
	function getUserInfo() {
            // fetch user info from your own db
	}

	function addUser() {
            // add user uid to your own db
        }

        function updateUser() {
            // update user in your own db for e.g. stats tracking
        }
}
