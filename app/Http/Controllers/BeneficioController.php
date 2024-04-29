<?php

namespace App\Http\Controllers;

use App\Http\Resources\BeneficioCollection;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use OpenApi\Annotations as OA;

/**
* @OA\Info(
*      title="Endpoint para prueba Kuantaz", 
*      version="1.0",
* )
*/

class BeneficioController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/beneficios",
     *     summary="Obtener lista de beneficios",
     *     tags={"Beneficios"},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    function index() {
        $beneficios = Http::get('https://run.mocky.io/v3/399b4ce1-5f6e-4983-a9e8-e3fa39e1ea71'); // endpoint beneficios
        $beneficios = collect($beneficios['data']);

        $filtros = Http::get('https://run.mocky.io/v3/06b8dd68-7d6d-4857-85ff-b58e204acbf4');
        $filtros = collect($filtros['data']);

        $fichas = Http::get('https://run.mocky.io/v3/c7a4777f-e383-4122-8a89-70f29a6830c0');
        $fichas = collect($fichas['data']);

        $beneficios = $beneficios->map(
            function ($item, $key) use ($filtros, $fichas) {
                $item['anio'] = (string)(Carbon::parse($item['fecha']))->year; // se obtiene el año de la fecha y se agrega como elementa
                foreach ($filtros as $filtro) { // se relacionan el filtro con el beneficio
                    if ($filtro['id_programa'] === $item['id_programa']) {
                        //$item['filtro'] = $filtro;
                        foreach ($fichas as $ficha) { // se relaciona la ficha con el filtro
                            if ($ficha['id'] === $filtro['ficha_id']) {
                                $item['ficha'] = $ficha;
                            }
                        }
                    }
                }
                return $item;
            });
        $beneficios = $beneficios->reject(
            function ($item, $Key) use ($filtros) {
                foreach ($filtros as $filtro) {
                    return $item['monto'] > $filtro['max'] || $item['monto'] < $filtro['min'];
                }
            }
        );
        $beneficios = $beneficios->groupBy('anio'); // se agrupan los beneficios por año
        $beneficios = $beneficios->map( // suma monto total; número beneficios
            function ($item) {
                $item = collect($item);
                $item->prepend($item->count(),'beneficios');
                $item->prepend($item->sum('monto'),'monto_total');
                return $item;
            }
        );
        return new BeneficioCollection($beneficios);
    }
}
