<form id="responseForm" method="POST" action="{{ route('student_responses.store') }}">
    @csrf
    <input type="hidden" name="student_id" value="{{ $studentId }}">

    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>#</th>
                <th>Question</th>
                <th>Type</th>
                <th>Response</th>
            </tr>
        </thead>
        <tbody>
            @foreach($questions as $q)
                <tr>
                    <td>{{ $q->id }}</td>
                    <td>{{ $q->question_no ?? 'Q'.$q->id }}</td>
                    <td>{{ $q->type }}</td>
                    <td>
                        @if($q->type == 'MCQ')
                            <select name="responses[{{ $q->id }}][is_correct]" class="form-select form-select-sm">
                                <option value="" @if(!isset($responses[$q->id])) selected @endif>--</option>
                                <option value="1" @if(isset($responses[$q->id]) && $responses[$q->id]->is_correct) selected @endif>Correct</option>
                                <option value="0" @if(isset($responses[$q->id]) && $responses[$q->id]->is_correct===0) selected @endif>Wrong</option>
                            </select>
                        @else
                            <input type="number" 
                                   name="responses[{{ $q->id }}][obtained_marks]" 
                                   class="form-control form-control-sm" 
                                   max="{{ $q->max_marks }}" 
                                   value="{{ $responses[$q->id]->obtained_marks ?? '' }}">
                            <small class="text-muted">/ {{ $q->max_marks }}</small>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <button class="btn btn-primary mt-2">Save Responses</button>
</form>
