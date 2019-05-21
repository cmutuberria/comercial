<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use App\Model\Usuario;
use PhpParser\Node\Stmt\Label;

class ComercialController extends Controller
{
  public function ConsultoresList()
  {
    return DB::select('select  u.co_usuario, u.no_usuario from cao_usuario u
        join permissao_sistema p on(u.co_usuario=p.co_usuario)
        where p.co_sistema=1 and p.in_ativo="S" and p.co_tipo_usuario in (0,1,2)');
  }
  public function AnnosList()
  {
    return DB::select('select DISTINCT EXTRACT(YEAR FROM data_emissao) anno from cao_fatura f 
        join cao_os os on (f.co_os=os.co_os) order by anno desc');
  }
  /**
   * Store a new user.
   *
   * @param  Request  $request
   * @return Response
   */
  public function Relatorio(Request $request)
  {
    $mes_desde = $request->input('mes_desde');
    $anno_desde = $request->input('anno_desde');
    $mes_hasta = $request->input('mes_hasta');
    $anno_hasta = $request->input('anno_hasta');
    $consultores = $request->input('consultores');
    $usuarios = Usuario::find($consultores);
    $respuesta = [];

    foreach ($usuarios as $usuario) {
      array_push($respuesta, [
        "usuario" => $usuario->co_usuario,
        "nombre" => $usuario->no_usuario,
        "relatorio" => $usuario->Relatorio($anno_desde, $mes_desde, $anno_hasta, $mes_hasta)
      ]);
    }

    return response()->json($respuesta);
  }
  public function Grafico(Request $request)
  {
    $mes_desde = $request->input('mes_desde');
    $anno_desde = $request->input('anno_desde');
    $mes_hasta = $request->input('mes_hasta');
    $anno_hasta = $request->input('anno_hasta');
    $consultores = $request->input('consultores');
    $usuarios = Usuario::find($consultores);
    $fechas = DB::Select(
      'select distinct EXTRACT(YEAR_MONTH FROM f.data_emissao) fecha, 
        EXTRACT(YEAR FROM f.data_emissao) anno, 
        EXTRACT(MONTH FROM f.data_emissao) mes from cao_fatura f 
            where EXTRACT(YEAR FROM f.data_emissao) BETWEEN :anno_desde and :anno_hasta
            and EXTRACT(MONTH FROM f.data_emissao) BETWEEN :mes_desde and :mes_hasta
            order by EXTRACT(YEAR_MONTH FROM f.data_emissao)',
      [
        "anno_desde" => $anno_desde, "mes_desde" => $mes_desde,
        "anno_hasta" => $anno_hasta, "mes_hasta" => $mes_hasta
      ]
    );

    $custo_fixo =  round(
      DB::select('SELECT avg(IFNULL(sa.brut_salario,0)) custo_fixo  from cao_salario sa')[0]->custo_fixo,
      2
    );

    $meses = [
      "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio",
      "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
    ];
    $labels = []; //meses
    $bar = [];
    $first_iteration = true;
    $max_value = $custo_fixo;
    foreach ($usuarios as $usuario) {
      $dataBar = []; //data para cada usuario      
      $dataLine = [];
      foreach ($fechas as $fecha) {
        if ($first_iteration) {
          //array de labels 
          //array_push($labels, $fecha->anno + " " + DateTime::createFromFormat('!m', $fecha->mes)->format('F'));          
          array_push($labels, $fecha->anno . " " . $meses[$fecha->mes - 1]);
          // la data del line
          array_push($dataLine, $custo_fixo);
        }
        //llenando el array de data del usuario
        $receita_liquida = $usuario->receita_liquida($fecha->fecha);
        $max_value = $max_value < $receita_liquida ? $receita_liquida : $max_value;
        array_push($dataBar, $receita_liquida);
      }
      if ($first_iteration) {
        //incluir todo lo del grafico line en array bar        
        array_push($bar, [
          "data" => $dataLine, "label" => 'Custo_fixo', "type" => 'line',
          "backgroundColor" => "transparent", "steppedLine" => true
        ]);
        $first_iteration = false;
      }
      //data y label de cada usuario
      array_push($bar, ["data" => $dataBar, "label" => $usuario->no_usuario]);
    }

    return response()->json(["labels" => $labels, "bar" => $bar, "max_value" => round($max_value + $max_value / 10, 0)]);
  }


  public function Pizza(Request $request)
  {
    $mes_desde = $request->input('mes_desde');
    $anno_desde = $request->input('anno_desde');
    $mes_hasta = $request->input('mes_hasta');
    $anno_hasta = $request->input('anno_hasta');
    $consultores = $request->input('consultores');
    $usuarios = Usuario::find($consultores);

    $labels = []; //meses
    $data = [];    
    foreach ($usuarios as $usuario) {
      array_push($labels, $usuario->no_usuario);
      array_push($data,
        $usuario->receita_liquida_date_interval($anno_desde , $mes_desde, $anno_hasta , $mes_hasta));
    }
    return response()->json(["labels" => $labels, "data" => $data]);
  }
}
