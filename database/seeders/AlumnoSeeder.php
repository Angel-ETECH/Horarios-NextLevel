<?php
// database/seeders/AlumnoSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Alumno;

class AlumnoSeeder extends Seeder
{
    public function run(): void
    {
        $alumnos = [
            ['codigo' => 'ALU-001', 'nombre' => 'Pedro', 'apellido_paterno' => 'González', 'apellido_materno' => 'Reyes', 'dni' => '11111111', 'email' => 'pedro.gonzalez@example.com', 'telefono' => '987111111', 'fecha_nacimiento' => '2015-05-15', 'direccion' => 'Av. Principal 123', 'genero' => 'masculino', 'grado_id' => 1, 'fecha_ingreso' => '2024-03-01', 'estado' => 'activo'],
            ['codigo' => 'ALU-002', 'nombre' => 'María', 'apellido_paterno' => 'Luna', 'apellido_materno' => 'Díaz', 'dni' => '22222222', 'email' => 'maria.luna@example.com', 'telefono' => '987222222', 'fecha_nacimiento' => '2016-08-22', 'direccion' => 'Calle Las Flores 456', 'genero' => 'femenino', 'grado_id' => 1, 'fecha_ingreso' => '2024-03-01', 'estado' => 'activo'],
            ['codigo' => 'ALU-003', 'nombre' => 'Carlos', 'apellido_paterno' => 'Mendoza', 'apellido_materno' => 'Silva', 'dni' => '33333333', 'email' => 'carlos.mendoza@example.com', 'telefono' => '987333333', 'fecha_nacimiento' => '2014-11-10', 'direccion' => 'Jr. Los Olivos 789', 'genero' => 'masculino', 'grado_id' => 2, 'fecha_ingreso' => '2024-03-01', 'estado' => 'activo'],
            ['codigo' => 'ALU-004', 'nombre' => 'Ana', 'apellido_paterno' => 'Quispe', 'apellido_materno' => 'Huamán', 'dni' => '44444444', 'email' => 'ana.quispe@example.com', 'telefono' => '987444444', 'fecha_nacimiento' => '2009-04-20', 'direccion' => 'Av. Los Pinos 321', 'genero' => 'femenino', 'grado_id' => 8, 'fecha_ingreso' => '2024-03-01', 'estado' => 'activo'],
            ['codigo' => 'ALU-005', 'nombre' => 'Javier', 'apellido_paterno' => 'Pormachi', 'apellido_materno' => 'Gomez', 'dni' => '55555555', 'email' => 'javier.pormachi@example.com', 'telefono' => '987555555', 'fecha_nacimiento' => '2010-07-15', 'direccion' => 'Calle Los Álamos 654', 'genero' => 'masculino', 'grado_id' => 9, 'fecha_ingreso' => '2024-03-01', 'estado' => 'activo'],
            ['codigo' => 'ALU-006', 'nombre' => 'Lucía', 'apellido_paterno' => 'Fernández', 'apellido_materno' => 'Ramos', 'dni' => '66666666', 'email' => 'lucia.fernandez@example.com', 'telefono' => '987666666', 'fecha_nacimiento' => '2017-03-10', 'direccion' => 'Av. Las Palmeras 987', 'genero' => 'femenino', 'grado_id' => 3, 'fecha_ingreso' => '2024-03-01', 'estado' => 'activo'],
            ['codigo' => 'ALU-007', 'nombre' => 'Diego', 'apellido_paterno' => 'Salazar', 'apellido_materno' => 'Torres', 'dni' => '77777777', 'email' => 'diego.salazar@example.com', 'telefono' => '987777777', 'fecha_nacimiento' => '2008-09-25', 'direccion' => 'Jr. Los Naranjos 147', 'genero' => 'masculino', 'grado_id' => 10, 'fecha_ingreso' => '2024-03-01', 'estado' => 'activo'],
            ['codigo' => 'ALU-008', 'nombre' => 'Camila', 'apellido_paterno' => 'Rojas', 'apellido_materno' => 'Vega', 'dni' => '88888888', 'email' => 'camila.rojas@example.com', 'telefono' => '987888888', 'fecha_nacimiento' => '2018-12-01', 'direccion' => 'Av. Los Cedros 258', 'genero' => 'femenino', 'grado_id' => 4, 'fecha_ingreso' => '2024-03-01', 'estado' => 'activo'],
            ['codigo' => 'ALU-009', 'nombre' => 'Andrés', 'apellido_paterno' => 'Navarro', 'apellido_materno' => 'Paredes', 'dni' => '99999999', 'email' => 'andres.navarro@example.com', 'telefono' => '987999999', 'fecha_nacimiento' => '2007-06-18', 'direccion' => 'Calle Los Robles 369', 'genero' => 'masculino', 'grado_id' => 12, 'fecha_ingreso' => '2024-03-01', 'estado' => 'activo'],
            ['codigo' => 'ALU-010', 'nombre' => 'Valentina', 'apellido_paterno' => 'Cruz', 'apellido_materno' => 'Mamani', 'dni' => '10101010', 'email' => 'valentina.cruz@example.com', 'telefono' => '987101010', 'fecha_nacimiento' => '2019-02-28', 'direccion' => 'Jr. Los Sauces 741', 'genero' => 'femenino', 'grado_id' => 5, 'fecha_ingreso' => '2024-03-01', 'estado' => 'activo'],
            ['codigo' => 'ALU-011', 'nombre' => 'Mateo', 'apellido_paterno' => 'Herrera', 'apellido_materno' => 'Paredes', 'dni' => '12121212', 'email' => 'mateo.herrera@example.com', 'telefono' => '987121212', 'fecha_nacimiento' => '2016-07-20', 'direccion' => 'Av. Los Ángeles 159', 'genero' => 'masculino', 'grado_id' => 6, 'fecha_ingreso' => '2024-03-01', 'estado' => 'activo'],
            ['codigo' => 'ALU-012', 'nombre' => 'Sofía', 'apellido_paterno' => 'Vega', 'apellido_materno' => 'Ramos', 'dni' => '13131313', 'email' => 'sofia.vega@example.com', 'telefono' => '987131313', 'fecha_nacimiento' => '2015-09-12', 'direccion' => 'Calle Los Girasoles 753', 'genero' => 'femenino', 'grado_id' => 7, 'fecha_ingreso' => '2024-03-01', 'estado' => 'activo'],
            ['codigo' => 'ALU-013', 'nombre' => 'Emilio', 'apellido_paterno' => 'Rojas', 'apellido_materno' => 'Flores', 'dni' => '14141414', 'email' => 'emilio.rojas@example.com', 'telefono' => '987141414', 'fecha_nacimiento' => '2009-11-05', 'direccion' => 'Jr. Los Tulipanes 852', 'genero' => 'masculino', 'grado_id' => 11, 'fecha_ingreso' => '2024-03-01', 'estado' => 'activo'],
            ['codigo' => 'ALU-014', 'nombre' => 'Valeria', 'apellido_paterno' => 'Mendoza', 'apellido_materno' => 'Silva', 'dni' => '15151515', 'email' => 'valeria.mendoza@example.com', 'telefono' => '987151515', 'fecha_nacimiento' => '2010-04-25', 'direccion' => 'Av. Los Rosales 963', 'genero' => 'femenino', 'grado_id' => 13, 'fecha_ingreso' => '2024-03-01', 'estado' => 'activo'],
            ['codigo' => 'ALU-015', 'nombre' => 'Nicolás', 'apellido_paterno' => 'Lozano', 'apellido_materno' => 'Torres', 'dni' => '16161616', 'email' => 'nicolas.lozano@example.com', 'telefono' => '987161616', 'fecha_nacimiento' => '2018-08-15', 'direccion' => 'Calle Los Claveles 147', 'genero' => 'masculino', 'grado_id' => 2, 'fecha_ingreso' => '2024-03-01', 'estado' => 'activo'],
        ];

        foreach ($alumnos as $alumno) {
            Alumno::create($alumno);
        }

        $this->command->info('✅ ' . count($alumnos) . ' alumnos creados');
    }
}
