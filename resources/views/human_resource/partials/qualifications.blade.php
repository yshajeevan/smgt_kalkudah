                <table id="coursetable">
                    <thead>
                        <tr>
                            <th style="width:40%;">Couse Name</th>
                            <th style="width:40%;">Institution</th>
                            <th style="width:20%;">Duration(Months)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($qualifications as $item)
                            <input type="hidden" name="qualifications[{{$loop->index}}][id]" value="{{$item->id}}">
                            <tr id="rowdata_{{$item->id}}">
                                <td>
                                    <input list="course_name" class="form-control form-control-sm" name="qualifications[{{$loop->index}}][course_name]" value="{{$item->course_name}}" readonly/>
                                    <datalist id="course_name">
                                        @foreach($qualifData as $qdata)
                                            <option value="{{$qdata->course_name}}">
                                        @endforeach
                                    </datalist>
                                </td>
                                <td><input list="instituion" class="form-control form-control-sm" name="qualifications[{{$loop->index}}][institution]" value="{{$item->institution}}" readonly/>
                                    <datalist id="instituion">
                                        @foreach($instituteData as $idata)
                                            <option value="{{$idata->institution}}">
                                        @endforeach
                                    </datalist>
                                </td>
                                <td><input type="text" class="form-control form-control-sm" name="qualifications[{{$loop->index}}][duration]" value="{{$item->duration}}" readonly/></td>
                                <td><button data-id="removedata_{{$item->id}}" class="btn removedata btn-danger btn-sm" style="display:none;"><i class="fa fa-trash"></i></button>
                            </tr>
                        @endforeach
                        <div class='element' id='div_1'></div>
                    </tbody>
                </table>