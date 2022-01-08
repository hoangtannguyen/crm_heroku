<?php
namespace App\Http\Controllers\backends;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use App\Models\SupplieBallot;
use App\Models\Provider;
use App\Models\Equipment;
use App\Models\Eqsupplie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon; 
use Illuminate\Support\Str;
class SupplieBallotController extends Controller {
    public function index(Request  $request){
        $user = Auth::user();
            $keyword = isset($request->key) ? $request->key : '';
            $departments_key = isset($request->department_key) ? $request->department_key : '';
            $department_name = Department::select('id','title')->get();
            $supplieBallots = SupplieBallot::query();
            if($keyword != ''){
                $supplieBallots = $supplieBallots->whereHas('departments', function ($query) use ($keyword){
                $query->select('id','title')->where('departments.title','like','%'.$keyword.'%');
                    })->orWhereHas('providers', function ($q) use ($keyword){
                $q->select('id','title')->where('providers.title','like','%'.$keyword.'%');
                    })->orWhereHas('users', function ($q) use ($keyword){
                        $q->select('id','name')->where('users.name','like','%'.$keyword.'%');
                    });
                }
            if($departments_key != ''){
                $supplieBallots = $supplieBallots->where('department_id',$departments_key);
            }
            $supplieBallots = $supplieBallots->orderBy('created_at', 'desc')->paginate(15);
            return view('backends.supplieballot.list',compact('supplieBallots','keyword','department_name','departments_key'));
    }
    public function create(){
        $user= Auth::user();
        //if($user->can('create', SupplieBallot::class)){ 
            $departments = Department::select('id','title')->get();
            $eqsupplies = Eqsupplie::select('id','title','code','serial','model')->get();
            $providers = Provider::select('id','title','type')->provider()->get();
            $cur_time = Carbon::now()->format('Y-m-d');
            return view('backends.supplieballot.create',compact('departments','providers','cur_time','eqsupplies'));
        //}else{abort(403);}
    }
    public function store(Request $request){
        $rules = [
            'department_id'=>'required',
            'provider_id'=>'required',
        ];
        $messages = [
            'department_id.required'=>'Vui lòng chọn khoa phòng',
            'provider_id.required'=>'Vui lòng chọn nhà cung cấp',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()):
            return redirect()->back()->withErrors($validator)->withInput();
        else:
        $atribute = $request->all();
        // $array = array();
        // foreach($request->data_id as $key => $value){
        //     $array[] = array('id' => $value,'amount' => $request->amount[$key]);
        // }
        // $atribute['equi_array'] = json_encode($array);
        $atribute['ballot'] = Carbon::now()->format('YmdHis');
        $atribute['status'] = "pendding";
        $ballots = SupplieBallot::create($atribute);
        foreach($request->data_id as $key => $items){
            $ballots->supplies()->attach($items,['amount' => $request->amount[$key] ,'unit_price' =>  $request->unit_price[$key] ]);
        }
        return redirect()->route('supplieBallot.index')->with('success','Thêm thành công');
        endif;
    }
    public function edit($id){
        $user = Auth::user();
        $ballots = SupplieBallot::findOrFail($id);
        //if($user->can('update', $ballots)) {
            $departments = Department::select('id','title')->get();
            $eqsupplies = Eqsupplie::select('id','title','code','serial','model')->get();
            $providers = Provider::select('id','title','type')->provider()->get();
            $cur_time = Carbon::now()->format('Y-m-d');
            return view('backends.supplieballot.edit',compact('departments','providers','cur_time','eqsupplies','ballots'));
        //}else{abort(403);}
    }
    public function update(Request  $request , $id){
        $ballots = SupplieBallot::findOrFail($id);
        $atribute = $request->all();
        $ballots->update($atribute);
        $array = array();
        foreach($request->data_id as $key => $value){
            $array[] = array('supplie_id' => $value,'amount' => $request->amount[$key],'unit_price' => $request->unit_price[$key] );
        }
        $ballots->supplies()->sync($array);
        if($ballots){
            return redirect()->route('supplieBallot.edit',$id)->with('success','Cập nhật thành công');
        }else{
            return redirect()->route('supplieBallot.edit',$id)->with('error','Cập nhật không thành công');
        }
    }
    public function updateSuccess(Request  $request , $id){
        $ballots = SupplieBallot::findOrFail($id);
        $ballots->status = $request->status;
        $ballots->save();
        if($ballots){
            return redirect()->back()->with('success','Cập nhật thành công');
        }else{
            return redirect()->back()->with('error','Cập nhật không thành công');
        }
    }
    public function destroy($id){
        $user = Auth::user();
        $ballots = SupplieBallot::findOrFail($id);
        //if ($user->can('delete', $ballots)) {
            $ballots->delete();
            $ballots->supplies()->detach();
            return redirect()->route('supplieBallot.index')->with('success','Xóa thành công');
        //}else{abort(403);}
    }
    public function showEqui(Request $request ){
        $ballots = SupplieBallot::where('id', $request->id)->first();
        $html_ballot = '';
        $html_ballot = $ballots->ballot;
        if($ballots){
            $html = '';
            foreach($ballots->supplies as $key => $items){
                $html .=  '<tr>';
                        $html .= '<td>'. ++$key .'</td>';
                        $html .= '<td>'. $items->code.'</td>';
                        $html .= '<td>'. $items->title.'</td>';
                        $html .= '<td>'. $items->model.'</td>';
                        $html .= '<td>'. $items->serial.'</td>';
                        $html .= '<td>'. $items->pivot->amount.'</td>';
                $html .=  '</tr>';
            }
        }
        return response()->json([
            'check' => 'true',
            'html' => $html,
            'html_ballot' => $html_ballot,
        ]);
    }
    public function table(Request $request ){
        $check = false;
        $eqsupplies = Eqsupplie::where('id',$request->id)->first();
        if($eqsupplies) {
            $html = '';
                $html .=  '<tr data-id="'.$eqsupplies->id.'">';
                    $html .= 
                    '<td>
                        <input name="data_id[]" type="hidden" class="hidden" value="'.$eqsupplies->id.'">
                        <a class="remove-ballot text-danger"><i class="fas fa-times"></i></a>
                    </td>';
                    $html .= '<td>'. $eqsupplies->title.'</td>';
                    $html .= '<td>'. $eqsupplies->model.'</td>';
                    $html .= '<td>'. $eqsupplies->serial.'</td>';
                    $html .= 
                    '<td><div class="quanlity">
                            <input class="quanlity-z" name="amount[]" type="number" min="0" class="form-control" value="1">
                        </div>
                    </td>';
                    $html .= 
                    '<td><div class="currency">
                            <input class="currency-z" name="unit_price[]" type="number" min="0" class="form-control" value="'.$eqsupplies->import_price.'">
                        </div>
                    </td>';
                    $html .= '<td class="total" name="price[]">'.$eqsupplies->import_price.'</td>';
                $html .=  '</tr>';
            $check = true;
        };
        return response()->json([
            'check' => 'true',
            'html' => $html,
        ]);
    }
}