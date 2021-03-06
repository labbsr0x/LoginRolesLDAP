<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\LoginRolesLdap\tests\Integration;

use Piwik\Config;
use Piwik\Plugins\LoginRolesLdap\Auth\LdapAuth;

/**
 * @group LoginRolesLdap
 * @group LoginRolesLdap_Integration
 * @group LoginRolesLdap_MultipleServersTest
 */
class MultipleServersTest extends LdapIntegrationTest
{
    public function setUp()
    {
        parent::setUp();

        Config::getInstance()->LoginRolesLdap_dummyserver1 = array(
            'hostname' => "notanldaphost.com",
            'port' => self::SERVER_PORT,
            'base_dn' => self::SERVER_BASE_DN,
            'admin_user' => 'cn=fury,' . self::SERVER_BASE_DN,
            'admin_pass' => 'secrets'
        );
        Config::getInstance()->LoginRolesLdap_dummyserver2 = array(
            'hostname' => "localhost",
            'port' => 999,
            'base_dn' => self::SERVER_BASE_DN,
            'admin_user' => 'cn=fury,' . self::SERVER_BASE_DN,
            'admin_pass' => 'secrets'
        );
    }

    public function testAuthenticateSucceedsWhenFirstServerWorksButOthersFailToConnect()
    {
        Config::getInstance()->LoginRolesLdap['servers'] = array('testserver', 'dummyserver1', 'dummyserver2');

        $this->doAuthTest();
    }

    public function testAuthenticateSucceedsWhenOneServerSucceedsButOthersFailToConnect()
    {
        Config::getInstance()->LoginRolesLdap['servers'] = array('dummyserver1', 'testserver', 'dummyserver2');

        $this->doAuthTest();

        Config::getInstance()->LoginRolesLdap['servers'] = array('dummyserver1', 'dummyserver2', 'testserver');

        $this->doAuthTest();
    }

    /**
     * @expectedException \Piwik\Plugins\LoginRolesLdap\Ldap\Exceptions\ConnectionException
     * @expectedExceptionMessageContains Could not connect to any of the
     */
    public function testAuthenticateFailsWhenAllServersFailToConnect()
    {
        Config::getInstance()->LoginRolesLdap['servers'] = array('dummyserver1', 'dummyserver2');

        $this->doAuthTest($expectCode = 0);
    }

    private function doAuthTest($expectCode = 1)
    {
        $ldapAuth = LdapAuth::makeConfigured();
        $ldapAuth->setLogin(self::TEST_LOGIN);
        $ldapAuth->setPassword(self::TEST_PASS);
        $authResult = $ldapAuth->authenticate();

        $this->assertEquals($expectCode, $authResult->getCode());
    }
}