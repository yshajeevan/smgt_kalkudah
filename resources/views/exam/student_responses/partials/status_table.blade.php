<table class="table table-bordered table-sm">
    <thead>
        <tr>
            <th>Student</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($students as $stu)
            @php
                $hasResponses = isset($responses[$stu->id]);
            @endphp
            <tr>
                <td>{{ $stu->name }}</td>
                <td>
                    @if($hasResponses)
                        <span class="badge bg-success">Entered</span>
                    @else
                        <span class="badge bg-warning text-dark">Pending</span>
                    @endif
                </td>
                <td>
                    @if($hasResponses)
                        <button class="btn btn-sm btn-primary" 
                                onclick="editStudent({{ $stu->id }}, {{ $examId }}, {{ $subjectId }})">Edit</button>
                        <button class="btn btn-sm btn-danger" 
                                onclick="deleteStudent({{ $stu->id }}, {{ $examId }}, {{ $subjectId }})">Delete</button>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
