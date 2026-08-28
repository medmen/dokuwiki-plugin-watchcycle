<?php

/**
 * Limiter tests for the watchcycle plugin
 *
 * @group plugin_watchcycle
 * @group plugins
 */
class limiter_plugin_watchcycle_test extends DokuWikiTest
{
    protected $pluginsEnabled = ['watchcycle', 'sqlite'];

    public function test_lastMailLimiter()
    {
        global $conf;

        /** @var helper_plugin_watchcycle_db $dbHelper */
        $dbHelper = plugin_load('helper', 'watchcycle_db');
        $this->assertNotNull($dbHelper);

        $sqlite = $dbHelper->getDB();
        $this->assertNotNull($sqlite);

        $page = 'test:limiter_unit_test';
        $sqlite->query('DELETE FROM watchcycle WHERE page=?', $page);

        $this->assertEquals(0, $dbHelper->getLastMail($page));

        $sqlite->query(
            'INSERT INTO watchcycle (page, maintainer, cycle, last_maintainer_rev, uptodate, last_mail) VALUES (?, ?, ?, ?, ?, ?)',
            [$page, 'testuser1', 30, time() - 40 * 86400, 0, 0]
        );

        $this->assertEquals(0, $dbHelper->getLastMail($page));

        $timestamp = time() - 500;
        $dbHelper->updateLastMail($page, $timestamp);
        $this->assertEquals($timestamp, $dbHelper->getLastMail($page));
    }
}
