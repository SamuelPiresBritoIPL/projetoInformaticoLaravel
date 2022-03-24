<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UtilizadorSeeder extends Seeder
{
    private $numberOfUtilizadores = 100;  
    public static $used_emails = [];


    private static function stripAccents($stripAccents)
    {
        $from = 'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ';
        $to =   'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY';
        $keys = array();
        $values = array();
        preg_match_all('/./u', $from, $keys);
        preg_match_all('/./u', $to, $values);
        $mapping = array_combine($keys[0], $values[0]);
        return strtr($stripAccents, $mapping);
    }

    public static function randomName($faker, &$gender, &$fullname, &$email)
    {
        $gender = $faker->randomElement(['male', 'female']);
        $firstname = $faker->firstName($gender);
        $lastname = $faker->lastName();
        $secondname = $faker->numberBetween(1, 3) == 2 ? "" : " " . $faker->firstName($gender);
        $number_middlenames = $faker->numberBetween(1, 6);
        $number_middlenames = $number_middlenames == 1 ? 0 : ($number_middlenames >= 5 ? $number_middlenames - 3 : 1);
        $middlenames = "";
        for ($i = 0; $i < $number_middlenames; $i++) {
            $middlenames .= " " . $faker->lastName();
        }
        $fullname = $firstname . $secondname . $middlenames . " " . $lastname;
        $email = strtolower(UtilizadorSeeder::stripAccents($firstname) . "." . UtilizadorSeeder::stripAccents($lastname) . "@mail.pt");
        $i = 2;
        while (in_array($email, UtilizadorSeeder::$used_emails)) {
            $email = strtolower(UtilizadorSeeder::stripAccents($firstname) . "." . UtilizadorSeeder::stripAccents($lastname) . "." . $i . "@mail.pt");
            $i++;
        }
        UtilizadorSeeder::$used_emails[] = $email;
        $gender = $gender == 'male' ? 'M' : 'F';
    }

    private function newFakeUtilizador($faker)
    {
        $fullname = "";
        $email = "";
        $gender = "";
        
        UtilizadorSeeder::randomName($faker, $gender, $fullname, $email);

        $number = $faker->numberBetween(2150000, 2229999);
        $type = $faker->numberBetween(0,3);
        $courseId = $faker->numberBetween(1,5);

        return [
            'nome' => $fullname,
            'email' => $email,
            'numero' => $number,
            'tipo' => $type,
            'idCurso' => $courseId
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
        for ($i = 1; $i <= $this->numberOfUtilizadores; $i++) {
            $utilizador = $this->newFakeUtilizador($faker);
            DB::table('utilizador')->insert($utilizador);
        }
    }
}
