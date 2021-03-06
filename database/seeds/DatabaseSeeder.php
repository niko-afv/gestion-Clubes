<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(ActivityCategoriesTableSeeder::class);
        $this->call(FieldsTableSeeder::class);
        $this->call(PositionsTableSeeder::class);
        $this->call(ZonesTableSeeder::class);
        $this->call(ClubsTableSeeder::class);
        $this->call(MemberPositionsTableSeeder::class);
        $this->call(MembersTableSeeder::class);
        $this->call(ProfilesTableSeeder::class);
        $this->call(LogTypesTableSeeder::class);
        $this->call(UnitsTableSeeder::class);
        $this->call(EventsTableSeeder::class);
    }
}
