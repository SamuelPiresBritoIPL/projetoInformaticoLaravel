<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PedidosSeeder extends Seeder
{
    private $numberOfAulas = 1;  

    private function newFakePedido($faker, $i)
    {
        return [
            'idUtilizador' =>  $faker->numberBetween(1, 99)];
    }
    private function newFakePedidoUcs($faker, $id)
    {
        return [
                'idPedidos' =>  $id,
                'idCadeira' =>  $faker->numberBetween(1, 15)
            ];
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create('pt_PT');
        for ($i = 2; $i <= 30; $i++) {
            $pedidos = $this->newFakePedido($faker, $i);
            DB::table('pedidos')->insert($pedidos);
            $id = DB::getPdo()->lastInsertId();
            $pedidosucs = $this->newFakePedidoUcs($faker, $id);
            DB::table('pedidosucs')->insert($pedidosucs);
            if($i%2 == 0){
                $pedidosucs = $this->newFakePedidoUcs($faker, $id);
                DB::table('pedidosucs')->insert($pedidosucs);
            }
        }
    }
}
