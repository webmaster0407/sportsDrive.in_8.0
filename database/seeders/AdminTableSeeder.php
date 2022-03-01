<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admin')->insert([
           			'id'=>1,
           			'admin_name'=>'admin',
                    'admin_email'=>'admin@invitratech.com', 
                    'password'=>base64_encode('admin'),
                    'site_heading'=>'Sports Drive',
                    'remember_token'=>csrf_token(),
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
        ]);
    }
}
