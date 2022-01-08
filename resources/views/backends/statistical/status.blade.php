<div class="table-responsive">
   <table class="table table-striped table-bordered" role="table">
      <thead class="thead">
         <tr class="text-center">
            <th>{{ __('STT') }}</th>
            <th>{{ __('Trạng thái') }}</th>
            <th>{{ __('Mã TB') }}</th>
            <th>{{ __('Tên TB') }}</th>
            <th>{{ __('DVT') }}</th>
            <th>{{ __('Model') }}</th>
            <th>{{ __('S/N') }}</th>
            <th>{{ __('Hãng SX') }}</th>
            <th>{{ __('Nước SX') }}</th>
            <th>{{ __('Năm SX') }}</th>
            <th>{{ __('Năm SD') }}</th>
            <th>{{ __('Đơn giá') }}</th>
            <th>{{ __('Số lượng') }}</th>
            <th>{{ __('Thành tiền') }}</th>
            
         </tr>
      </thead>
      <tbody class="tbody">
         @if(!$equipments->isEmpty())
            @php
               $sum = 0;
            @endphp
            @foreach($equipments as $key => $equipment)
               @php $money = $equipment->amount * $equipment->import_price; @endphp
               <tr class="text-center">
                  <td>{{ ++$key}}</td>
                  <td>{{ isset($statusEquipments[$equipment->status]) ? $statusEquipments[$equipment->status] :'-' }}</td>
                  <td>{{ $equipment->code != null ? $equipment->code : '-' }}</td>
                  <td>{{ $equipment->title != null ? $equipment->title : '-' }}</td>
                  <td>{{ isset($equipment->equipment_unit) ? $equipment->equipment_unit->title : '-' }}</td>
                  <td>{{ $equipment->model != null ? $equipment->model : '-'}}</td>
                  <td>{{ $equipment->serial != null ? $equipment->serial : '-' }}</td>
                  <td>{{ $equipment->manufacturer != null ? $equipment->manufacturer : '-' }}</td>
                  <td>{{ $equipment->origin != null ? $equipment->origin : '-' }}</td>
                  <td>{{ $equipment->year_manufacture != null ? $equipment->year_manufacture : '-' }}</td>
                  <td>{{ $equipment->year_use  != null ? $equipment->year_use : '-' }}</td>
                  <td>{!! $equipment->import_price != null ? convert_currency($equipment->import_price) : '0' !!}</td>
                  <td>{{ $equipment->amount != null ? $equipment->amount : '0' }}</td>
                  <td>{!! convert_currency($money) !!}</td> 
               </tr>
               @php
                  $sum = $sum + $money; 
               @endphp
            @endforeach 
               <tr>
                  <td colspan="11"></td>
                  <td>{{ __('Tổng') }}</td>
                  <td>{{ $equipments->sum('amount') }}</td>
                  <td>{!! convert_currency($sum) !!}</td>
               </tr>
         @else
         <tr>
            <td colspan="13">{{ __('No items!') }}</td>
         </tr>
         @endif
      </tbody>
   </table>
</div>