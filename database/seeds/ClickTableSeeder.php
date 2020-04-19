<?php

use App\Link;
use App\LinkClick;
use Illuminate\Database\Seeder;

class ClickTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(LinkClick::class, 5000)->create([
            'link_id' => app(Link::class)->whereNotNull('id')->first()->id
        ]);
    }
}
