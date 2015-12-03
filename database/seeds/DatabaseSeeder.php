<?php

use App\Order;
use App\OrderProduct;
use App\Product;
use App\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{

    /**
     * @var array
     */
    private $tables = [
        'users',
        'products',
        'orders',
        'order_products'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $this->cleanDatabase();

        Model::unguard();

         $this->call(UsersTableSeeder::class);
         $this->call(ProductsTableSeeder::class);
         $this->call(OrdersTableSeeder::class);
         $this->call(OrderProductsTableSeeder::class);

        Model::reguard();
    }

    public function cleanDatabase()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach($this->tables as $tableName) {
            DB::table($tableName)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}


/**
 * Class UsersTableSeeder
 */
class UsersTableSeeder extends Seeder {

    public function run()
    {
        $faker = Faker::create();

        foreach(range(1, 10) as $index)
        {
            User::create([
                'name' => $faker->sentence(1),
                'email' => $faker->email,
                'location' => $faker->sentence(1),
                'password' => bcrypt(123456),
            ]);
        }
    }

}


/**
 * Class ProductsTableSeeder
 */
class ProductsTableSeeder extends Seeder {

    public function run()
    {
        $faker = Faker::create();

        $userIds = User::lists('id')->all();

        foreach(range(1, 10) as $index)
        {
            Product::create([
                'user_id' => $faker->randomElement($userIds),
                'name' => $faker->word(),
                'type' => $faker->numberBetween(0,4),
                'image_path' => $faker->word(),
                'option_strength' => $faker->numberBetween(0,4),
                'option_milk' => $faker->numberBetween(0,4),
                'option_sugar' => $faker->numberBetween(0,4),
                'option_mug' => $faker->boolean(),
            ]);
        }
    }

}

/**
 * Class OrdersTableSeeder
 */
class OrdersTableSeeder extends Seeder {

    public function run()
    {
        $faker = Faker::create();

        $userIds = User::lists('id')->all();

        foreach(range(1, 10) as $index)
        {
            Order::create([
                'user_id' => $faker->randomElement($userIds),
                'location' => $faker->sentence(),
                'delivered_at' => $faker->dateTime(),
                'delivery_status' => $faker->numberBetween(0,10)
            ]);
        }
    }

}


/**
 * Class OrderProductsTableSeeder
 */
class OrderProductsTableSeeder extends Seeder {

    public function run()
    {
        $faker = Faker::create();

        $orderIds = Order::lists('id')->all();

        foreach(range(1, 10) as $index)
        {
            OrderProduct::create([
                'order_id' => $faker->randomElement($orderIds),
                'name' => $faker->word(),
                'type' => $faker->numberBetween(0,4),
                'image_path' => $faker->word(),
                'option_strength' => $faker->numberBetween(0,4),
                'option_milk' => $faker->numberBetween(0,4),
                'option_sugar' => $faker->numberBetween(0,4),
                'option_mug' => $faker->boolean(),
            ]);
        }
    }

}


