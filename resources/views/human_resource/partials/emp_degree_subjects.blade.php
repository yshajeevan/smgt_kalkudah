
                        @foreach($empDegreeSubjects as $item)
                            <input type="hidden" name="degree_subjects[{{$loop->index}}][id]" value="{{$item->id}}">
                            
                            <tr id="rowdata_{{$item->id}}">
                                <td>
                                    <select class="form-control form-control-sm" name="degree_subjects[{{$loop->index}}][subject_name]"
                                        value="{{$item->subject_name}}" disabled>
                                        @foreach($degreesubs as $subject)
                                            <option value="{{$subject->name}}"
                                                {{$item->subject_name == $subject->name ? 'selected' : ''}}>
                                                {{$subject->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>


                                <td>
                                    <button data-id="removedata_{{$item->id}}"
                                        class="btn removedegreedata btn-danger btn-sm"
                                        style="display:none;">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
 