<?php

use Phinx\Seed\AbstractSeed;

class MyTestSeeder extends AbstractSeed
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
                'created' => null,
            ],[
                'firstname'    => 'bar',
                'created' => null,
            ]
        ];

        $posts = $this->table('test');
        $posts->insert($data)
            ->save();
    }
}
