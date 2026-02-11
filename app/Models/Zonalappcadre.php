<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Appcadre;
use Illuminate\Support\Facades\DB;

class Zonalappcadre extends Model
{
    protected $table = 'appcadres'; 

    use HasFactory;

    public function scopeLastSync($query)
    {
        return $query->select(DB::raw("sum(app_primary) AS app_primary,sum(app_english_pri) AS app_english_pri,sum(app_sinhala_pri) AS app_sinhala_pri,sum(app_science) AS app_science,sum(app_maths_sec) AS app_maths_sec,sum(app_tamil_Sec) AS app_tamil_Sec,sum(app_english_Sec) AS app_english_Sec,sum(app_history_Sec) AS app_history_Sec,sum(app_saivanery) AS app_saivanery,sum(app_rc) AS app_rc,sum(app_nrc) AS app_nrc,sum(app_islam) AS app_islam,sum(app_geography_sec) AS app_geography_sec,sum(app_civics) AS app_civics,sum(app_entrepren) AS app_entrepren,sum(app_busandacct) AS app_busandacct,sum(app_sinhala_sec) AS app_sinhala_sec,sum(app_wesmusic_Sec) AS app_wesmusic_Sec,sum(app_carnmusic_sec) AS app_carnmusic_sec,sum(app_art_sec) AS app_art_sec,sum(app_baratham_sec) AS app_baratham_sec,sum(app_drama_sec) AS app_drama_sec,sum(app_tamillit_sec) AS app_tamillit_sec,sum(app_englit_sec) AS app_englit_sec,sum(app_ict_sec) AS app_ict_sec,sum(app_argiculture_sec) AS app_argiculture_sec,sum(app_fisharies_sec) AS app_fisharies_sec,sum(app_artcraft) AS app_artcraft,sum(app_homeeco_sec) AS app_homeeco_sec,sum(app_commedia_sec) AS app_commedia_sec,sum(app_hpe) AS app_hpe,sum(app_des_mecha_sec) AS app_des_mecha_sec,sum(app_des_elec_sec) AS app_des_elec_sec,sum(app_des_con_sec) AS app_des_con_sec,sum(app_bi_maths) AS app_bi_maths,sum(app_bi_science) AS app_bi_science,sum(app_bi_geography) AS app_bi_geography,sum(app_bi_civics) AS app_bi_civics,sum(app_bi_entrepre) AS app_bi_entrepre,sum(app_bi_busandacct) AS app_bi_busandacct,sum(app_bi_ict) AS app_bi_ict,sum(app_bi_hpe) AS app_bi_hpe,sum(app_physics) AS app_physics,sum(app_chemistry) AS app_chemistry,sum(app_commaths) AS app_commaths,sum(app_biology) AS app_biology,sum(app_agriscience) AS app_agriscience,sum(app_engtech) AS app_engtech,sum(app_biotech) AS app_biotech,sum(app_scifortech) AS app_scifortech,sum(app_accounting) AS app_accounting,sum(app_economics) AS app_economics,sum(app_busstatics) AS app_busstatics,sum(app_bustudies) AS app_bustudies,sum(app_logic) AS app_logic,sum(app_politicsci) AS app_politicsci,sum(app_christianity) AS app_christianity,sum(app_tamil_al) AS app_tamil_al,sum(app_maths_al) AS app_maths_al,sum(app_civiltech) AS app_civiltech,sum(app_mechtech_al) AS app_mechtech_al,sum(app_electech_al) AS app_electech_al,sum(app_foodtech_al) AS app_foodtech_al,sum(app_agrotech) AS app_agrotech,sum(app_biorstech_al) AS app_biorstech_al,sum(app_hinducivil) AS app_hinducivil,sum(app_chriscivil) AS app_chriscivil,sum(app_commedia_al) AS app_commedia_al,sum(app_ict_al) AS app_ict_al,sum(app_history_al) AS app_history_al,sum(app_drama_al) AS app_drama_al,sum(app_geography_al) AS app_geography_al,sum(app_homeeco_al) AS app_homeeco_al,sum(app_dance_al) AS app_dance_al,sum(app_music_al) AS app_music_al,sum(app_art_al) AS app_art_al,sum(app_addieng) AS app_addieng,sum(app_addigit) AS app_addigit,sum(app_superv_sec) AS app_superv_sec,sum(app_superv_al) AS app_superv_al,sum(app_counceling) AS app_counceling,sum(app_ictcentre) AS app_ictcentre,sum(app_library) AS app_library,sum(app_specialedu) AS app_specialedu,sum(app_13yearsedu) AS app_13yearsedu,sum(app_astprincipal) AS app_astprincipal,sum(app_depprincipal) AS app_depprincipal,sum(app_actprincipal) AS app_actprincipal,sum(app_principal) AS app_principal,sum(totapp_1stbas) AS totapp_1stbas,sum(totapp_2ndbas) AS totapp_2ndbas,sum(totapp_3rdbas) AS totapp_3rdbas,sum(totapp_1stbas_bi) AS totapp_1stbas_bi,sum(totapp_2ndbas_bi) AS totapp_2ndbas_bi,sum(totapp_sci_al) AS totapp_sci_al,sum(totapp_tech_al) AS totapp_tech_al,sum(totapp_artcom_al) AS totapp_artcom_al,sum(totapp_slts) AS totapp_slts,sum(totapp_slps) AS totapp_slps"));
    
        // to use in blade: $zonalcadre = Zonalappcadre::LastSync()->get();
    
    }
}
