<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'OnlineTestCase.php';

class PersonTest extends OnlineTestCase {
  public function testGetSelf() {
    $this->assertSupportedMethod('people.get');

    $batch = $this->suite->osapi->newBatch();
    $batch->add($this->suite->osapi->people->get(array('userId' => '@me', 'groupId' => '@self')), 'self');
    $result = $batch->execute();
    $person = $result['self'];

    if ($person instanceof osapiError) {
      $this->fail($person->getErrorMessage());
      return;
    }
    
    $this->assertEquals($this->suite->USER_A_ID, $person->getId());
    $this->assertEquals($this->suite->USER_A_DISPLAY_NAME, $person->getDisplayName());
  }

  public function testGetSelfById() {
    $this->assertSupportedMethod('people.get');
    $this->assertSupportedMethod('people.get_by_id');

    $batch = $this->suite->osapi->newBatch();
    $batch->add($this->suite->osapi->people->get(array('userId' => $this->suite->USER_A_ID, 'groupId' => '@self')), 'self');
    $result = $batch->execute();
    $person = $result['self'];

    if ($person instanceof osapiError) {
      $this->fail($person->getErrorMessage());
    }
    
    $this->assertEquals($this->suite->USER_A_ID, $person->getId());
    $this->assertEquals($this->suite->USER_A_DISPLAY_NAME, $person->getDisplayName());
  }
}