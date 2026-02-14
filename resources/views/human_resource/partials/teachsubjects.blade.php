<label class="card-title">Teaching Subjects</label>
<table id="subjecttable">
    <thead>
        <tr>
            <th style="width:40%;">Teaching Subject</th>
            <th style="width:40%;">Periods (Per week)</th>
        </tr>
    </thead>
    <tbody>
    @foreach($teachsubjects as $item)
        <input type="hidden" name="teachsubjects[{{$loop->index}}][id]" value="{{$item->id}}">
        <tr id="rowdata1_{{$item->id}}">
            <td>
                <select name="teachsubjects[{{$loop->index}}][teachsubject_id]" class="form-control selectdrp" disabled >
                    <option disabled selected value> -- Select Teaching Subject -- </option>
                    @foreach($cadresubs as $cadresub)
                    <option value="{{ $cadresub->id}}" {{(isset($item) && $item->cadresubject_id == $cadresub->id)  ? 'selected' : ''}}>{{$cadresub->cadre}}</option>
                    @endforeach
                </select> 
            </td>
            <td><input type="text" class="form-control form-control-sm" name="teachsubjects[{{$loop->index}}][periods]" value="{{$item->periods}}" readonly/></td>
            <td><button data-id="removedata1_{{$item->id}}" class="btn removedata1 btn-danger btn-sm" style="display:none;"><i class="fa fa-trash"></i></button></td>
        </tr>
    @endforeach
    <div class='element1' id='div_1'></div>
    </tbody>
</table>