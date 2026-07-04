<?php

namespace Database\Seeders;

use App\Models\Catalogo;
use Illuminate\Database\Seeder;

/**
 * Entidades federativas (clave CURP/RENAPO) y municipios de Michoacán.
 *
 * Catálogo dependiente (CAT-03): entidad → municipio.
 * Municipios de otras entidades y localidades se administran después
 * desde el panel o por importación CSV.
 *
 * NOTA: verificar la lista de municipios contra el catálogo INEGI vigente
 * antes del arranque del ciclo.
 */
class EntidadesMunicipiosSeeder extends Seeder
{
    public function run(): void
    {
        $entidades = [
            'AS' => 'Aguascalientes', 'BC' => 'Baja California', 'BS' => 'Baja California Sur',
            'CC' => 'Campeche', 'CL' => 'Coahuila', 'CM' => 'Colima', 'CS' => 'Chiapas',
            'CH' => 'Chihuahua', 'DF' => 'Ciudad de México', 'DG' => 'Durango',
            'GT' => 'Guanajuato', 'GR' => 'Guerrero', 'HG' => 'Hidalgo', 'JC' => 'Jalisco',
            'MC' => 'Estado de México', 'MN' => 'Michoacán', 'MS' => 'Morelos', 'NT' => 'Nayarit',
            'NL' => 'Nuevo León', 'OC' => 'Oaxaca', 'PL' => 'Puebla', 'QT' => 'Querétaro',
            'QR' => 'Quintana Roo', 'SP' => 'San Luis Potosí', 'SL' => 'Sinaloa', 'SR' => 'Sonora',
            'TC' => 'Tabasco', 'TS' => 'Tamaulipas', 'TL' => 'Tlaxcala', 'VZ' => 'Veracruz',
            'YN' => 'Yucatán', 'ZS' => 'Zacatecas', 'NE' => 'Nacido en el extranjero',
        ];

        $orden = 0;
        foreach ($entidades as $clave => $nombre) {
            Catalogo::updateOrCreate(
                ['tipo' => 'entidad', 'clave' => $clave],
                ['nombre' => $nombre, 'orden' => $orden++],
            );
        }

        $michoacan = Catalogo::where('tipo', 'entidad')->where('clave', 'MN')->first();

        $municipios = [
            'Acuitzio', 'Aguililla', 'Álvaro Obregón', 'Angamacutiro', 'Angangueo',
            'Apatzingán', 'Aporo', 'Aquila', 'Ario', 'Arteaga', 'Briseñas', 'Buenavista',
            'Carácuaro', 'Charapan', 'Charo', 'Chavinda', 'Cherán', 'Chilchota',
            'Chinicuila', 'Chucándiro', 'Churintzio', 'Churumuco', 'Coahuayana',
            'Coalcomán de Vázquez Pallares', 'Coeneo', 'Cojumatlán de Régules', 'Contepec',
            'Copándaro', 'Cotija', 'Cuitzeo', 'Ecuandureo', 'Epitacio Huerta',
            'Erongarícuaro', 'Gabriel Zamora', 'Hidalgo', 'Huandacareo', 'Huaniqueo',
            'Huetamo', 'Huiramba', 'Indaparapeo', 'Irimbo', 'Ixtlán', 'Jacona', 'Jiménez',
            'Jiquilpan', 'José Sixto Verduzco', 'Juárez', 'Jungapeo', 'La Huacana',
            'La Piedad', 'Lagunillas', 'Lázaro Cárdenas', 'Los Reyes', 'Madero',
            'Maravatío', 'Marcos Castellanos', 'Morelia', 'Morelos', 'Múgica', 'Nahuatzen',
            'Nocupétaro', 'Nuevo Parangaricutiro', 'Nuevo Urecho', 'Numarán', 'Ocampo',
            'Pajacuarán', 'Panindícuaro', 'Paracho', 'Parácuaro', 'Pátzcuaro', 'Penjamillo',
            'Peribán', 'Purépero', 'Puruándiro', 'Queréndaro', 'Quiroga', 'Sahuayo',
            'Salvador Escalante', 'San Lucas', 'Santa Ana Maya', 'Senguio', 'Susupuato',
            'Tacámbaro', 'Tancítaro', 'Tangamandapio', 'Tangancícuaro', 'Tanhuato',
            'Taretan', 'Tarímbaro', 'Tepalcatepec', 'Tingambato', 'Tingüindín',
            'Tiquicheo de Nicolás Romero', 'Tlalpujahua', 'Tlazazalca', 'Tocumbo',
            'Tumbiscatío', 'Turicato', 'Tuxpan', 'Tuzantla', 'Tzintzuntzan', 'Tzitzio',
            'Uruapan', 'Venustiano Carranza', 'Villamar', 'Vista Hermosa', 'Yurécuaro',
            'Zacapu', 'Zamora', 'Zináparo', 'Zinapécuaro', 'Ziracuaretiro', 'Zitácuaro',
        ];

        foreach ($municipios as $i => $nombre) {
            $municipio = Catalogo::updateOrCreate(
                ['tipo' => 'municipio', 'clave' => 'MN-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT)],
                ['nombre' => $nombre, 'parent_id' => $michoacan->id, 'orden' => $i],
            );

            if ($nombre === 'Ario') {
                Catalogo::updateOrCreate(
                    ['tipo' => 'localidad', 'clave' => 'ARIO-CAB'],
                    ['nombre' => 'Ario de Rosales', 'parent_id' => $municipio->id, 'orden' => 1],
                );
            }
        }
    }
}
