<?php

use Phinx\Migration\AbstractMigration;

/**
 * RECOMMENDED: Use the command line to create the empty migration file
 * cli example: sudo make create-migration NAME='TestingMigration'
 *
 */
class TestingMigration extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        // execute()
        $this->execute(
            'CREATE TABLE test (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
firstname VARCHAR(30) NOT NULL,
reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)'
        );
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // execute()
        $this->execute(
            'DROP TABLE test'
        );
    }
}
