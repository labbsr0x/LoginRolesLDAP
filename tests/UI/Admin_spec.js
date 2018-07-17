/*!
 * Piwik - free/libre analytics platform
 *
 * LoginRolesLdap admin page screenshot tests.
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

describe("LoginRolesLdap_Admin", function () {
    this.timeout(0);

    this.fixture = "Piwik\\Tests\\Fixtures\\OneVisitorTwoVisits";

    before(function () {
        testEnvironment.pluginsToLoad = ['LoginRolesLdap'];
        testEnvironment.configOverride = {
            LoginRolesLdap: {
                servers: ['testserver'],
                new_user_default_sites_view_access: '1,2',
                enable_synchronize_access_from_ldap: 1
            },
            LoginRolesLdap_testserver: {
                hostname: 'localhost',
                port: 389,
                base_dn: 'dc=avengers,dc=shield,dc=org',
                admin_user: 'cn=fury,dc=avengers,dc=shield,dc=org',
                admin_pass: 'secrets'
            }
        };
        testEnvironment.save();
    });

    var ldapAdminUrl = "?module=LoginRolesLdap&action=admin&idSite=1&period=day&date=yesterday";

    it("should load correctly and allow testing the filter and group fields", function (done) {
        expect.screenshot('admin_page').to.be.captureSelector('#content', function (page) {
            page.load(ldapAdminUrl, 2000);

            page.sendKeys('input#required_member_of', 'a');
            page.sendKeys('input#ldap_user_filter', 'a');

            page.evaluate(function () {
                $('input#required_member_of').val('cn=avengers,dc=avengers,dc=shield,dc=org').trigger('input');
                $('input#ldap_user_filter').val('(objectClass=person)').trigger('input');
            });

            page.evaluate(function () {
                $('[piwik-login-roles-ldap-testable-field] [piwik-save-button] input').click();
            }, 1000);
        }, done);
    });
});
