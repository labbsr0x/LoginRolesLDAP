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
use Piwik\Plugins\UsersManager\API as UsersManagerAPI;

/**
 * @group LoginRolesLdap
 * @group LoginRolesLdap_Integration
 * @group LoginRolesLdap_BackwardsCompatibilityTest
 */
class BackwardsCompatibilityTest extends LdapIntegrationTest
{
    public function setUp()
    {
        parent::setUp();

        Config::getInstance()->LoginRolesLdap = array(
            'serverUrl' => @Config::getInstance()->LoginRolesLdap_testserver['hostname'] ?: self::SERVER_HOST_NAME,
            'ldapPort' => @Config::getInstance()->LoginRolesLdap_testserver['port'] ?: self::SERVER_PORT,
            'baseDn' => self::SERVER_BASE_DN,
            'adminUser' => 'cn=fury,' . self::SERVER_BASE_DN,
            'adminPass' => 'secrets',
            'useKerberos' => 'false'
        );

        UsersManagerAPI::getInstance()->addUser(self::TEST_LOGIN, self::TEST_PASS, 'billionairephilanthropistplayboy@starkindustries.com', $alias = false);
    }

    public function testAuthenticationWithOldServerConfig()
    {
        $ldapAuth = LdapAuth::makeConfigured();
        $ldapAuth->setLogin(self::TEST_LOGIN);
        $ldapAuth->setPassword(self::TEST_PASS);
        $authResult = $ldapAuth->authenticate();

        $this->assertEquals(1, $authResult->getCode());
    }
}