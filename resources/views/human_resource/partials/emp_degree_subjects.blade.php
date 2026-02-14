<label class="card-title">Degree Subjects</label>
<table id="degreesubjecttable">
    <thead>
        <tr>
            <th style="width:90%;">Subject Name</th>
            <th style="width:10%;"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($empDegreeSubjects as $item)
            <input type="hidden" name="degree_subjects[{{$loop->index}}][id]" value="{{$item->id}}">
            
            <tr id="rowdata_{{$item->id}}">
                <td>
                    <select class="form-control form-control-sm" disabled>
                        @foreach($degreesubs as $subject)
                            <option value="{{$subject->name}}"
                                {{$item->subject_name == $subject->name ? 'selected' : ''}}>
                                {{$subject->name}}
                            </option>
                        @endforeach
                    </select>

                    {{-- Hidden input because disabled select will not submit --}}
                    <input type="hidden"
                        name="degree_subjects[{{$loop->index}}][subject_name]"
                        value="{{$item->subject_name}}">
                </td>


                <td>
                    <button data-id="removedata_{{$item->id}}"
                        class="btn removedata btn-danger btn-sm"
                        style="display:none;">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        @endforeach

        <tr class='element' id='div_degree_1'></tr>
    </tbody>
</table>
