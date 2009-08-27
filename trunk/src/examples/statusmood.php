<?php
/*
 * Copyright 2008 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */ 

	require_once "__init__.php";
	
	if ($osapi) {
	  if ($strictMode) {
	    $osapi->setStrictMode($strictMode);
	  }
	  
	  // Start a batch so that many requests may be made at once.
	  $batch = $osapi->newBatch();
	  
	  // Fetch the status mood MySpace specific.
	  $batch->add($osapi->statusmood->get(array()), 'get_status_mood');
	  
	  // Set the status mood MySpace specific.
	  $params = array('statusMood'=>
	      array(
	      	'moodName' =>'excited',
	      	'status' => 'Working on PHP SDK'
	      )
	   );
	  
	  $batch->add($osapi->statusmood->update($params), 'set_status_mood');
	  
	  // Send the batch request.
	  $result = $batch->execute();
	?>
	
	<h1>StatusMood API Examples</h1>
	<h2>Request:</h2>
	<p>This sample fetched statusmood(msypace specific), update statusmood(msypace specific)</p>
	<?php
	
        require_once('response_block.php');
	}
?>