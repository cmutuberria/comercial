<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Usuario extends Model
{
    //cao_usuario
    protected $table = 'cao_usuario';
    protected $primaryKey = 'co_usuario';
    protected $keyType = false;
    public $timestamps = false;

    /**
     *Permisos del usuario
     */
    public function Permissaos()
    {
        return $this->hasMany('App\Model\Permissao', 'co_usuario', 'co_usuario');
    }

    /**Consultores */
    public function Consultores()
    {
        return  DB::select('select  u.* from cao_usuario u
           join permissao_sistema p on(u.co_usuario=p.co_usuario)
           where p.co_sistema=1 and p.in_ativo=`S` and p.co_tipo_usuario in (0,1,2)');
    }
    /**
     * Relatorio de cada usuario
     */
    public function relatorio($anno_desde, $mes_desde, $anno_hasta, $mes_hasta)
    {

        $respuesta = [];
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
        foreach ($fechas as $fecha) {
            $receita_liquida = $this->receita_liquida($fecha->fecha);
            $comissao = $this->comissao($fecha->fecha) ? $this->comissao($fecha->fecha)[0]->comissao : 0;
            $custo_fixo = $this->custo_fixo() ? $this->custo_fixo()[0]->custo_fixo : 0;
            if ($receita_liquida != 0) {
                array_push($respuesta, [
                    "anno" => $fecha->anno,
                    "mes" => $fecha->mes,
                    "receita_liquida" => $receita_liquida,
                    "comissao" => $comissao,
                    "custo_fixo" => $custo_fixo,
                    "Lucro" => round($receita_liquida - ($comissao + $custo_fixo), 2)
                ]);
            }
        }
        return $respuesta;
    }
    public function receita_liquida($fecha)
    {
        $receita_liquida = DB::Select('select  
            ROUND(sum(valor-(valor*total_imp_inc/100)),2) receita_liquida
            from cao_fatura f 
            join cao_os os on (f.co_os=os.co_os)
            where EXTRACT(YEAR_MONTH FROM f.data_emissao)= :fecha
            and os.co_usuario=:usuario
            group by EXTRACT(YEAR_MONTH FROM f.data_emissao)
            order by f.data_emissao', ["fecha" => $fecha, "usuario" => $this->co_usuario]);
        return $receita_liquida ? $receita_liquida[0]->receita_liquida : 0;
    }
    public function receita_liquida_date_interval($anno_desde, $mes_desde, $anno_hasta, $mes_hasta)
    {
        $receita_liquida = DB::Select('select  
            ROUND(sum(valor-(valor*total_imp_inc/100)),2) receita_liquida
            from cao_fatura f 
            join cao_os os on (f.co_os=os.co_os)            
            where EXTRACT(YEAR FROM f.data_emissao) BETWEEN :anno_desde and :anno_hasta
            and EXTRACT(MONTH FROM f.data_emissao) BETWEEN :mes_desde and :mes_hasta
            and os.co_usuario=:usuario', [
            "anno_desde" => $anno_desde, "mes_desde" => $mes_desde,
            "anno_hasta" => $anno_hasta, "mes_hasta" => $mes_hasta,
            "usuario" => $this->co_usuario
        ]);

        return $receita_liquida[0]->receita_liquida ? $receita_liquida[0]->receita_liquida : 0;
    }
    public function comissao($fecha)
    {
        return DB::Select('select	
                ROUND(sum((valor-(valor*total_imp_inc/100))*comissao_cn/100),2) comissao
                from cao_fatura f 
                join cao_os os on (f.co_os=os.co_os)
                where EXTRACT(YEAR_MONTH FROM f.data_emissao) = :fecha
                and os.co_usuario=:usuario
                group by EXTRACT(YEAR_MONTH FROM f.data_emissao)
                order by f.data_emissao', ["fecha" => $fecha, "usuario" => $this->co_usuario]);
    }
    public function custo_fixo()
    {
        return DB::Select(
            'select IFNULL(sa.brut_salario,0) custo_fixo from cao_salario sa
                where sa.co_usuario=:usuario',
            ["usuario" => $this->co_usuario]
        );
    }
}
