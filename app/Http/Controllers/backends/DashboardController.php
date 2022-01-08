<?php
namespace App\Http\Controllers\backends;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Cates;
use App\Models\Action;
use App\Models\Device;
use App\Models\Department;
use App\Models\Equipment;

class DashboardController extends Controller {

    public function index(Request $request){
        $user = Auth::user();
        $statuses = get_statusEquipments();
        $depart_id = isset($request->depart_id) ? $request->depart_id : '';
        $status = isset($request->status) && in_array($request->status, array_keys($statuses)) ? $request->status : '';
        $list_department = Department::select('id', 'title')->get();
        $title_type = $title_types = '';
        $equipments = Equipment::query();
        $equipment_stt = Equipment::query();
        $equipment_types = Equipment::query();
        
        if($user->can('dashboard.read')){
            $equip_types = Device::get(['id', 'title'])->toArray();
            $equip_types = array_column($equip_types, 'title','id');
            $type = isset($request->type) && in_array($request->type, array_keys($equip_types)) ? $request->type : '';
            if($depart_id != '') {
                $department = Department::where('id', $depart_id)->first();
                $equipments = $equipments->where('department_id', $depart_id);
                $equipment_stt = $equipment_stt->where('department_id', $depart_id);
                $equipment_types = $equipment_types->where('department_id', $depart_id);
                $title = $department->title;
            }else $title = __('tất cả các khoa');

            $equipments = $equipments->groupBy('status')->get(['status', DB::raw('SUM(amount) as amount')])->toArray();
            $equipments = array_column($equipments, 'amount','status');

            if($type != '') {
                $equipment_stt = $equipment_stt->where('devices_id', $type);
                $title_type = $equip_types[$type];
            }else $title_type = __('tất cả loại thiết bị');
            $equipment_stt = $equipment_stt->groupBy('status')->get(['status', DB::raw('SUM(amount) as amount')])->toArray();
            $equipment_stt = array_column($equipment_stt, 'amount','status');
            foreach($statuses as $key => $value) {
                $equipments_graph[$key] = isset($equipments[$key]) ? $equipments[$key] : 0;
                $equipment_stt_graph[$key] = isset($equipment_stt[$key]) ? $equipment_stt[$key] : 0;
            }
            
            if($status != '') {
                $equipment_types = $equipment_types->where('status', $status);
                $title_stt = $statuses[$status];
            }else $title_stt = __('tất cả trạng thái');
            $equipment_types = $equipment_types->groupBy('devices_id')->get(['devices_id', DB::raw('SUM(amount) as amount')])->toArray();
            $equipment_types = array_column($equipment_types, 'amount','devices_id');
            foreach($equip_types as $key => $value) {
                $equipments_graph_type[$key] = isset($equipment_types[$key]) ? $equipment_types[$key] : 0;
            }
            // Tổng thiết bị theo khoa
            $equip_depart = Department::pluck('title','id')->toArray();
            $equipment_depart = Equipment::query();
            $equipment_depart = $equipment_depart->groupBy('department_id')->get(['department_id', DB::raw('SUM(amount) as amount')])->toArray();
            $equipment_depart = array_column($equipment_depart, 'amount','department_id');

            // Tổng thiết bị đang báo hỏng theo khoa
            $eqdepart_wasbroken = Department::query();
            $eqdepart_wasbroken = $eqdepart_wasbroken->withCount(['department_equipment'=>function ($query) {
                                    $query->where('status', 'was_broken');}]
                                )->pluck('department_equipment_count','id')->toArray();
           
            // Tổng thiết bị đang sửa chữa theo khoa
            $eqdepart_corrected = Department::query();
            $eqdepart_corrected = $eqdepart_corrected->withCount(['department_equipment'=>function ($query) {
                                    $query->where('status', 'corrected');}]
                                )->pluck('department_equipment_count','id')->toArray();
            
        }else{
            $eq_dev= $user->user_department->department_equipment;
            $arr_device = array();
            foreach ($eq_dev as $item) {
                $arr_device[]=$item->equipment_device;
            }
            $equip_types = array_column($arr_device, 'title','id');
            $type = isset($request->type) && in_array($request->type, array_keys($equip_types)) ? $request->type : '';

            $equip_depart = Department::where('id',$user->department_id)->pluck('title','id')->toArray();
            $department_id = $user->department_id;
            $depart_query = function ($query) use ($department_id) {
                return $query->select('departments.id','departments.title')->where('departments.id',$department_id);  
            };
            
            $title = $user->user_department->title;
            $equipments = $equipments->whereHas('equipment_department', $depart_query)->groupBy('status')->get(['status', DB::raw('SUM(amount) as amount')])->toArray();
            $equipments = array_column($equipments, 'amount','status');

            if($type != '') {
                $equipment_stt = $equipment_stt->where('devices_id', $type);
                $title_type = $equip_types[$type];
            }else $title_type = __('tất cả loại thiết bị');
            $equipment_stt = $equipment_stt->whereHas('equipment_department', $depart_query)->groupBy('status')->get(['status', DB::raw('SUM(amount) as amount')])->toArray();
            $equipment_stt = array_column($equipment_stt, 'amount','status');
            foreach($statuses as $key => $value) {
                $equipments_graph[$key] = isset($equipments[$key]) ? $equipments[$key] : 0;
                $equipment_stt_graph[$key] = isset($equipment_stt[$key]) ? $equipment_stt[$key] : 0;
            }
            
            if($status != '') {
                $equipment_types = $equipment_types->where('status', $status);
                $title_stt = $statuses[$status];
            }else $title_stt = __('tất cả trạng thái');
            $equipment_types = $equipment_types->whereHas('equipment_department', $depart_query)->groupBy('devices_id')->get(['devices_id', DB::raw('SUM(amount) as amount')])->toArray();
            $equipment_types = array_column($equipment_types, 'amount','devices_id');
            foreach($equip_types as $key => $value) {
                $equipments_graph_type[$key] = isset($equipment_types[$key]) ? $equipment_types[$key] : 0;
            }
            // Tổng thiết bị theo khoa
            $equipment_depart = Equipment::query();
            $equipment_depart = $equipment_depart->whereHas('equipment_department', $depart_query)->groupBy('department_id')->get(['department_id', DB::raw('SUM(amount) as amount')])->toArray();
            $equipment_depart = array_column($equipment_depart, 'amount','department_id');

             // Tổng thiết bị đang báo hỏng theo khoa
            $eqdepart_wasbroken = Department::query();
            $eqdepart_wasbroken = $eqdepart_wasbroken->where('id',$user->department_id)->withCount(['department_equipment'=>function ($query) {
                                    $query->where('status', 'was_broken');}]
                                )->pluck('department_equipment_count','id')->toArray();
           
            // Tổng thiết bị đang sửa chữa theo khoa
            $eqdepart_corrected = Department::query();
            $eqdepart_corrected = $eqdepart_corrected->where('id',$user->department_id)->withCount(['department_equipment'=>function ($query) {
                                    $query->where('status', 'corrected');}]
                                )->pluck('department_equipment_count','id')->toArray();
        }
        // action
        $repairs = Action::eqrepair()->latest()->take(5)->get();
        $maintenances = Action::periodic()->latest()->take(5)->get();
        $accreditations = Action::accre()->latest()->take(5)->get();
        $guarantees = Action::guarantee()->latest()->take(5)->get();
        $data = [
            'equipments'            => $equipments,
            'title'                 => $title,
            'title_type'            => $title_type,
            'title_stt'             => $title_stt,
            'logo'                  => get_option('logo'),
            'depart_id'             => $depart_id,
            'status'                => $status,
            'type'                  => $type,
            'statuses'              => $statuses,
            'equip_types'           => $equip_types,
            'equipments_graph'      => $equipments_graph,
            'equipment_stt_graph'   => $equipment_stt_graph,
            'equipments_graph_type' => $equipments_graph_type,
            'repairs'               => $repairs,
            'maintenances'          => $maintenances,
            'accreditations'        => $accreditations,
            'guarantees'            => $guarantees,
            'equipment_depart'      => $equipment_depart,
            'equip_depart'          => $equip_depart,
            'eqdepart_wasbroken'    => $eqdepart_wasbroken,
            'eqdepart_corrected'    => $eqdepart_corrected,
            'list_department'       => $list_department,
            'user'                  => $user,
        ];
        // dd(generate_random_color(3));
        return view('backends.dashboard', $data);
    }
}