<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 判断是否存在 admin 账号，不存在则创建一个 admin 账号
        $this->createUserAdmin();

    }

    /**
     * @description: 创建一个 admin 账号
     * @param {*}
     * @return {*}
     */
    public function createUserAdmin()
    {
        $userAdmin = User::find(1);
        if ($userAdmin) {
            return;
        }

        DB::table('users')->insert([
            'name'     => 'admin',
            'start'    => 1,
            'password' => Hash::make('admin'),
        ]);

    }
}