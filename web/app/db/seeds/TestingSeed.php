<?php

use Phinx\Seed\AbstractSeed;

/**
 * RECOMMENDED: Use the command line to create the empty migration file
 * cli example: sudo make create-seed NAME='TestingSeed'
 *
 */
class TestingSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data = [
            [
                'firstname'    => 'foo',
                'reg_date' => null,
            ],[
                'firstname'    => 'bar',
                'reg_date' => null,
            ]
        ];

        $posts = $this->table('test');
        $posts->insert($data)
            ->save();
    }
}
