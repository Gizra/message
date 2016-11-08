<?php

namespace Drupal\Tests\message\Functional;

use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests admin menus for the message module.
 *
 * @group message_subscribe
 */
class MenuTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['message'];

  /**
   * Test that the menu links are working properly.
   */
  public function testMenuLinks() {
    $admin = $this->drupalCreateUser([], NULL, TRUE);
    $this->drupalLogin($admin);

    // Link should appear on main config page.
    $this->drupalGet(Url::fromRoute('system.admin_config'));
    $this->assertSession()->linkExists(t('Message'));

    // Link should be on the message-specific overview page.
    $this->drupalGet(Url::fromRoute('message.main_settings'));
    $this->assertSession()->linkExists(t('Message'));

    $this->clickLink(t('Message'));
    $this->assertSession()->statusCodeEquals(200);
  }

}
