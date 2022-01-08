<?php

namespace App\Http\Controllers\backends;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipment;
use App\Models\Provider;
use App\Models\User;
use App\Models\Department;
use App\Models\Liquidation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportEquipmentWaitingLiquidation;
use App\Repositories\Liquidation\LiquidationRepository;
use App\Repositories\Liquidation\LiquidationRepositoryInterface;
use App\Notifications\LiquidationNotifications;
use App\Notifications\PublicLiquiNotifications;

class LiquidationController extends Controller {
    public $liquidation;
    public function __construct(LiquidationRepositoryInterface $liquidation) {
        $this->liquidation = $liquidation;
    }
	public function index(Request $request) {
	 	$eqliquis = Equipment::query();
	 	$keyword = isset($request->key) ? $request->key : '';
	 	$department_id = isset($request->department_id) ? $request->department_id : '';
	 	$departments = Department::select('id','title')->get();
	 	if($keyword != ''){
            $eqliquis = $eqliquis->where(function ($query) use ($keyword) {
            $query->where('title','like','%'.$keyword.'%')
                ->orWhere('code','like','%'.$keyword.'%')
                ->orWhere('model','like','%'.$keyword.'%')
                ->orWhere('serial','like','%'.$keyword.'%')
                ->orWhere('manufacturer','like','%'.$keyword.'%');
            });
        }
        if($department_id != ''){
            $eqliquis = $eqliquis->where('department_id',$department_id);
        }
        $eqliquis = $eqliquis->whereIn('status',['inactive','liquidated'])->where('amount','>', 0)->orderBy('created_at', 'desc')->paginate(10);
	 	$data=[
	 		'eqliquis'=>$eqliquis,
	 		'keyword'=>$keyword,
	 		'department_id'=>$department_id,
	 		'departments'=>$departments,
	 	];
		return view('backends.liquidations.list',$data);
	}
    public function store(Request $request, $equip_id){
        $rules = [
            'amount'=>'required|min:0',
            'reason'=>'required',
        ];
        $messages = [
            'reason.required'=>'Vui lòng nhập lý do!',
            'amount.required'=>'Vui lòng nhập số lượng !',
            'amount.min'=>'Số lượng không được nhỏ hơn 0!',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()):
            return redirect()->route('eqliquis.index')->withErrors($validator)->withInput();
        else:
            $equipment = Equipment::findOrFail($equip_id);
            if($request->amount <= $equipment->amount):
                $request['user_id']= Auth::id();
                $request['equipment_id']= $equip_id;
                $atribute = $request->all();
                $liquidation = Liquidation::create($atribute);
                //notify
                $array_user = User::with('roles')->pluck('id')->toArray();
                    if($array_user != null){
                        foreach ($array_user as $key => $value) {
                            $user = User::findOrFail($value);
                            $user->notify(new LiquidationNotifications($liquidation));
                        }
                }
                if($equipment->wasChanged('status')){
                    $equipment['status'] = 'liquidated';
                    $equipment->update($request->only('status'));
                }
                return redirect()->route('eqliquis.index')->with('success','Tạo phiếu đề nghị thanh lý thành công');
            else:
                return redirect()->route('eqliquis.index')->with('success','Tạo phiếu đề nghị thanh lý thất bại');
            endif;
        endif;
    }
    public function listLiqui(Request $request, $equip_id) {
        $user = Auth::user();
        if($user->can('liquidation.read')){
            $equipment = Equipment::findOrFail($equip_id);
            $data = [
                'equipment'         => $equipment,
                'liquidations'      => $equipment->liquidations->sortByDesc('created_at')->simplePaginate(10),
            ];
            return view('backends.liquidations.list-lq',$data);
        }else{
            abort(403);
        }
    }
    public function update(Request $request, $equip_id, $liqui_id){
        $equipments = Equipment::findOrFail($equip_id);
        $liquidations = Liquidation::findOrFail($liqui_id);
        $liquidations['status']  = "liquidated";
        $liquidations['person_up']  = Auth::id();
        $liquidations->save();
        if($liquidations->status == 'liquidated'){
            //notify
            $user= User::where('id',$liquidations->user_id)->first();
            $roles = [$user->roles->first()->name];
            $array_user = User::role($roles)->pluck('id')->toArray();
                if($array_user != null){
                    foreach ($array_user as $key => $value) {
                        $user = User::findOrFail($value);
                        $user->notify(new PublicLiquiNotifications($liquidations));
                    }
            }
            
        }
        if($liquidations){
            $equipments['amount']  = $equipments->amount - $liquidations->amount;
            $equipments->save();
            if($liquidations->wasChanged())
                return redirect()->back()->with('success','Cập nhật thành công');
            else 
                return redirect()->back();
        }else{
            return redirect()->back()->with('error','Cập nhật không thành công');
        }
    }
    public function destroy($equip_id, $id){
        $user = Auth::user();
        $liquidation = Liquidation::findOrFail($id);
        if ($user->can('delete', $liquidation)) {
            $liquidation->delete();
            \DB::table('notifications')
            ->where('type','App\Notifications\LiquidationNotifications')
            ->orWhere('type','App\Notifications\PublicLiquiNotifications')
            ->where('data->id',intval($id))
            ->delete();
            return redirect()->route('eqliquis.listLiqui',['equip_id'=>$equip_id])->with('success','Xóa thành công');
        }else{
          abort(403);
        }
        // $this->liquidation->destroy($id);
        // return redirect()->route('eqliquis.listLiqui',['equip_id'=>$equip_id])->with('success','Xóa thành công');
    }
    public function exportLiquidation(Request $request) {
        return Excel::download(new ExportEquipmentWaitingLiquidation, 'Những thiết bị chờ thanh lý ' . Carbon::now()->format('d-m-Y') . '.xlsx');
    }
	
}
