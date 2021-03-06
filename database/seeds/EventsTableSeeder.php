<?php

use Illuminate\Database\Seeder;

class EventsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('events')->truncate();


        $events = [
            [
                'id' => 1,
                'name' => 'Campamento Zonal Oriente',
                'description' => 'Campamento anual realizado por la Zona Oriente',
                'start' => '2019-05-03',
                'end' => '2019-05-05'
            ],
        ];

        foreach ($events as $event){
            DB::table('events')->insert([
                'id' => $event['id'],
                'name' => $event['name'],
                'description' => $event['description'],
                'start' => $event['start'],
                'end' => $event['end']
            ]);
        }
    }
}
