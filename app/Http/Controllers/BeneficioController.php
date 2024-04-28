<?php

namespace App\Http\Controllers;

use App\Http\Resources\BeneficioCollection;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BeneficioController extends Controller
{
    function index() {
        $response = Http::get('https://run.mocky.io/v3/399b4ce1-5f6e-4983-a9e8-e3fa39e1ea71');
        $response = $response->collect();
        $response = collect($response['data']);
        $response = $response->map( // se obtiene año de la fecha
            function ($item, $key) {
                $item['anio'] = (string)(Carbon::parse($item['fecha']))->year;
                return $item;
            });
        $response = $response->groupBy('anio'); // se agrupan los beneficios por año
        
        $response = $response->map( // suma monto total
            function ($item) {
                $item['monto_total'] = $item->sum('monto');
                return $item;
            }
        );

        $response = $response->map(
            function ($item) {
                $item['beneficios'] = $item->count();
                return $item;
            }
        );
        return new BeneficioCollection($response);
        //dd($response);
    }
}
