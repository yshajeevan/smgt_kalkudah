                <label class="card-title">Professional Qualifications</label>
                <table id="coursetable">
                    <thead>
                        <tr>
                            <th style="width:35%;">Course Name</th>
                            <th style="width:35%;">Institution</th>
                            <th style="width:20%;">Duration (Months)</th>
                            <th style="width:10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($qualifications as $item)
                            <input type="hidden" name="qualifications[{{$loop->index}}][id]" value="{{$item->id}}">
                            <tr id="rowdata_{{$item->id}}">
                                <!-- COURSE -->
                                <td>
                                    <select class="form-control form-control-sm" name="qualifications[{{$loop->index}}][course_name]" 
                                        value="{{$item->course_name}}" disabled>
                                        @foreach($qualifData as $qdata)
                                            <option value="{{$qdata->name}}"
                                                {{$item->course_name == $qdata->name ? 'selected' : ''}}>
                                                {{$qdata->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <!-- INSTITUTION -->
                                <td>
                                    <select class="form-control form-control-sm" name="qualifications[{{$loop->index}}][institution]" 
                                        value="{{$item->institution}}" disabled>
                                        @foreach($instituteData as $idata)
                                            <option value="{{$idata->name}}"
                                                {{$item->institution == $idata->name ? 'selected' : ''}}>
                                                {{$idata->name}}
                                            </option>
                                        @endforeach
                                    </select>
            
                                </td>

                                <!-- DURATION -->
                                <td>
                                    <input type="text" class="form-control form-control-sm" 
                                        name="qualifications[{{$loop->index}}][duration]" 
                                        value="{{$item->duration}}" readonly/>
                                </td>
                                <!-- DELETE BUTTON -->
                                <td>
                                    <button type="button"
                                        data-id="removedata_{{$item->id}}"
                                        class="btn removedata btn-danger btn-sm"
                                        style="display:none;">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>

                            </tr>

                        @endforeach
                        <div class='element' id='div_1'></div>
                    </tbody>
                </table>