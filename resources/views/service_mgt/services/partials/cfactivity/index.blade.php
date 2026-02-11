<div class="slugs">
    <div class="sub-head">
        CF Allocation
    </div>
<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <div class="form-col-2 text-left">
                <label for="cfund" class="control-label">CF Activity</label>
            </div>
            <div class="form-col-10">
                <select name="cfund" id="cfund" class="form-control form-control-sm showhide" {{isset($process->cfactivity) ? "disabled" : ''}}>
                    <option value="">--Select CF ID--</option>
                    @foreach ($cf as $cf)
                        <option value="{{ $cf->id}}" {{(isset($process->cfactivity) && $process->cfactivity->id == $cf->id)  ? 'selected' : ''}}>{{ $cf->cfid."-".$cf->activity."-".$cf->estimated_cost}}</option>
                    @endforeach    
                </select>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-3">
        <div class="form-group">
            <div class="form-col-2 text-left">
                <label for="estimated_cost" class="control-label">Expenditure</label>
            </div>
            <div class="form-col-3">    
                <input type="text" class="form-control form-control-sm" name="expenditure" id="expenditure" value="{{isset($process->cfactivity) ? $process->cfactivity->expenditure : ''}}" {{isset($process->cfactivity) ? "disabled" : ''}}>
            </div>
        </div>
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