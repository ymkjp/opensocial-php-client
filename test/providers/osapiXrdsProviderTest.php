<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */

/**
 * osapiXrdsProvider test case.
 */
class osapiXrdsProviderTest extends PHPUnit_Framework_TestCase {
  
  /**
   * Tests osapiXrdsProvider->__construct()
   */
  public function test__construct() {
    $headerResponse = "HTTP/1.1 200 OK\r\nX-XRDS-Location: http://www.partuza.nl/xrds\r\n\r\nsomerandomcontent";
    
    $xrds = '<XRDS xmlns="xri://$xrds">
        <XRD xml:id="oauth" xmlns:simple="http://xrds-simple.net/core/1.0" xmlns="xri://$xrd*($v*2.0)" version="2.0">
          <Type>xri://$xrds*simple</Type>
          <Expires>2009-03-08T23:17:12Z</Expires>
          <Service priority="10">
            <Type>http://oauth.net/core/1.0/endpoint/request</Type>
            <Type>http://oauth.net/core/1.0/parameters/auth-header</Type>
            <Type>http://oauth.net/core/1.0/parameters/uri-query</Type>
            <Type>http://oauth.net/core/1.0/signature/PLAINTEXT</Type>
            <URI>http://www.partuza.nl/oauth/request_token</URI>
          </Service>
          <Service priority="10">
            <Type>http://oauth.net/core/1.0/endpoint/authorize</Type>
            <Type>http://oauth.net/core/1.0/parameters/uri-query</Type>
            <URI>http://www.partuza.nl/oauth/authorize</URI>
          </Service>
          <Service priority="10">
            <Type>http://oauth.net/core/1.0/endpoint/access</Type>
            <Type>http://oauth.net/core/1.0/parameters/auth-header</Type>
            <Type>http://oauth.net/core/1.0/parameters/uri-query</Type>
            <Type>http://oauth.net/core/1.0/signature/PLAINTEXT</Type>
            <URI>http://www.partuza.nl/oauth/access_token</URI>
          </Service>
          <Service priority="10">
            <Type>http://oauth.net/core/1.0/endpoint/resource</Type>
            <Type>http://oauth.net/core/1.0/parameters/auth-header</Type>
            <Type>http://oauth.net/core/1.0/parameters/uri-query</Type>
            <Type>http://oauth.net/core/1.0/signature/HMAC-SHA1</Type>
          </Service>
          <Service priority="10">
            <Type>http://oauth.net/discovery/1.0/consumer-identity/static</Type>
            <LocalID>0685bd9184jfhq22</LocalID>
          </Service>
        </XRD>
        <XRD xmlns:simple="http://xrds-simple.net/core/1.0" xmlns="xri://$xrd*($v*2.0)" xmlns:os="http://ns.opensocial.org/" version="2.0">
            <Type>xri://$xrds*simple</Type>
            <Service>
              <Type>http://portablecontacts.net/spec/1.0</Type>
              <URI>http://modules.partuza.nl/social/rest/people</URI>
            </Service>
            <Service>
              <Type>http://ns.opensocial.org/people/0.8</Type>
              <os:URI-Template>http://modules.partuza.nl/social/rest/people/{guid}/{selector}{-prefix|/|pid}?format=atom</os:URI-Template>
              <URI>http://modules.partuza.nl/social/rest/people</URI>
            </Service>
            <Service>
              <Type>http://ns.opensocial.org/activities/0.8</Type>
              <os:URI-Template>http://modules.partuza.nl/social/rest/activities/{guid}/{selector}?format=atom</os:URI-Template>
              <URI>http://modules.partuza.nl/social/rest/activities</URI>
            </Service>
            <Service>
              <Type>http://ns.opensocial.org/appdata/0.8</Type>
              <os:URI-Template>http://modules.partuza.nl/social/rest/appdata/{guid}/{selector}?format=atom</os:URI-Template>
              <URI>http://modules.partuza.nl/social/rest/activities</URI>
            </Service>
            <Service>
              <Type>http://ns.opensocial.org/messages/0.8</Type>
              <os:URI-Template>http://modules.partuza.nl/social/rest/messages/{guid}/outbox/{msgid}</os:URI-Template>
              <URI>http://modules.partuza.nl/social/rest/messages</URI>
            </Service>
            <Service>
              <Type>http://ns.opensocial.org/rest/0.8</Type>
              <URI>http://modules.partuza.nl/social/rest</URI>
            </Service>
            <Service>
              <Type>http://ns.opensocial.org/rpc/0.8</Type>
              <URI>http://modules.partuza.nl/social/rpc</URI>
            </Service>
            <Service priority="0">
              <Type>http://specs.openid.net/auth/2.0/signon</Type>
              <Type>http://openid.net/signon/1.1</Type>
              <URI>http://www.partuza.nl/openid/auth</URI>
            </Service>
            <Service priority="10">
              <Type>http://oauth.net/discovery/1.0</Type>
              <URI>#oauth</URI>
            </Service>
        </XRD>
    </XRDS>';
    
    $responses = array($xrds, $headerResponse);
    
    $httpProvider = new osapiLocalHttpProvider($responses);
    $storage = new osapiFileStorage("/tmp/osapi");
    $url = "http://www.partuza.nl";
    
    $requestTokenUrl = "http://www.partuza.nl/oauth/request_token";
    $authorizeUrl = "http://www.partuza.nl/oauth/authorize";
    $accessTokenUrl = "http://www.partuza.nl/oauth/access_token";
    $restEndpoint = "http://modules.partuza.nl/social/rest";
    $rpcEndpoint = "http://modules.partuza.nl/social/rpc";
    
    $xrdsProvider = new osapiXrdsProvider($url, $storage, $httpProvider);
    
    $this->assertEquals($requestTokenUrl, $xrdsProvider->requestTokenUrl);
    $this->assertEquals($authorizeUrl, $xrdsProvider->authorizeUrl);
    $this->assertEquals($accessTokenUrl, $xrdsProvider->accessTokenUrl);
    $this->assertEquals($restEndpoint, $xrdsProvider->restEndpoint);
    $this->assertEquals($rpcEndpoint, $xrdsProvider->rpcEndpoint);
    
    $storage->delete($url . ":xrds");
  }
}
