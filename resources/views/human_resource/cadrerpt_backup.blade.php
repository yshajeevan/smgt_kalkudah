@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">Cadre Summary</h6>
    </div>
    <div class="card-body">
	<button type="button" id="print" class="commonButton">
		<i class="fas fa-save"></i>&nbsp;Print
	</button>
	  <div class="table-responsive">
	  	<table width='100%' border='1' id='cadretbl' style='border-collapse: collapse;' class="table-sm table-bordered">
			@foreach($cadres as $key=>$value)
			<thead>
			<tr>  
			<th>Catg</th><th>CDR</th><th>Subject</th><th>CR</th><th>AV</th><th>D/E</th><th>Catg</th><th>CDR</th><th>Subject</th><th>CR</th><th>AV</th><th>D/E</th>
			</tr>
			</thead>
			<tr>
			<td width="5%" rowspan="3">Pri</td>
			<td width="5%">&nbsp;</td>
			<td width="25%">Primary (General)</td>
			<td width="5%">{{ $value['app_primary_gen'] }}</td>
			<td width="5%">{{ $value['primary_gen'] }}</td>
			<td width="5%">{{ $value['primary_gen']-$value['app_primary_gen'] }}</td>
			<td width="5%" rowspan="3">Tech</td>
			<td width="5%" rowspan="3">{{ $value['totapp_tech_al'] }}</td>
			<td width="25%">Engineering Technology</td>
			<td width="5%">{{ $value['app_engtech'] }}</td>
			<td width="5%">{{ $value['engtech'] }}</td>
			<td width="5%">{{ $value['engtech'] }}</td>
			</tr>
			<tr>
			<td>&nbsp;</td>
			<td>English (Primary)</td>
			<td>{{ $value['app_english_pri'] }}</td>
			<td>{{ $value['english_pri'] }}</td>
			<td>{{ $value['english_pri']-$value['app_english_pri'] }}</td>
			<td>Bio System Technology</td>
			<td>{{ $value['app_biotech'] }}</td>
			<td>{{ $value['biotech'] }}</td>
			<td>{{ $value['biotech']-$value['app_biotech'] }}</td>
			</tr>
			<tr>
			<td>&nbsp;</td>
			<td>Sinhala (Primary)</td>
			<td>{{ $value['app_sinhala_pri'] }}</td>
			<td>{{ $value['sinhala_pri'] }}</td>
			<td>{{ $value['sinhala_pri']-$value['app_sinhala_pri'] }}</td>
			<td>Science for Technology</td>
			<td>{{ $value['app_scifortech'] }}</td>
			<td>{{ $value['scifortech'] }}</td>
			<td>{{ $value['scifortech']-$value['app_scifortech'] }}</td>
			</tr>
			<tr>
			<td rowspan="9">Sec</td>
			<td>&nbsp;</td>
			<td>Science</td>
			<td>{{ $value['app_science'] }}</td>
			<td>{{ $value['science'] }}</td>
			<td>{{ $value['science']-$value['app_science'] }}</td>
			<td rowspan="29">Arts & Com</td>
			<td rowspan="29">{{ $value['totapp_artcom_al'] }}</td>
			<td>Accounting</td>
			<td>{{ $value['app_accounting'] }}</td>
			<td>{{ $value['accounting'] }}</td>
			<td>{{ $value['accounting']-$value['app_accounting'] }}</td>
			</tr>
			<tr>
			<td>&nbsp;</td>
			<td>Mathematics</td>
			<td>{{ $value['app_maths_sec'] }}</td>
			<td>{{ $value['maths_sec'] }}</td>
			<td>{{ $value['maths_sec']-$value['app_maths_sec'] }}</td>
			<td>Economics</td>
			<td>{{ $value['app_economics'] }}</td>
			<td>{{ $value['economics'] }}</td>
			<td>{{ $value['economics']-$value['app_economics'] }}</td>
			</tr>
			<tr>
			<td>&nbsp;</td>
			<td>Tamil</td>
			<td>{{ $value['app_tamil_Sec'] }}</td>
			<td>{{ $value['tamil_Sec'] }}</td>
			<td>{{ $value['tamil_Sec']-$value['app_tamil_Sec'] }}</td>
			<td>Business Statistics</td>
			<td>{{ $value['app_busstatics'] }}</td>
			<td>{{ $value['busstatics'] }}</td>
			<td>{{ $value['busstatics']-$value['app_busstatics'] }}</td>
			</tr>
			<tr>
			<td>&nbsp;</td>
			<td>English</td>
			<td>{{ $value['app_english_Sec'] }}</td>
			<td>{{ $value['english_Sec'] }}</td>
			<td>{{ $value['english_Sec']-$value['app_english_Sec'] }}</td>
			<td>Business Studies</td>
			<td>{{ $value['app_bustudies'] }}</td>
			<td>{{ $value['bustudies'] }}</td>
			<td>{{ $value['bustudies']-$value['app_bustudies'] }}</td>
			</tr>
			<tr>
			<td>&nbsp;</td>
			<td>History</td>
			<td>{{ $value['app_history_Sec'] }}</td>
			<td>{{ $value['history_Sec'] }}</td>
			<td>{{ $value['history_Sec']-$value['app_history_Sec'] }}</td>
			<td>Logic & Scientific Method</td>
			<td>{{ $value['app_logic'] }}</td>
			<td>{{ $value['logic'] }}</td>
			<td>{{ $value['logic']-$value['app_logic'] }}</td>
			</tr>
			<tr>
			<td>&nbsp;</td>
			<td>Saivanery</td>
			<td>{{ $value['app_saivanery'] }}</td>
			<td>{{ $value['saivanery'] }}</td>
			<td>{{ $value['saivanery']-$value['app_saivanery'] }}</td>
			<td>Political Science</td>
			<td>{{ $value['app_politicsci'] }}</td>
			<td>{{ $value['politicsci'] }}</td>
			<td>{{ $value['politicsci']-$value['app_politicsci'] }}</td>
			</tr>
			<tr>
			<td>&nbsp;</td>
			<td>Roman Catholic</td>
			<td>{{ $value['app_rc'] }}</td>
			<td>{{ $value['rc'] }}</td>
			<td>{{ $value['rc']-$value['app_rc'] }}</td>
			<td>Hindusim</td>
			<td>0</td>
			<td>0</td>
			<td>0</td>
			</tr>
			<tr>
			<td>&nbsp;</td>
			<td>Non-Roman Catholic</td>
			<td>{{ $value['app_nrc'] }}</td>
			<td>{{ $value['nrc'] }}</td>
			<td>{{ $value['nrc']-$value['app_nrc'] }}</td>
			<td>Christianity</td>
			<td>{{ $value['app_christianity'] }}</td>
			<td>{{ $value['christianity'] }}</td>
			<td>{{ $value['christianity']-$value['app_christianity'] }}</td>
			</tr>
			<tr>
			<td>&nbsp;</td>
			<td>Islam</td>
			<td>{{ $value['app_islam'] }}</td>
			<td>{{ $value['islam'] }}</td>
			<td>{{ $value['islam']-$value['app_islam'] }}</td>
			<td>Tamil</td>
			<td>{{ $value['app_tamil_al'] }}</td>
			<td>{{ $value['tamil_al'] }}</td>
			<td>{{ $value['tamil_al']-$value['app_tamil_al'] }}</td>
			</tr>
			<td rowspan="5">B1</td>
			<td rowspan="5">{{ $value['totapp_1stbas'] }}</td>
			<td>Geography</td>
			<td>{{ $value['app_geography_sec'] }}</td>
			<td>{{ $value['geography_sec'] }}</td>
			<td>{{ $value['geography_sec']-$value['app_geography_sec'] }}</td>
			<td>English</td>
			<td>0</td>
			<td>0</td>
			<td>0</td>
			</tr>
			<tr>
			<td>Civics</td>
			<td>{{ $value['app_civics'] }}</td>
			<td>{{ $value['civics'] }}</td>
			<td>{{ $value['civics']-$value['app_civics'] }}</td>
			<td>Agriculture Science</td>
			<td>0</td>
			<td>0</td>
			<td>0</td>
			</tr>
			<tr>
			<td>Entreperneurship Edu.</td>
			<td>{{ $value['app_entrepren'] }}</td>
			<td>{{ $value['entrepren'] }}</td>
			<td>{{ $value['entrepren']-$value['app_entrepren'] }}</td>
			<td>Mathematics</td>
			<td>{{ $value['app_maths_al'] }}</td>
			<td>{{ $value['maths_al'] }}</td>
			<td>{{ $value['maths_al']-$value['app_maths_al'] }}</td>
			</tr>
			<tr>
			<td>Business & Accounting</td>
			<td>{{ $value['app_busandacct'] }}</td>
			<td>{{ $value['busandacct'] }}</td>
			<td>{{ $value['busandacct']-$value['app_busandacct'] }}</td>
			<td>Civil Technology</td>
			<td>{{ $value['app_civiltech'] }}</td>
			<td>{{ $value['civiltech'] }}</td>
			<td>{{ $value['civiltech']-$value['app_civiltech'] }}</td>
			</tr>
			<tr>
			<td>Second Language-Sinhala</td>
			<td>{{ $value['app_sinhala_sec'] }}</td>
			<td>{{ $value['sinhala_sec'] }}</td>
			<td>{{ $value['sinhala_sec']-$value['app_sinhala_sec'] }}</td>
			<td>Mechanical Tech</td>
			<td>{{ $value['app_mechtech_al'] }}</td>
			<td>{{ $value['mechtech_al'] }}</td>
			<td>{{ $value['mechtech_al']-$value['app_mechtech_al'] }}</td>
			</tr>
			<td rowspan="7">B2</td>
			<td rowspan="7">{{ $value['totapp_2ndbas'] }}</td>
			<td>Music-Western</td>
			<td>{{ $value['app_wesmusic_Sec'] }}</td>
			<td>{{ $value['wesmusic_Sec'] }}</td>
			<td>{{ $value['wesmusic_Sec']-$value['app_wesmusic_Sec'] }}</td>
			<td>Electronic & Electric Tech</td>
			<td>{{ $value['app_electech_al'] }}</td>
			<td>{{ $value['electech_al'] }}</td>
			<td>{{ $value['electech_al']-$value['app_electech_al'] }}</td>
			</tr>
			<tr>
			<td>Music-Carnatic</td>
			<td>{{ $value['app_carnmusic_sec'] }}</td>
			<td>{{ $value['carnmusic_sec'] }}</td>
			<td>{{ $value['carnmusic_sec']-$value['app_carnmusic_sec'] }}</td>
			<td>Food Technology</td>
			<td>{{ $value['app_foodtech_al'] }}</td>
			<td>{{ $value['foodtech_al'] }}</td>
			<td>{{ $value['foodtech_al']-$value['app_foodtech_al'] }}</td>
			</tr>
			<tr>
			<td>Art</td>
			<td>{{ $value['app_art_sec'] }}</td>
			<td>{{ $value['art_sec'] }}</td>
			<td>{{ $value['art_sec']-$value['app_art_sec'] }}</td>
			<td>Agro Technology</td>
			<td>{{ $value['app_agrotech'] }}</td>
			<td>{{ $value['agrotech'] }}</td>
			<td>{{ $value['agrotech']-$value['app_agrotech'] }}</td>
			</tr>
			<tr>
			<td>Dance-Baratham</td>
			<td>{{ $value['app_baratham_sec'] }}</td>
			<td>{{ $value['baratham_sec'] }}</td>
			<td>{{ $value['baratham_sec']-$value['app_baratham_sec'] }}</td>
			<td>Bio-Resource Tech</td>
			<td>{{ $value['app_biorstech_al'] }}</td>
			<td>{{ $value['biorstech_al'] }}</td>
			<td>{{ $value['biorstech_al']-$value['app_biorstech_al'] }}</td>
			</tr>
			<tr>
			<td>Drama & Theatre</td>
			<td>{{ $value['app_drama_sec'] }}</td>
			<td>{{ $value['drama_sec'] }}</td>
			<td>{{ $value['drama_sec']-$value['app_drama_sec'] }}</td>
			<td>Hindu Civilization</td>
			<td>{{ $value['app_hinducivil'] }}</td>
			<td>{{ $value['hinducivil'] }}</td>
			<td>{{ $value['hinducivil']-$value['app_hinducivil'] }}</td>
			</tr>
			<tr>
			<td>Tamil Literature</td>
			<td>{{ $value['app_tamillit_sec'] }}</td>
			<td>{{ $value['tamillit_sec'] }}</td>
			<td>{{ $value['tamillit_sec']-$value['app_tamillit_sec'] }}</td>
			<td>Christian Civilization</td>
			<td>{{ $value['app_chriscivil'] }}</td>
			<td>{{ $value['chriscivil'] }}</td>
			<td>{{ $value['chriscivil']-$value['app_chriscivil'] }}</td>
			</tr>
			<tr>
			<td>English Literature</td>
			<td>{{ $value['app_englit_sec'] }}</td>
			<td>{{ $value['englit_sec'] }}</td>
			<td>{{ $value['englit_sec']-$value['app_englit_sec'] }}</td>
			<td>Communication & Media</td>
			<td>{{ $value['app_commedia_al'] }}</td>
			<td>{{ $value['commedia_al'] }}</td>
			<td>{{ $value['commedia_al']-$value['app_commedia_al'] }}</td>
			</tr>
			<td rowspan="10">B3</td>
			<td rowspan="10">{{ $value['totapp_3rdbas'] }}</td>
			<td>ICT</td>
			<td>{{ $value['app_ict_sec'] }}</td>
			<td>{{ $value['ict_sec'] }}</td>
			<td>{{ $value['ict_sec']-$value['app_ict_sec'] }}</td>
			<td>ICT</td>
			<td>{{ $value['app_ict_al'] }}</td>
			<td>{{ $value['ict_al'] }}</td>
			<td>{{ $value['ict_al']-$value['app_ict_al'] }}</td>
			</tr>
			<tr>
			<td>Agriculture & Food Tech</td>
			<td>{{ $value['app_argiculture_sec'] }}</td>
			<td>{{ $value['argiculture_sec'] }}</td>
			<td>{{ $value['argiculture_sec']-$value['app_argiculture_sec'] }}</td>
			<td>History</td>
			<td>{{ $value['app_history_al'] }}</td>
			<td>{{ $value['history_al'] }}</td>
			<td>{{ $value['history_al']-$value['app_history_al'] }}</td>
			</tr>
			<tr>
			<td>Fisharies & Food Tech</td>
			<td>{{ $value['app_fisharies_sec'] }}</td>
			<td>{{ $value['fisharies_sec'] }}</td>
			<td>{{ $value['fisharies_sec']-$value['app_fisharies_sec'] }}</td>
			<td>Drama & Theatre</td>
			<td>{{ $value['app_drama_al'] }}</td>
			<td>{{ $value['drama_al'] }}</td>
			<td>{{ $value['drama_al']-$value['app_drama_al'] }}</td>
			</tr>
			<tr>
			<td>Art & Craft</td>
			<td>{{ $value['app_artcraft'] }}</td>
			<td>{{ $value['artcraft'] }}</td>
			<td>{{ $value['artcraft']-$value['app_artcraft'] }}</td>
			<td>Geography</td>
			<td>{{ $value['app_geography_al'] }}</td>
			<td>{{ $value['geography_al'] }}</td>
			<td>{{ $value['geography_al']-$value['app_geography_al'] }}</td>
			</tr>
			<tr>
			<td>Home Economics</td>
			<td>{{ $value['app_homeeco_sec'] }}</td>
			<td>{{ $value['homeeco_sec'] }}</td>
			<td>{{ $value['homeeco_sec']-$value['app_homeeco_sec'] }}</td>
			<td>Home Economics</td>
			<td>{{ $value['app_homeeco_al'] }}</td>
			<td>{{ $value['homeeco_al'] }}</td>
			<td>{{ $value['homeeco_al']-$value['app_homeeco_al'] }}</td>
			</tr>
			<tr>
			<td>Communication & Media</td>
			<td>{{ $value['app_commedia_sec'] }}</td>
			<td>{{ $value['commedia_sec'] }}</td>
			<td>{{ $value['commedia_sec']-$value['app_commedia_sec'] }}</td>
			<td>Dance</td>
			<td>{{ $value['app_dance_al'] }}</td>
			<td>{{ $value['dance_al'] }}</td>
			<td>{{ $value['dance_al']-$value['app_dance_al'] }}</td>
			</tr>
			<tr>
			<td>Health & Physical Edu</td>
			<td>{{ $value['app_hpe'] }}</td>
			<td>{{ $value['hpe'] }}</td>
			<td>{{ $value['hpe']-$value['app_hpe'] }}</td>
			<td>Music</td>
			<td>{{ $value['app_music_al'] }}</td>
			<td>{{ $value['music_al'] }}</td>
			<td>{{ $value['music_al']-$value['app_music_al'] }}</td>
			</tr>
			<tr>
			<td>Design & Machanical Tech</td>
			<td>{{ $value['app_des_mecha_sec'] }}</td>
			<td>{{ $value['des_mecha_sec'] }}</td>
			<td>{{ $value['des_mecha_sec']-$value['app_des_mecha_sec'] }}</td>
			<td>Art</td>
			<td>{{ $value['app_art_al'] }}</td>
			<td>{{ $value['art_al'] }}</td>
			<td>{{ $value['art_al']-$value['app_art_al'] }}</td>
			</tr>
			<tr>
			<td>Design & Electrical Tech</td>
			<td>{{ $value['app_des_elec_sec'] }}</td>
			<td>{{ $value['des_elec_sec'] }}</td>
			<td>{{ $value['des_elec_sec']-$value['app_des_elec_sec'] }}</td>
			<td rowspan="2">Addi</td>
			<td>&nbsp;</td>
			<td>Engligh</td>
			<td>{{ $value['app_addieng'] }}</td>
			<td>{{ $value['addieng'] }}</td>
			<td>{{ $value['addieng']-$value['app_addieng'] }}</td>
			</tr>
			<tr>
			<td>Design & Contruction Tech</td>
			<td>{{ $value['app_des_con_sec'] }}</td>
			<td>{{ $value['des_con_sec'] }}</td>
			<td>{{ $value['des_con_sec']-$value['app_des_con_sec'] }}</td>
			<td>&nbsp;</td>
			<td>GIT</td>
			<td>{{ $value['app_addigit'] }}</td>
			<td>{{ $value['addigit'] }}</td>
			<td>{{ $value['addigit']-$value['app_addigit'] }}</td>
			</tr>
			<tr>
			<td rowspan="2">Bi_Main</td>
			<td>&nbsp;</td>
			<td>Mathematics_Bi</td>
			<td>{{ $value['app_bi_maths'] }}</td>
			<td>{{ $value['bi_maths'] }}</td>
			<td>{{ $value['bi_maths']-$value['app_bi_maths'] }}</td>
			<td rowspan="2">Sup</td>
			<td>&nbsp;</td>
			<td>Supervision-Secondary</td>
			<td>{{ $value['app_superv_sec'] }}</td>
			<td>{{ $value['superv_sec'] }}</td>
			<td>{{ $value['superv_sec']-$value['app_superv_sec'] }}</td>
			</tr>
			<tr>
			<td>&nbsp;</td>
			<td>Science_Bi</td>
			<td>{{ $value['app_bi_science'] }}</td>
			<td>{{ $value['bi_science'] }}</td>
			<td>{{ $value['bi_science']-$value['app_bi_science'] }}</td>
			<td>&nbsp;</td>
			<td>Supervision-A/L</td>
			<td>{{ $value['app_superv_al'] }}</td>
			<td>{{ $value['superv_al'] }}</td>
			<td>{{ $value['superv_al']-$value['app_superv_al'] }}</td>
			</tr>
			<tr>
			<td rowspan="4">Bi Bas1</td>
			<td rowspan="4">{{ $value['totapp_1stbas_bi'] }}</td>
			<td>Geography_Bi</td>
			<td>{{ $value['app_bi_geography'] }}</td>
			<td>{{ $value['bi_geography'] }}</td>
			<td>{{ $value['bi_geography']-$value['app_bi_geography'] }}</td>
			<td rowspan="5">Gen</td>
			<td>&nbsp;</td>
			<td>Guidance & Counceling</td>
			<td>{{ $value['app_counceling'] }}</td>
			<td>{{ $value['counceling'] }}</td>
			<td>{{ $value['counceling']-$value['app_counceling'] }}</td>
			</tr>
			<tr>
			<td>Civics_Bi</td>
			<td>{{ $value['app_bi_civics'] }}</td>
			<td>{{ $value['bi_civics'] }}</td>
			<td>{{ $value['bi_civics']-$value['app_bi_civics'] }}</td>
			<td>&nbsp;</td>
			<td>Library Incharge</td>
			<td>{{ $value['app_library'] }}</td>
			<td>{{ $value['library'] }}</td>
			<td>{{ $value['library']-$value['app_library'] }}</td>
			</tr>
			<tr>
			<td>Entrepreneurship_Bi</td>
			<td>{{ $value['app_bi_entrepre'] }}</td>
			<td>{{ $value['bi_entrepre'] }}</td>
			<td>{{ $value['bi_entrepre']-$value['app_bi_entrepre'] }}</td>
			<td>&nbsp;</td>
			<td>Special Education</td>
			<td>{{ $value['app_specialedu'] }}</td>
			<td>{{ $value['specialedu'] }}</td>
			<td>{{ $value['specialedu']-$value['app_specialedu'] }}</td>
			</tr>
			<tr>
			<td>Business & Accounting_Bi</td>
			<td>{{ $value['app_bi_busandacct'] }}</td>
			<td>{{ $value['bi_busandacct'] }}</td>
			<td>{{ $value['bi_busandacct']-$value['app_bi_busandacct'] }}</td>
			<td>&nbsp;</td>
			<td>Teacher-ICT Centre</td>
			<td>{{ $value['app_ictcentre'] }}</td>
			<td>{{ $value['ictcentre'] }}</td>
			<td>{{ $value['ictcentre']-$value['app_ictcentre'] }}</td>
			</tr>
			<tr>
			<td rowspan="2">Bi Bas2</td>
			<td rowspan="2">{{ $value['totapp_2ndbas_bi'] }}</td>
			<td>ICT_Bi</td>
			<td>{{ $value['app_bi_ict'] }}</td>
			<td>{{ $value['bi_ict'] }}</td>
			<td>{{ $value['bi_ict']-$value['app_bi_ict'] }}</td>
			<td>&nbsp;</td>
			<td>13 Years Education</td>
			<td>{{ $value['app_13yearsedu'] }}</td>
			<td>{{ $value['thrtyearsedu'] }}</td>
			<td>{{ $value['thrtyearsedu']-$value['app_13yearsedu'] }}</td>
			</tr>
			<tr>
			<td>Health & Physical Edu_Bi</td>
			<td>{{ $value['app_bi_hpe'] }}</td>
			<td>{{ $value['bi_hpe'] }}</td>
			<td>{{ $value['bi_hpe']-$value['app_bi_hpe'] }}</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td style="font-weight:bold">Total of SLTS</td>
			<td style="font-weight:bold">{{ $value['totapp_slts'] }}</td>
			<td style="font-weight:bold">{{ $value['totavi_slts'] }}</td>
			<td style="font-weight:bold">{{ $value['totavi_slts']-$value['totapp_slts'] }}</td>
			</tr>
			<td rowspan="5">A/L Sci</td>
			<td rowspan="5">{{ $value['totapp_sci_al'] }}</td>
			<td>Physics</td>
			<td>{{ $value['app_physics'] }}</td>
			<td>{{ $value['physics'] }}</td>
			<td>{{ $value['physics']-$value['app_physics'] }}</td>
			<td rowspan="5">Admin</td>
			<td rowspan="5">{{ $value['totapp_slps'] }}</td>
			<td>Assistant Principals</td>
			<td>{{ $value['app_astprincipal'] }}</td>
			<td>{{ $value['astprincipal'] }}</td>
			<td>{{ $value['astprincipal']-$value['app_astprincipal'] }}</td>
			</tr>
			<tr>
			<td>Chemistry</td>
			<td>{{ $value['app_chemistry'] }}</td>
			<td>{{ $value['chemistry'] }}</td>
			<td>{{ $value['chemistry']-$value['app_chemistry'] }}</td>
			<td>Deputy Principals</td>
			<td>{{ $value['app_depprincipal'] }}</td>
			<td>{{ $value['depprincipal'] }}</td>
			<td>{{ $value['depprincipal']-$value['app_depprincipal'] }}</td>
			</tr>
			<tr>
			<td>Combined Maths</td>
			<td>{{ $value['app_commaths'] }}</td>
			<td>{{ $value['commaths'] }}</td>
			<td>{{ $value['commaths']-$value['app_commaths'] }}</td>
			<td>Acting Principals</td>
			<td>{{ $value['app_actprincipal'] }}</td>
			<td>{{ $value['actprincipal'] }}</td>
			<td>{{ $value['actprincipal']-$value['app_actprincipal'] }}</td>
			</tr>
			<tr>
			<td>Biology</td>
			<td>{{ $value['app_biology'] }}</td>
			<td>{{ $value['biology'] }}</td>
			<td>{{ $value['biology']-$value['app_biology'] }}</td>
			<td>Principals</td>
			<td>{{ $value['app_principal'] }}</td>
			<td>{{ $value['principal'] }}</td>
			<td>{{ $value['principal']-$value['app_principal'] }}</td>
			</tr>
			<tr>
			<td>Agriculture Science</td>
			<td>{{ $value['app_agriscience'] }}</td>
			<td>{{ $value['agriscience'] }}</td>
			<td>{{ $value['agriscience']-$value['app_agriscience'] }}</td>
			<td style="font-weight:bold">Total of SLPS</td>
			<td style="font-weight:bold">{{ $value['totapp_slps'] }}</td>
			<td style="font-weight:bold">{{ $value['totavi_slps'] }}</td>
			<td style="font-weight:bold">{{ $value['totavi_slps']-$value['totapp_slps'] }}</td>
			</tr> 
			@endforeach
			</table>
		</div>
	</div>
</div>
@endsection

@push('styles')
<style>
	/* .show-on-print{
 display: none; 
}

@media print {
  .show-on-print{
    display:block;
  }
  .hide-on-print{
    display: none;
  }
 } */
</style>
@endpush

@push('scripts')
<script>
function printData() {
  var divToPrint = document.getElementById("cadretbl");
  newWin = window.open("");
  newWin.document.write(divToPrint.outerHTML);
  newWin.print();
  newWin.close();
}

$('#print').on('click', function() {
  printData();
})
</script>
@endpush

		