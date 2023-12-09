<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\BuilderEntityController;
use App\Http\Controllers\Controller;
use App\Models\AmoCRMLead;
use App\Models\PATIENTS;
use App\Models\PLANNING;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadBuilderController extends BuilderEntityController
{
    /**
     * @param int $id
     * @return array
     * Description: get Lead row from the DB
     */
    public static function getRow(int $id) : array{
//        $lead = self::getColumns($id);
        $lead = AmoCRMLead::find(1)->toArray();
        if ($lead){
            return $lead;
        }
        return [];
    }

    protected static function getColumns(int $leadID): array
    {
        $vrachSpecializacii = DB::select('act.label,spec.LABEL from MEDECINS_CARD mc
join ZDR_ACTIVITIES act on act.ZDR_ACTIVITIES_ID=mc.ZDR_ACTIVITIES_ID
join zdr_specialisation spec on spec.ZDR_SPECIALISATION_ID=mc.ZDR_SPECIALISATION_ID
where mc.MEDECINS_CARD_ID =dbo.zdr_GetMedcardIdPlanning('.$leadID.')');
        $vrachFIO = DB::select("MEDECINS.NOM+" . "+MEDECINS.PRENOM,
PL_EXAM.NAME,
isnull(PLANNING.PATIENT_ARRIVEE,0)
PLANNING.DATE_CONS+dbo.pl_fPlanTimeToTime(PLANNING.HEURE), --дата приема
PL_SUBJ.NAME
FROM PLANNING
JOIN PL_SUBJ ON PL_SUBJ.PL_SUBJ_ID=PLANNING.PL_SUBJ_ID
JOIN MEDECINS ON PL_SUBJ.MEDECINS_ID=MEDECINS.MEDECINS_ID
JOIN PL_EXAM ON PL_EXAM.PL_EXAM_ID=PLANNING.PL_EXAM_ID
WHERE PLANNING.PLANNING_ID=".$leadID);
        $bill = DB::select('b.FM_BILL_ID,b.BILL_DATE,sum(bd.price_to_pay) [sum] from planning pl with (nolock)
join pl_subj s with (nolock) on s.pl_subj_id=pl.pl_subj_id
join medecins med with (nolock) on med.medecins_id=s.medecins_id or s.medecins_id in (1144,1145,1146,1147,1148,11774) --кабинеты
join patients p with (nolock) on p.PATIENTS_ID =pl.PATIENTS_ID
left join fm_bill b with (nolock) on pl.PATIENTS_ID =b.PATIENTS_ID and pl.DATE_CONS =b.BILL_DATE and isnull(pl.STATUS,0)=0 and b.MEDECINS1_ID=med.MEDECINS_ID
join FM_BILLDET bd with (nolock) on bd.FM_BILL_ID=b.FM_BILL_ID
where pl.PLANNING_ID='.$leadID.'
group by b.FM_BILL_ID,b.BILL_DATE');
        $patientsID = PLANNING::all('PATIENTS_ID')->where('PLANNING_ID', '=', $leadID)->first;

        return [
            'direction'   => $vrachSpecializacii['label'],
            'patID'    => $patientsID,
            'specDoc'   => $vrachSpecializacii['LABEL'],
            'fioDoc'  => $vrachFIO,
            'billID'  => $bill['FM_BILL_ID'],
            'billSum'  => $bill['sum'],
        ];
    }
}
