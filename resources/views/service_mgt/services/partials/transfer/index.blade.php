<div class="slugs">
    <div class="sub-head">
        Transfer (Inter Zone)
    </div>
<input type="hidden" id="transfer_id" name="transfer_id" value="{{isset($process->transfer->id) ? $process->transfer->id : ''}}">
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <div class="form-col-2 text-left">
                    <label for="" class="control-label">Transfer To</label>
                </div>
                <div class="form-col-10">    
                    <select name="transfer_to" id="transfer_to" class="form-control form-control-sm" {{isset($process->transfer) ? "disabled" : ''}}>
                        <option value="">--Select Institution--</option>
                        @foreach ($institutes as $institute)
                        <option value="{{ $institute->id}}" {{(isset($process->transfer) && $process->transfer->transfer_to == $institute->id)  ? 'selected' : ''}}>{{ $institute->institute}} </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <div class="form-col-2 text-left">
                    <label for="" class="control-label">Transfer Type</label>
                </div>
                <div class="form-col-10">    
                    <select name="transfer_type" id="transfer_type" class="form-control form-control-sm" {{isset($process->transfer) ? "disabled" : ''}}>
                        <option value="" @if(isset($process->transfer) && $process->transfer->transfer_type==""){{"selected"}} @endif >--Select Tranfer Type--</option>
                        <option value="0" @if(isset($process->transfer) && $process->transfer->transfer_type==0){{"selected"}} @endif >Service Need (Permanant)</option>
                        <option value="1" @if(isset($process->transfer) && $process->transfer->transfer_type==1){{"selected"}} @endif >Service Need(Temprory)</option>
                        <option value="2" @if(isset($process->transfer) && $process->transfer->transfer_type==2){{"selected"}} @endif >Service Need(2:3 Days)</option>
                        <option value="3" @if(isset($process->transfer) && $process->transfer->transfer_type==3){{"selected"}} @endif >Annual</option>
                        <option value="4" @if(isset($process->transfer) && $process->transfer->transfer_type==4){{"selected"}} @endif >Temprory(Request)</option>
                        <option value="5" @if(isset($process->transfer) && $process->transfer->transfer_type==5){{"selected"}} @endif >Temprory(Maternity)</option>
                        <option value="6" @if(isset($process->transfer) && $process->transfer->transfer_type==6){{"selected"}} @endif >Permanent(Request)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3">
            <div class="form-group">
                <div class="form-col-2 text-left">
                    <label for="" class="control-label">Letter Date</label>
                </div>
                <div class="form-col-3">    
                    <input type="date" class="form-control form-control-sm date" name="letter_date" id="letter_date" value="{{isset($process->transfer) ? $process->transfer->letter_date : now()->format('Y-m-d')}}" {{isset($process->transfer) ? "disabled" : ''}} required>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3">
            <div class="form-group">
                <div class="form-col-2 text-left">
                    <label for="" class="control-label">Effective From</label>
                </div>
                <div class="form-col-3">    
                    <input type="date" class="form-control form-control-sm" name="effect_from" id="effect_from" value="{{isset($process->transfer) ? $process->transfer->effect_from : ''}}" {{isset($process->transfer) ? "disabled" : ''}}>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            @if(isset($process->transfer))
                <a href="{{route('process.print_transfer',$process->transfer->id)}}" target="_blank" class="btn btn-primary btn-sm float-left" data-toggle="tooltip" data-placement="bottom" title="Add User"><i class="fa fa-print"></i> Print Letter</a>
            @endif
        </div>
    </div>
</div>

<style>
.slugs{
    position:relative;
    padding:20px;
    box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;
}
.sub-head{
    border:1px solid;
    position:absolute;
    border-radius: 5px;
    padding: 5px;
    left:20px;
    top:-10px;
    z-index:99;
    color: white;
    background-color: green;
}
</style>
